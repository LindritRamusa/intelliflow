<script setup lang="ts">
defineProps<{
  title: string
  insight: string
  category?: string
  priority?: 'low' | 'medium' | 'high'
}>()

const priorityColor = (p?: string) => {
  const map: Record<string, string> = {
    high: 'text-red-400 bg-red-500/10',
    medium: 'text-amber-400 bg-amber-500/10',
    low: 'text-emerald-400 bg-emerald-500/10',
  }
  return map[p ?? 'low'] ?? map.low
}
</script>

<template>
  <div class="glass-panel p-4 hover:border-brand-500/30 transition-colors">
    <div class="flex items-start gap-3">
      <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-600/15 flex-shrink-0">
        <svg class="h-4 w-4 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
        </svg>
      </div>
      <div class="flex-1 min-w-0">
        <div class="flex items-center gap-2 mb-1">
          <p class="text-xs font-semibold text-slate-200">{{ title }}</p>
          <span v-if="priority" class="badge text-[10px] px-1.5 py-0.5" :class="priorityColor(priority)">
            {{ priority }}
          </span>
        </div>
        <p class="text-xs text-slate-400 leading-relaxed">{{ insight }}</p>
        <p v-if="category" class="text-[11px] text-slate-600 mt-1.5">{{ category }}</p>
      </div>
    </div>
  </div>
</template>
