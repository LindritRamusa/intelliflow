<script setup lang="ts">
const route = useRoute()
const authStore = useAuthStore()
const { logout } = useAuth()

const navItems = [
  { label: 'Dashboard', to: '/dashboard', exact: true, icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' },
  { label: 'AI Assistant', to: '/dashboard/ai-assistant', exact: false, icon: 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z', badge: 'AI' },
  { label: 'Workflows', to: '/dashboard/workflows', exact: false, icon: 'M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z' },
  { label: 'Automations', to: '/dashboard/automations', exact: false, icon: 'M13 10V3L4 14h7v7l9-11h-7z' },
  { label: 'Analytics', to: '/dashboard/analytics', exact: false, icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z' },
  { label: 'Recruitment', to: '/dashboard/recruitment', exact: false, icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z' },
  { label: 'Knowledge Base', to: '/dashboard/knowledge-base', exact: false, icon: 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253' },
  { label: 'Logistics', to: '/dashboard/logistics', exact: false, icon: 'M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7' },
  { label: 'Notifications', to: '/dashboard/notifications', exact: false, icon: 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9' },
]

const isActive = (item: { to: string; exact: boolean }) => {
  if (item.exact) return route.path === item.to
  return route.path.startsWith(item.to)
}
</script>

<template>
  <aside class="fixed left-0 top-0 h-full w-64 bg-[#0f1117] border-r border-white/[0.06] flex flex-col z-40">
    <div class="flex items-center gap-3 px-5 py-5 border-b border-white/[0.06]">
      <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-violet-600/20 shrink-0">
        <svg class="h-5 w-5 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
        </svg>
      </div>
      <div class="min-w-0">
        <p class="text-sm font-semibold text-white truncate">IntelliFlow</p>
        <p class="text-[10px] font-medium uppercase tracking-widest text-violet-400">AI Platform</p>
      </div>
    </div>

    <div class="px-3 py-2.5 border-b border-white/[0.06]">
      <OrgSwitcher />
    </div>

    <nav class="flex-1 overflow-y-auto px-3 py-3 space-y-0.5">
      <NuxtLink
        v-for="item in navItems"
        :key="item.to"
        :to="item.to"
        :class="[
          'group flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-150',
          isActive(item)
            ? 'bg-violet-600/15 text-violet-300 border border-violet-500/20'
            : 'text-gray-400 hover:text-gray-200 hover:bg-white/5',
        ]"
      >
        <svg
          class="h-4 w-4 shrink-0 transition-colors"
          :class="isActive(item) ? 'text-violet-400' : 'text-gray-500 group-hover:text-gray-300'"
          fill="none" stroke="currentColor" viewBox="0 0 24 24"
        >
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" :d="item.icon"/>
        </svg>
        <span>{{ item.label }}</span>
        <span
          v-if="'badge' in item && item.badge"
          class="ml-auto text-[9px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded bg-violet-600/20 text-violet-400"
        >
          {{ item.badge }}
        </span>
      </NuxtLink>
    </nav>

    <div class="px-3 py-3 border-t border-white/[0.06] space-y-0.5">
      <NuxtLink
        to="/dashboard/settings"
        :class="[
          'group flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all',
          $route.path.startsWith('/dashboard/settings')
            ? 'bg-violet-600/15 text-violet-300 border border-violet-500/20'
            : 'text-gray-400 hover:text-gray-200 hover:bg-white/5',
        ]"
      >
        <svg class="h-4 w-4 shrink-0 text-gray-500 group-hover:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        <span>Settings</span>
      </NuxtLink>

      <NuxtLink
        to="/dashboard/notifications"
        :class="[
          'group flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all',
          'text-gray-400 hover:text-gray-200 hover:bg-white/5',
        ]"
      >
        <div class="h-7 w-7 rounded-full bg-violet-600/15 flex items-center justify-center shrink-0">
          <span class="text-xs font-semibold text-violet-300 uppercase">
            {{ authStore.user?.name?.charAt(0) ?? 'U' }}
          </span>
        </div>
        <div class="flex-1 min-w-0 text-left">
          <p class="text-xs font-medium text-gray-300 truncate">{{ authStore.user?.name }}</p>
          <p class="text-[10px] text-gray-500 capitalize truncate">{{ authStore.user?.role?.replace('_', ' ') }}</p>
        </div>
      </NuxtLink>
    </div>
  </aside>
</template>
