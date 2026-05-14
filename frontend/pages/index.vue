<script setup lang="ts">
definePageMeta({ layout: 'default' })

const authStore = useAuthStore()

const features = [
  {
    icon: 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z',
    title: 'AI Copilot',
    desc: 'Context-aware enterprise AI assistant for instant business insights',
  },
  {
    icon: 'M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z',
    title: 'Workflow Automation',
    desc: 'Build intelligent pipelines with trigger-based workflow orchestration',
  },
  {
    icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
    title: 'Smart Analytics',
    desc: 'Real-time dashboards with AI-generated performance insights',
  },
  {
    icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
    title: 'Recruitment AI',
    desc: 'Intelligent CV screening, candidate ranking, and skill matching',
  },
  {
    icon: 'M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2',
    title: 'Knowledge Base',
    desc: 'Semantic AI search across your company documentation and wiki',
  },
  {
    icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
    title: 'Logistics Optimizer',
    desc: 'Route optimization, delivery tracking, and predictive scheduling',
  },
]
</script>

<template>
  <div class="min-h-screen bg-surface-900 bg-gradient-mesh">
    <!-- Hero -->
    <div class="flex flex-col items-center justify-center min-h-screen px-4 text-center">
      <div class="mb-3">
        <span class="badge-blue text-xs px-3 py-1">Enterprise AI Automation Platform</span>
      </div>

      <div class="mb-6 flex items-center gap-3">
        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-600/20 border border-brand-500/20">
          <svg class="h-7 w-7 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
          </svg>
        </div>
      </div>

      <h1 class="text-5xl font-bold text-white mb-4 tracking-tight max-w-2xl leading-tight">
        Automate everything.
        <span class="text-brand-400">Intelligently.</span>
      </h1>

      <p class="text-lg text-slate-400 max-w-xl mb-10 leading-relaxed">
        IntelliFlow is an enterprise AI operating platform that automates workflows, surfaces insights, and powers smart decisions across your entire organization.
      </p>

      <div class="flex items-center gap-3">
        <NuxtLink
          :to="authStore.isAuthenticated ? '/dashboard' : '/auth/register'"
          class="btn-primary px-6 py-3 text-sm"
        >
          {{ authStore.isAuthenticated ? 'Go to Dashboard' : 'Start for free' }}
          <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
        </NuxtLink>
        <NuxtLink to="/auth/login" class="btn-secondary px-6 py-3 text-sm">
          Sign in
        </NuxtLink>
      </div>

      <!-- Stats -->
      <div class="flex items-center gap-8 mt-14 text-center">
        <div v-for="stat in [['10x', 'Faster operations'], ['99.9%', 'Uptime SLA'], ['AI-first', 'Architecture']]" :key="stat[0]">
          <p class="text-2xl font-bold text-white">{{ stat[0] }}</p>
          <p class="text-xs text-slate-500 mt-0.5">{{ stat[1] }}</p>
        </div>
      </div>
    </div>

    <!-- Features -->
    <div class="max-w-5xl mx-auto px-6 pb-24">
      <div class="text-center mb-12">
        <h2 class="text-2xl font-bold text-white mb-3">Everything your team needs</h2>
        <p class="text-sm text-slate-400">A complete AI operations suite for modern businesses</p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div
          v-for="feature in features"
          :key="feature.title"
          class="glass-panel p-5 hover:border-brand-500/30 transition-all duration-200 hover:bg-surface-700/40"
        >
          <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand-600/15 mb-3">
            <svg class="h-4.5 w-4.5 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 18px; height: 18px">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" :d="feature.icon" />
            </svg>
          </div>
          <h3 class="text-sm font-semibold text-slate-200 mb-1.5">{{ feature.title }}</h3>
          <p class="text-xs text-slate-500 leading-relaxed">{{ feature.desc }}</p>
        </div>
      </div>

      <div class="text-center mt-12">
        <NuxtLink
          :to="authStore.isAuthenticated ? '/dashboard' : '/auth/register'"
          class="btn-primary px-8 py-3 text-sm"
        >
          {{ authStore.isAuthenticated ? 'Open Dashboard' : 'Get started — it\'s free' }}
        </NuxtLink>
      </div>
    </div>
  </div>
</template>
