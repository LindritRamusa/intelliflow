<script setup lang="ts">
definePageMeta({ layout: 'dashboard' })

const { overview, activityData, predictions, isLoading, isPredicting, fetchOverview, fetchActivity, fetchPredictions } = useAnalytics()

const selectedPeriod = ref(30)
const periodOptions = [
  { label: '7 days', value: 7 },
  { label: '30 days', value: 30 },
  { label: '90 days', value: 90 },
]

const insightTypeConfig: Record<string, { class: string; icon: string }> = {
  success: { class: 'bg-emerald-500/10 border-emerald-500/20 text-emerald-400', icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' },
  info: { class: 'bg-blue-500/10 border-blue-500/20 text-blue-400', icon: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
  warning: { class: 'bg-amber-500/10 border-amber-500/20 text-amber-400', icon: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z' },
}

const handlePeriodChange = (period: number) => {
  selectedPeriod.value = period
  fetchOverview(period)
}

onMounted(async () => {
  await Promise.all([fetchOverview(30), fetchActivity(), fetchPredictions()])
})
</script>

<template>
  <div class="p-6 space-y-6">
    <PageHeader title="Analytics" description="Platform performance and predictive intelligence">
      <template #actions>
        <div class="flex items-center gap-1 p-1 bg-white/5 border border-white/10 rounded-lg">
          <button
            v-for="opt in periodOptions"
            :key="opt.value"
            :class="['px-3 py-1.5 text-xs font-medium rounded-md transition-colors', selectedPeriod === opt.value ? 'bg-violet-600 text-white' : 'text-gray-400 hover:text-white']"
            @click="handlePeriodChange(opt.value)"
          >
            {{ opt.label }}
          </button>
        </div>
      </template>
    </PageHeader>

    <div v-if="isLoading" class="grid grid-cols-2 lg:grid-cols-3 gap-4">
      <div v-for="i in 6" :key="i" class="bg-[#141824] border border-white/[0.06] rounded-xl p-5 animate-pulse h-24" />
    </div>

    <div v-else-if="overview" class="grid grid-cols-2 lg:grid-cols-3 gap-4">
      <KpiCard title="Total Workflows" :value="overview.totalWorkflows" icon="⚙️" />
      <KpiCard title="Active Workflows" :value="overview.activeWorkflows" icon="✅" />
      <KpiCard title="Automations" :value="overview.totalAutomations" icon="⚡" />
      <KpiCard title="Executions" :value="overview.automationExecutions" icon="🔄" />
      <KpiCard title="AI Queries" :value="overview.aiQueries" icon="🤖" />
      <KpiCard title="Tokens Used" :value="overview.tokensUsed.toLocaleString()" icon="🧠" />
    </div>

    <div v-if="activityData.length > 0" class="bg-[#141824] border border-white/[0.06] rounded-xl p-5">
      <h2 class="text-white font-medium mb-4">Activity (Last 7 Days)</h2>
      <MetricsChart :data="activityData" />
    </div>

    <div class="bg-[#141824] border border-white/[0.06] rounded-xl p-5">
      <div class="flex items-center justify-between mb-5">
        <div>
          <h2 class="text-white font-medium">Predictive Intelligence</h2>
          <p class="text-gray-400 text-sm mt-0.5">AI-generated forecasts based on usage patterns</p>
        </div>
        <div v-if="isPredicting" class="flex items-center gap-2 text-xs text-gray-400">
          <svg class="w-4 h-4 animate-spin" viewBox="0 0 24 24" fill="none">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
          </svg>
          Analyzing...
        </div>
      </div>

      <div v-if="predictions" class="space-y-5">
        <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
          <div class="bg-white/[0.03] border border-white/[0.06] rounded-xl p-4">
            <p class="text-xs text-gray-500 mb-1">AI Query Trend</p>
            <div class="flex items-baseline gap-2">
              <span class="text-2xl font-bold" :class="predictions.aiQueryTrend >= 0 ? 'text-emerald-400' : 'text-red-400'">
                {{ predictions.aiQueryTrend >= 0 ? '+' : '' }}{{ predictions.aiQueryTrend }}%
              </span>
            </div>
            <p class="text-xs text-gray-500 mt-1">vs previous 30 days</p>
          </div>
          <div class="bg-white/[0.03] border border-white/[0.06] rounded-xl p-4">
            <p class="text-xs text-gray-500 mb-1">Projected AI Queries</p>
            <span class="text-2xl font-bold text-white">{{ predictions.projectedAiQueries }}</span>
            <p class="text-xs text-gray-500 mt-1">next 30 days</p>
          </div>
          <div class="bg-white/[0.03] border border-white/[0.06] rounded-xl p-4">
            <p class="text-xs text-gray-500 mb-1">Automation Efficiency</p>
            <span class="text-2xl font-bold text-violet-400">{{ Math.round(predictions.automationEfficiency) }}%</span>
            <p class="text-xs text-gray-500 mt-1">estimated</p>
          </div>
          <div class="bg-white/[0.03] border border-white/[0.06] rounded-xl p-4">
            <p class="text-xs text-gray-500 mb-1">Cost Savings Est.</p>
            <span class="text-2xl font-bold text-emerald-400">${{ Math.round(predictions.estimatedCostSavings) }}</span>
            <p class="text-xs text-gray-500 mt-1">this month</p>
          </div>
          <div class="bg-white/[0.03] border border-white/[0.06] rounded-xl p-4">
            <p class="text-xs text-gray-500 mb-1">New Workflows</p>
            <span class="text-2xl font-bold text-white">{{ predictions.workflowsAddedThisMonth }}</span>
            <p class="text-xs text-gray-500 mt-1">this month</p>
          </div>
          <div class="bg-white/[0.03] border border-white/[0.06] rounded-xl p-4">
            <p class="text-xs text-gray-500 mb-1">Projected Workflows</p>
            <span class="text-2xl font-bold text-white">{{ predictions.projectedWorkflows }}</span>
            <p class="text-xs text-gray-500 mt-1">next 30 days</p>
          </div>
        </div>

        <div v-if="predictions.insights.length > 0">
          <h3 class="text-sm font-medium text-gray-300 mb-3">AI Insights</h3>
          <div class="space-y-2">
            <div
              v-for="(insight, i) in predictions.insights"
              :key="i"
              :class="['flex items-start gap-3 p-3.5 border rounded-xl', insightTypeConfig[insight.type]?.class ?? insightTypeConfig.info.class]"
            >
              <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="insightTypeConfig[insight.type]?.icon ?? insightTypeConfig.info.icon"/>
              </svg>
              <p class="text-sm">{{ insight.text }}</p>
            </div>
          </div>
        </div>
      </div>

      <div v-else-if="!isPredicting" class="py-8 text-center">
        <p class="text-gray-500 text-sm">No prediction data available yet. Predictions improve as you use the platform.</p>
      </div>
    </div>
  </div>
</template>
