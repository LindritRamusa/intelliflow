<?php

namespace App\Http\Controllers;

use App\Models\Automation;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AutomationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $automations = Automation::where('organization_id', $request->user()->organization_id)
            ->with('workflow:id,name,status')
            ->latest()
            ->paginate(20);

        return response()->json([
            'data' => $automations->items(),
            'meta' => [
                'total' => $automations->total(),
                'page' => $automations->currentPage(),
                'perPage' => $automations->perPage(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'workflow_id' => 'nullable|exists:workflows,id',
            'trigger_type' => 'required|string',
            'action_type' => 'required|string',
            'config' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $automation = Automation::create([
            ...$validated,
            'organization_id' => $request->user()->organization_id,
            'status' => 'paused',
        ]);

        return response()->json(['data' => $automation, 'message' => 'Automation created'], 201);
    }

    public function show(Automation $automation): JsonResponse
    {
        $this->authorizeOrg($automation);

        return response()->json(['data' => $automation->load('workflow:id,name,status')]);
    }

    public function update(Request $request, Automation $automation): JsonResponse
    {
        $this->authorizeOrg($automation);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'trigger_type' => 'sometimes|string',
            'action_type' => 'sometimes|string',
            'config' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $automation->update($validated);

        return response()->json(['data' => $automation, 'message' => 'Automation updated']);
    }

    public function destroy(Automation $automation): JsonResponse
    {
        $this->authorizeOrg($automation);
        $automation->delete();

        return response()->json(['message' => 'Automation deleted']);
    }

    public function toggle(Automation $automation): JsonResponse
    {
        $this->authorizeOrg($automation);

        $newState = !$automation->is_active;
        $automation->update(['is_active' => $newState]);

        NotificationService::automationToggled(
            auth()->id(),
            auth()->user()?->organization_id,
            $automation->name,
            $newState
        );

        return response()->json(['data' => $automation]);
    }

    private function authorizeOrg(Automation $automation): void
    {
        if ($automation->organization_id !== auth()->user()?->organization_id) {
            abort(403, 'Access denied');
        }
    }
}
