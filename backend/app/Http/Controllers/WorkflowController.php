<?php

namespace App\Http\Controllers;

use App\Models\Workflow;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WorkflowController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Workflow::where('organization_id', $request->user()->activeOrgId())
            ->with('creator:id,name,avatar_url')
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $query->where('name', 'ilike', '%' . $request->input('search') . '%');
        }

        $workflows = $query->paginate(20);

        return response()->json([
            'data' => $workflows->items(),
            'meta' => [
                'total' => $workflows->total(),
                'page' => $workflows->currentPage(),
                'perPage' => $workflows->perPage(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'trigger' => 'required|string',
            'trigger_config' => 'nullable|array',
            'status' => 'in:active,paused,draft',
        ]);

        $workflow = Workflow::create([
            ...$validated,
            'organization_id' => $request->user()->activeOrgId(),
            'created_by' => $request->user()->id,
            'status' => $validated['status'] ?? 'draft',
        ]);

        if ($workflow->status === 'active') {
            NotificationService::workflowActivated(
                $request->user()->id,
                $request->user()->organization_id,
                $workflow->name
            );
        }

        return response()->json(['data' => $workflow->load('creator:id,name'), 'message' => 'Workflow created'], 201);
    }

    public function show(Workflow $workflow): JsonResponse
    {
        $this->authorizeOrg($workflow);

        return response()->json(['data' => $workflow->load(['creator:id,name', 'automations'])]);
    }

    public function update(Request $request, Workflow $workflow): JsonResponse
    {
        $this->authorizeOrg($workflow);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'trigger' => 'sometimes|string',
            'trigger_config' => 'nullable|array',
            'status' => 'in:active,paused,draft',
        ]);

        $workflow->update($validated);

        return response()->json(['data' => $workflow, 'message' => 'Workflow updated']);
    }

    public function destroy(Workflow $workflow): JsonResponse
    {
        $this->authorizeOrg($workflow);
        $workflow->delete();

        return response()->json(['message' => 'Workflow deleted']);
    }

    public function toggle(Workflow $workflow): JsonResponse
    {
        $this->authorizeOrg($workflow);

        $newStatus = $workflow->status === 'active' ? 'paused' : 'active';
        $workflow->update(['status' => $newStatus]);

        $userId = auth()->id();
        $orgId = auth()->user()?->organization_id;

        if ($newStatus === 'active') {
            NotificationService::workflowActivated($userId, $orgId, $workflow->name);
        } else {
            NotificationService::workflowPaused($userId, $orgId, $workflow->name);
        }

        return response()->json(['data' => $workflow, 'message' => 'Workflow status updated']);
    }

    private function authorizeOrg(Workflow $workflow): void
    {
        if ($workflow->organization_id !== auth()->user()?->organization_id) {
            abort(403, 'Access denied');
        }
    }
}
