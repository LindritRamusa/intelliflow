<?php

namespace App\Http\Controllers;

use App\Models\AiLog;
use App\Models\Automation;
use App\Models\Workflow;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function overview(Request $request): JsonResponse
    {
        $orgId = $request->user()->organization_id;
        $period = $request->integer('period', 30);
        $from = now()->subDays($period);

        return response()->json([
            'data' => [
                'totalWorkflows' => Workflow::where('organization_id', $orgId)->count(),
                'activeWorkflows' => Workflow::where('organization_id', $orgId)->where('status', 'active')->count(),
                'totalAutomations' => Automation::where('organization_id', $orgId)->count(),
                'automationExecutions' => (int) Automation::where('organization_id', $orgId)
                    ->where('last_executed_at', '>=', $from)
                    ->sum('execution_count'),
                'aiQueries' => AiLog::where('organization_id', $orgId)
                    ->where('created_at', '>=', $from)
                    ->count(),
                'tokensUsed' => (int) AiLog::where('organization_id', $orgId)
                    ->where('created_at', '>=', $from)
                    ->sum('tokens_used'),
            ],
        ]);
    }

    public function workflows(Request $request): JsonResponse
    {
        $orgId = $request->user()->organization_id;

        $byStatus = Workflow::where('organization_id', $orgId)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $topWorkflows = Workflow::where('organization_id', $orgId)
            ->orderByDesc('run_count')
            ->take(5)
            ->get(['id', 'name', 'run_count', 'status']);

        return response()->json([
            'data' => [
                'byStatus' => $byStatus,
                'topWorkflows' => $topWorkflows,
            ],
        ]);
    }

    public function activity(Request $request): JsonResponse
    {
        $orgId = $request->user()->organization_id;

        $days = collect(range(6, 0))->map(function (int $daysAgo) use ($orgId) {
            $date = now()->subDays($daysAgo);
            return [
                'date' => $date->format('M d'),
                'workflows' => Workflow::where('organization_id', $orgId)
                    ->whereDate('created_at', $date->toDateString())
                    ->count(),
                'automations' => Automation::where('organization_id', $orgId)
                    ->whereDate('last_executed_at', $date->toDateString())
                    ->count(),
                'aiQueries' => AiLog::where('organization_id', $orgId)
                    ->whereDate('created_at', $date->toDateString())
                    ->count(),
            ];
        });

        return response()->json(['data' => $days]);
    }
}
