<script setup lang="ts">
definePageMeta({ layout: 'dashboard', middleware: 'auth' })

const { overview, activityData, isLoading, fetchOverview, fetchActivity } = useAnalytics()

const selectedPeriod = ref(30)
const periodOptions = [
  { label: '7 days', value: 7 },
  { label: '30 days', value: 30 },
  { label: '90 days', value: 90 },
]

const handlePeriodChange = (period: number) => {
  selectedPeriod.value = period
  fetchOverview(period)
}

onMounted(async () => {
  await Promise.all([fetchOverview(30), fetchActivity()])
})
</script>

<template>
  <div>
    <PageHeader title="Analytics" description="Platform performance and business intelligence">
      <div class="flex items-center gap-1 p-1 bg-surface-700 rounded-lg border border-surface-600">
        <button
          v-for="opt in periodOptions"
          :key="opt.value"
          class="px-3 py-1.5 text-xs font-medium rounded-md transition-colors"
          :class="
            selectedPeriod === opt.value
              ? 'bg-brand-600 text-white'
              : 'text-slate-400 hover:text-slate-200'
          "
          @click="handlePeriodChange(opt.value)"
        >
          {{ opt.label }}
        </button>
      </div>
    </PageHeader>

    <!-- KPI Overview -->
    <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">
      <div class="card col-span-1" v-for="kpi in [
        { label: 'Total Workflows', value: overview?.totalWorkflows ?? 0, color: 'text-brand-400' },
        { label: 'Active Workflows', value: overview?.activeWorkflows ?? 0, color: 'text-emerald-400' },
        { label: 'Automations', value: overview?.totalAutomations ?? 0, color: 'text-violet-400' },
        { label: 'Executions', value: overview?.automationExecutions ?? 0, color: 'text-amber-400' },
        { label: 'AI Queries', value: overview?.aiQueries ?? 0, color: 'text-cyan-400' },
        { label: 'Tokens Used', value: overview?.tokensUsed?.toLocaleString() ?? 0, color: 'text-rose-400' },
      ]" :key="kpi.label">
        <div v-if="isLoading" class="h-6 bg-surface-700 rounded animate-pulse mb-1" />
        <p v-else class="text-xl font-bold" :class="kpi.color">{{ kpi.value }}</p>
        <p class="text-[11px] text-slate-500 mt-0.5">{{ kpi.label }}</p>
      </div>
    </div>

    <!-- Activity Chart -->
    <div class="card mb-4">
      <div class="flex items-center justify-between mb-4">
        <div>
          <h3 class="text-sm font-semibold text-white">Activity Trend</h3>
          <p class="text-xs text-slate-500">Last 7 days breakdown by type</p>
        </div>
      </div>
      <div class="h-64">
        <MetricsChart v-if="activityData.length" :data="activityData" />
        <div v-else class="h-full flex items-center justify-center">
          <EmptyState title="No activity data" description="Start using the platform to see trends" />
        </div>
      </div>
    </div>

    <!-- Bottom grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
      <!-- Bar chart comparison -->
      <div class="card">
        <h3 class="text-sm font-semibold text-white mb-1">Daily Comparison</h3>
        <p class="text-xs text-slate-500 mb-4">Workflows vs automations by day</p>
        <div class="h-48">
          <MetricsChart v-if="activityData.length" :data="activityData" type="bar" />
          <EmptyState v-else title="No comparison data" />
        </div>
      </div>

      <!-- AI Usage -->
      <div class="card">
        <h3 class="text-sm font-semibold text-white mb-1">AI Usage Summary</h3>
        <p class="text-xs text-slate-500 mb-4">Copilot utilization metrics</p>
        <div class="space-y-3">
          <div
            v-for="metric in [
              { label: 'Total AI Queries', value: overview?.aiQueries ?? 0, max: 1000, color: 'bg-cyan-500' },
              { label: 'Tokens Consumed', value: overview?.tokensUsed ?? 0, max: 100000, color: 'bg-violet-500' },
              { label: 'Active Workflows', value: overview?.activeWorkflows ?? 0, max: overview?.totalWorkflows ?? 10, color: 'bg-brand-500' },
              { label: 'Running Automations', value: overview?.automationExecutions ?? 0, max: overview?.totalAutomations ?? 10, color: 'bg-emerald-500' },
            ]"
            :key="metric.label"
          >
            <div class="flex items-center justify-between text-xs mb-1">
              <span class="text-slate-400">{{ metric.label }}</span>
              <span class="text-white font-medium tabular-nums">{{ metric.value.toLocaleString() }}</span>
            </div>
            <div class="h-1.5 bg-surface-600 rounded-full overflow-hidden">
              <div
                class="h-full rounded-full transition-all duration-500"
                :class="metric.color"
                :style="{ width: `${Math.min(100, metric.max > 0 ? (metric.value / metric.max) * 100 : 0)}%` }"
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
