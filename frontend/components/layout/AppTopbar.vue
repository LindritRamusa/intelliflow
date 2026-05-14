<script setup lang="ts">
const route = useRoute()
const { togglePanel, unreadCount } = useNotifications()

const pageTitle = computed(() => {
  const map: Record<string, string> = {
    '/dashboard': 'Dashboard',
    '/dashboard/ai-assistant': 'AI Assistant',
    '/dashboard/workflows': 'Workflows',
    '/dashboard/automations': 'Automations',
    '/dashboard/analytics': 'Analytics',
    '/dashboard/recruitment': 'Recruitment AI',
    '/dashboard/knowledge-base': 'Knowledge Base',
    '/dashboard/notifications': 'Notifications',
    '/dashboard/settings': 'Settings',
  }
  const matched = Object.entries(map)
    .filter(([path]) => route.path.startsWith(path))
    .sort((a, b) => b[0].length - a[0].length)[0]
  return matched?.[1] ?? 'IntelliFlow'
})

const pageDescription = computed(() => {
  const map: Record<string, string> = {
    '/dashboard': 'Overview of your automation platform',
    '/dashboard/ai-assistant': 'Chat with your enterprise AI copilot',
    '/dashboard/workflows': 'Manage and monitor your workflows',
    '/dashboard/automations': 'Configure and run automations',
    '/dashboard/analytics': 'Insights and performance metrics',
    '/dashboard/recruitment': 'AI-powered candidate screening',
    '/dashboard/knowledge-base': 'Company documentation and search',
    '/dashboard/notifications': 'Alerts and system notifications',
    '/dashboard/settings': 'Platform configuration',
  }
  const matched = Object.entries(map)
    .filter(([path]) => route.path.startsWith(path))
    .sort((a, b) => b[0].length - a[0].length)[0]
  return matched?.[1] ?? ''
})
</script>

<template>
  <header
    class="fixed top-0 left-64 right-0 h-16 bg-surface-800/90 backdrop-blur-sm border-b border-surface-600 z-30 flex items-center px-6 gap-4"
  >
    <!-- Page title -->
    <div class="flex-1 min-w-0">
      <h1 class="text-sm font-semibold text-white truncate">{{ pageTitle }}</h1>
      <p class="text-[11px] text-slate-500 truncate hidden sm:block">{{ pageDescription }}</p>
    </div>

    <!-- Search -->
    <div class="hidden md:flex items-center gap-2 bg-surface-700 border border-surface-600 rounded-lg px-3 py-1.5 w-56 focus-within:border-brand-500 transition-colors">
      <svg class="h-3.5 w-3.5 text-slate-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
      </svg>
      <input
        type="text"
        placeholder="Search..."
        class="bg-transparent text-xs text-slate-300 placeholder-slate-500 focus:outline-none w-full"
      />
      <kbd class="text-[9px] text-slate-600 font-mono hidden lg:block">⌘K</kbd>
    </div>

    <!-- AI Quick Action -->
    <NuxtLink
      to="/dashboard/ai-assistant"
      class="hidden sm:flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-brand-600/15 border border-brand-500/20 text-brand-300 hover:bg-brand-600/25 transition-colors text-xs font-medium"
    >
      <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
      </svg>
      Ask AI
    </NuxtLink>

    <!-- Notifications -->
    <button
      class="relative p-2 rounded-lg text-slate-400 hover:text-slate-200 hover:bg-surface-700 transition-colors"
      @click="togglePanel()"
    >
      <svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 18px; height: 18px">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
      </svg>
      <span
        v-if="unreadCount > 0"
        class="absolute top-1 right-1 h-4 w-4 flex items-center justify-center rounded-full bg-brand-600 text-[9px] font-bold text-white"
      >
        {{ unreadCount > 9 ? '9+' : unreadCount }}
      </span>
    </button>
  </header>
</template>
