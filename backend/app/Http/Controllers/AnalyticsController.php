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
        $orgId = $request->user()->activeOrgId();
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
        $orgId = $request->user()->activeOrgId();

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
        $orgId = $request->user()->activeOrgId();

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

    public function predictions(Request $request): JsonResponse
    {
        $orgId = $request->user()->activeOrgId();
        $now = now();

        $last30 = AiLog::where('organization_id', $orgId)
            ->where('created_at', '>=', $now->copy()->subDays(30))
            ->count();
        $prev30 = AiLog::where('organization_id', $orgId)
            ->whereBetween('created_at', [$now->copy()->subDays(60), $now->copy()->subDays(30)])
            ->count();

        $wfGrowth = Workflow::where('organization_id', $orgId)
            ->where('created_at', '>=', $now->copy()->subDays(30))
            ->count();

        $aiTrend = $prev30 > 0 ? round((($last30 - $prev30) / $prev30) * 100) : 0;
        $projectedAi = (int) round($last30 * 1.15);

        return response()->json([
            'data' => [
                'aiQueryTrend' => $aiTrend,
                'projectedAiQueries' => $projectedAi,
                'workflowsAddedThisMonth' => $wfGrowth,
                'projectedWorkflows' => Workflow::where('organization_id', $orgId)->count() + (int) round($wfGrowth * 0.8),
                'estimatedCostSavings' => $last30 * 2.4,
                'automationEfficiency' => min(98, 60 + ($last30 / max(1, $prev30)) * 15),
                'insights' => $this->buildInsights($orgId, $last30, $aiTrend, $wfGrowth),
            ],
        ]);
    }

    private function buildInsights(int $orgId, int $aiLast30, int $aiTrend, int $wfGrowth): array
    {
        $insights = [];

        if ($aiTrend > 20) {
            $insights[] = ['type' => 'success', 'text' => "AI usage is up {$aiTrend}% this month — strong adoption trend."];
        } elseif ($aiTrend < -10) {
            $insights[] = ['type' => 'warning', 'text' => 'AI usage declined this period. Consider reviewing team engagement.'];
        }

        if ($wfGrowth >= 3) {
            $insights[] = ['type' => 'info', 'text' => "{$wfGrowth} new workflows created this month — team is scaling automation."];
        }

        $inactiveAutomations = Automation::where('organization_id', $orgId)
            ->where('is_active', false)
            ->count();
        if ($inactiveAutomations > 0) {
            $insights[] = ['type' => 'warning', 'text' => "{$inactiveAutomations} automation(s) are inactive. Review and re-enable to maximize efficiency."];
        }

        if (empty($insights)) {
            $insights[] = ['type' => 'info', 'text' => 'System is operating normally. Keep monitoring for optimization opportunities.'];
        }

        return $insights;
    }
}
