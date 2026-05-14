<script setup lang="ts">
definePageMeta({ layout: 'dashboard', middleware: 'auth' })

const authStore = useAuthStore()
const { stats, isLoading, fetchStats } = useDashboard()
const { activityData, fetchActivity } = useAnalytics()

const aiInsights = [
  {
    title: 'Workflow Efficiency Gap',
    insight: '3 workflows have 0 executions in the past 7 days. Consider reviewing their trigger conditions or archiving unused automations.',
    priority: 'medium' as const,
    category: 'Optimization',
  },
  {
    title: 'Peak Usage Pattern',
    insight: 'AI queries spike on Monday mornings. Pre-scheduling report generation on Sunday night could reduce wait times by up to 40%.',
    priority: 'low' as const,
    category: 'Performance',
  },
  {
    title: 'Automation Opportunity',
    insight: 'Detected repetitive manual patterns in your team\'s workflow. Automating these could save ~3.5 hours per week.',
    priority: 'high' as const,
    category: 'Productivity',
  },
]

onMounted(async () => {
  await Promise.all([fetchStats(), fetchActivity()])
})
</script>

<template>
  <div>
    <!-- Greeting -->
    <div class="mb-6">
      <h2 class="text-xl font-bold text-white">
        Good {{ new Date().getHours() < 12 ? 'morning' : new Date().getHours() < 18 ? 'afternoon' : 'evening' }},
        {{ authStore.user?.name?.split(' ')[0] }} 👋
      </h2>
      <p class="text-sm text-slate-400 mt-0.5">Here's what's happening with your automation platform</p>
    </div>

    <!-- KPI Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
      <KpiCard
        title="Active Workflows"
        :value="stats?.kpis.workflows.active ?? 0"
        :subtitle="`${stats?.kpis.workflows.total ?? 0} total`"
        icon="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"
        :loading="isLoading"
      />
      <KpiCard
        title="Running Automations"
        :value="stats?.kpis.automations.active ?? 0"
        :subtitle="`${stats?.kpis.automations.total ?? 0} total`"
        icon="M13 10V3L4 14h7v7l9-11h-7z"
        icon-color="bg-violet-600/15"
        :loading="isLoading"
      />
      <KpiCard
        title="AI Queries (Month)"
        :value="stats?.kpis.aiQueries.thisMonth ?? 0"
        subtitle="via AI copilot"
        icon="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"
        icon-color="bg-cyan-600/15"
        :loading="isLoading"
      />
      <KpiCard
        title="Total Executions"
        :value="stats?.kpis.executions.total ?? 0"
        subtitle="all time"
        icon="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
        icon-color="bg-emerald-600/15"
        :loading="isLoading"
      />
    </div>

    <!-- Charts + Activity -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4 mb-6">
      <!-- Activity Chart -->
      <div class="xl:col-span-2 card">
        <div class="flex items-center justify-between mb-4">
          <div>
            <h3 class="text-sm font-semibold text-white">Platform Activity</h3>
            <p class="text-xs text-slate-500">Last 7 days</p>
          </div>
        </div>
        <div class="h-52">
          <MetricsChart v-if="activityData.length" :data="activityData" />
          <div v-else class="h-full flex items-center justify-center">
            <EmptyState
              title="No activity data yet"
              description="Activity will appear as you use the platform"
            />
          </div>
        </div>
      </div>

      <!-- Recent Activity -->
      <div class="card">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-sm font-semibold text-white">Recent Activity</h3>
          <NuxtLink to="/dashboard/workflows" class="text-[11px] text-brand-400 hover:text-brand-300">
            View all
          </NuxtLink>
        </div>
        <ActivityFeed :items="stats?.recentActivity ?? []" :loading="isLoading" />
      </div>
    </div>

    <!-- AI Insights -->
    <div class="card">
      <div class="flex items-center justify-between mb-4">
        <div>
          <h3 class="text-sm font-semibold text-white">AI Insights</h3>
          <p class="text-xs text-slate-500">Recommendations from your copilot</p>
        </div>
        <NuxtLink
          to="/dashboard/ai-assistant"
          class="flex items-center gap-1.5 text-xs text-brand-400 hover:text-brand-300 font-medium transition-colors"
        >
          <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
          </svg>
          Ask AI
        </NuxtLink>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <AiInsightCard
          v-for="insight in aiInsights"
          :key="insight.title"
          :title="insight.title"
          :insight="insight.insight"
          :priority="insight.priority"
          :category="insight.category"
        />
      </div>
    </div>
  </div>
</template>
