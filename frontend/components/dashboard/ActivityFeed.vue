<script setup lang="ts">
import type { ActivityItem } from '~/types'

defineProps<{
  items: ActivityItem[]
  loading?: boolean
}>()

const typeIcon = (type: string) => {
  const map: Record<string, string> = {
    workflow: 'M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z',
    automation: 'M13 10V3L4 14h7v7l9-11h-7z',
    ai: 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z',
    system: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
  }
  return map[type] ?? map.system
}

const typeColor = (type: string) => {
  const map: Record<string, string> = {
    workflow: 'bg-brand-600/15 text-brand-400',
    automation: 'bg-violet-600/15 text-violet-400',
    ai: 'bg-cyan-600/15 text-cyan-400',
    system: 'bg-slate-700 text-slate-400',
  }
  return map[type] ?? map.system
}
</script>

<template>
  <div class="space-y-3">
    <template v-if="loading">
      <div v-for="i in 4" :key="i" class="flex items-start gap-3 animate-pulse">
        <div class="h-8 w-8 rounded-lg bg-surface-700 flex-shrink-0" />
        <div class="flex-1 space-y-1.5">
          <div class="h-3 bg-surface-700 rounded w-3/4" />
          <div class="h-2.5 bg-surface-700 rounded w-1/4" />
        </div>
      </div>
    </template>

    <template v-else-if="items.length">
      <div
        v-for="item in items"
        :key="item.id"
        class="flex items-start gap-3"
      >
        <div
          class="h-8 w-8 rounded-lg flex items-center justify-center flex-shrink-0"
          :class="typeColor(item.type)"
        >
          <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" :d="typeIcon(item.type)" />
          </svg>
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-xs text-slate-300 leading-relaxed">{{ item.title }}</p>
          <p class="text-[11px] text-slate-500 mt-0.5">{{ item.time }}</p>
        </div>
        <StatusBadge v-if="item.status" :status="item.status" />
      </div>
    </template>

    <EmptyState
      v-else
      title="No recent activity"
      description="Activity will appear here as you use the platform"
    />
  </div>
</template>
