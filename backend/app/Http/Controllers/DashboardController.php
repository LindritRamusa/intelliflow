<?php

namespace App\Http\Controllers;

use App\Models\AiLog;
use App\Models\Automation;
use App\Models\Workflow;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function stats(Request $request): JsonResponse
    {
        $orgId = $request->user()->organization_id;

        $workflowCount = Workflow::where('organization_id', $orgId)->count();
        $activeWorkflows = Workflow::where('organization_id', $orgId)->where('status', 'active')->count();
        $automationCount = Automation::where('organization_id', $orgId)->count();
        $activeAutomations = Automation::where('organization_id', $orgId)->where('is_active', true)->count();
        $aiQueryCount = AiLog::where('organization_id', $orgId)
            ->where('created_at', '>=', now()->startOfMonth())
            ->count();
        $totalExecutions = (int) Automation::where('organization_id', $orgId)->sum('execution_count');

        $recentActivity = $this->buildRecentActivity($orgId);

        return response()->json([
            'data' => [
                'kpis' => [
                    'workflows' => ['total' => $workflowCount, 'active' => $activeWorkflows],
                    'automations' => ['total' => $automationCount, 'active' => $activeAutomations],
                    'aiQueries' => ['thisMonth' => $aiQueryCount],
                    'executions' => ['total' => $totalExecutions],
                ],
                'recentActivity' => $recentActivity,
            ],
        ]);
    }

    private function buildRecentActivity(int|null $orgId): array
    {
        if (!$orgId) return [];

        $recentWorkflows = Workflow::where('organization_id', $orgId)
            ->latest()
            ->take(3)
            ->get()
            ->map(fn (Workflow $w) => [
                'id' => (string) $w->id,
                'type' => 'workflow',
                'title' => "Workflow \"{$w->name}\" " . ($w->status === 'active' ? 'activated' : 'updated'),
                'time' => $w->updated_at->diffForHumans(),
                'status' => $w->status,
            ]);

        $recentAutomations = Automation::where('organization_id', $orgId)
            ->latest('last_executed_at')
            ->whereNotNull('last_executed_at')
            ->take(3)
            ->get()
            ->map(fn (Automation $a) => [
                'id' => (string) $a->id,
                'type' => 'automation',
                'title' => "Automation \"{$a->name}\" executed",
                'time' => $a->last_executed_at?->diffForHumans() ?? 'recently',
                'status' => $a->status,
            ]);

        return $recentWorkflows->merge($recentAutomations)
            ->values()
            ->toArray();
    }
}
