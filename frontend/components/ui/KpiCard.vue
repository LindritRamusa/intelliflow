<script setup lang="ts">
defineProps<{
  title: string
  value: string | number
  subtitle?: string
  trend?: number
  icon: string
  iconColor?: string
  loading?: boolean
}>()
</script>

<template>
  <div class="card flex flex-col gap-4">
    <div class="flex items-start justify-between">
      <div
        class="flex h-10 w-10 items-center justify-center rounded-xl flex-shrink-0"
        :class="iconColor ?? 'bg-brand-600/15'"
      >
        <svg class="h-5 w-5" :class="iconColor ? 'text-current' : 'text-brand-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" :d="icon" />
        </svg>
      </div>
      <div
        v-if="trend !== undefined"
        class="flex items-center gap-1 text-xs font-medium"
        :class="trend >= 0 ? 'text-emerald-400' : 'text-red-400'"
      >
        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            :d="trend >= 0 ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7'"
          />
        </svg>
        {{ Math.abs(trend) }}%
      </div>
    </div>

    <div>
      <div v-if="loading" class="h-8 w-24 bg-surface-700 rounded animate-pulse mb-1" />
      <p v-else class="text-2xl font-bold text-white tabular-nums">{{ value }}</p>
      <p class="text-xs font-medium text-slate-400 mt-0.5">{{ title }}</p>
      <p v-if="subtitle" class="text-[11px] text-slate-500 mt-0.5">{{ subtitle }}</p>
    </div>
  </div>
</template>
