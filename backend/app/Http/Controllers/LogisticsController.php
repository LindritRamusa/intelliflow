<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LogisticsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Shipment::where('organization_id', $request->user()->activeOrgId())->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->input('priority'));
        }

        if ($request->filled('search')) {
            $term = $request->input('search');
            $query->where(function ($q) use ($term) {
                $q->where('name', 'ilike', "%{$term}%")
                  ->orWhere('origin', 'ilike', "%{$term}%")
                  ->orWhere('destination', 'ilike', "%{$term}%");
            });
        }

        $shipments = $query->paginate(20);

        return response()->json([
            'data' => $shipments->map(fn (Shipment $s) => $this->format($s)),
            'meta' => [
                'total' => $shipments->total(),
                'page' => $shipments->currentPage(),
                'perPage' => $shipments->perPage(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'origin' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'cargo_type' => 'nullable|string|max:100',
            'cargo_weight_kg' => 'nullable|numeric|min:0',
            'priority' => 'in:low,normal,high,urgent',
            'driver_name' => 'nullable|string|max:255',
            'vehicle_id' => 'nullable|string|max:100',
            'estimated_arrival' => 'nullable|date',
            'distance_km' => 'nullable|numeric|min:0',
            'cost_estimate' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $shipment = Shipment::create([
            ...$validated,
            'organization_id' => $request->user()->activeOrgId(),
            'status' => 'pending',
        ]);

        return response()->json(['data' => $this->format($shipment), 'message' => 'Shipment created'], 201);
    }

    public function show(Shipment $shipment): JsonResponse
    {
        $this->authorizeOrg($shipment);

        return response()->json(['data' => $this->format($shipment)]);
    }

    public function update(Request $request, Shipment $shipment): JsonResponse
    {
        $this->authorizeOrg($shipment);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'origin' => 'sometimes|string|max:255',
            'destination' => 'sometimes|string|max:255',
            'cargo_type' => 'nullable|string',
            'cargo_weight_kg' => 'nullable|numeric',
            'priority' => 'in:low,normal,high,urgent',
            'status' => 'in:pending,in_transit,delivered,cancelled',
            'driver_name' => 'nullable|string',
            'vehicle_id' => 'nullable|string',
            'estimated_arrival' => 'nullable|date',
            'actual_arrival' => 'nullable|date',
            'distance_km' => 'nullable|numeric',
            'cost_estimate' => 'nullable|numeric',
            'notes' => 'nullable|string',
        ]);

        $wasDelivered = $shipment->status !== 'delivered' && ($validated['status'] ?? '') === 'delivered';

        $shipment->update($validated);

        if ($wasDelivered) {
            NotificationService::create(
                auth()->id(),
                auth()->user()?->activeOrgId(),
                'Shipment Delivered',
                "Shipment \"{$shipment->name}\" has been delivered to {$shipment->destination}.",
                'success',
                ['type' => 'logistics']
            );
        }

        return response()->json(['data' => $this->format($shipment), 'message' => 'Shipment updated']);
    }

    public function destroy(Shipment $shipment): JsonResponse
    {
        $this->authorizeOrg($shipment);
        $shipment->delete();

        return response()->json(['message' => 'Shipment deleted']);
    }

    public function saveOptimization(Request $request, Shipment $shipment): JsonResponse
    {
        $this->authorizeOrg($shipment);

        $validated = $request->validate([
            'optimized_route' => 'required|array',
            'ai_notes' => 'nullable|string',
            'distance_km' => 'nullable|numeric',
            'cost_estimate' => 'nullable|numeric',
        ]);

        $shipment->update($validated);

        return response()->json(['data' => $this->format($shipment), 'message' => 'Route optimization saved']);
    }

    public function stats(Request $request): JsonResponse
    {
        $orgId = $request->user()->activeOrgId();

        $byStatus = Shipment::where('organization_id', $orgId)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $byPriority = Shipment::where('organization_id', $orgId)
            ->selectRaw('priority, COUNT(*) as count')
            ->groupBy('priority')
            ->pluck('count', 'priority');

        return response()->json([
            'data' => [
                'total' => Shipment::where('organization_id', $orgId)->count(),
                'inTransit' => $byStatus['in_transit'] ?? 0,
                'delivered' => $byStatus['delivered'] ?? 0,
                'pending' => $byStatus['pending'] ?? 0,
                'urgent' => ($byPriority['urgent'] ?? 0) + ($byPriority['high'] ?? 0),
                'avgCost' => (float) Shipment::where('organization_id', $orgId)->whereNotNull('cost_estimate')->avg('cost_estimate'),
                'byStatus' => $byStatus,
                'byPriority' => $byPriority,
            ],
        ]);
    }

    private function format(Shipment $s): array
    {
        return [
            'id' => (string) $s->id,
            'name' => $s->name,
            'origin' => $s->origin,
            'destination' => $s->destination,
            'cargoType' => $s->cargo_type,
            'cargoWeightKg' => $s->cargo_weight_kg,
            'priority' => $s->priority,
            'status' => $s->status,
            'driverName' => $s->driver_name,
            'vehicleId' => $s->vehicle_id,
            'estimatedArrival' => $s->estimated_arrival?->toDateString(),
            'actualArrival' => $s->actual_arrival?->toDateString(),
            'distanceKm' => $s->distance_km,
            'costEstimate' => $s->cost_estimate,
            'optimizedRoute' => $s->optimized_route,
            'aiNotes' => $s->ai_notes,
            'notes' => $s->notes,
            'createdAt' => $s->created_at?->toISOString(),
            'updatedAt' => $s->updated_at?->toISOString(),
        ];
    }

    private function authorizeOrg(Shipment $shipment): void
    {
        if ($shipment->organization_id !== auth()->user()?->activeOrgId()) {
            abort(403, 'Access denied');
        }
    }
}
