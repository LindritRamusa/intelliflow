<script setup lang="ts">
import type { Workflow } from '~/types'

defineProps<{
  workflow: Workflow
}>()

const emit = defineEmits<{
  toggle: [id: string]
  delete: [id: string]
}>()
</script>

<template>
  <div class="card hover:border-surface-500 transition-all duration-200 flex flex-col gap-4">
    <!-- Header -->
    <div class="flex items-start justify-between gap-3">
      <div class="flex-1 min-w-0">
        <div class="flex items-center gap-2 mb-1">
          <h3 class="text-sm font-semibold text-slate-200 truncate">{{ workflow.name }}</h3>
          <StatusBadge :status="workflow.status" dot />
        </div>
        <p v-if="workflow.description" class="text-xs text-slate-500 line-clamp-2">
          {{ workflow.description }}
        </p>
      </div>
      <div class="flex items-center gap-1 flex-shrink-0">
        <NuxtLink
          :to="`/dashboard/workflows/${workflow.id}`"
          class="p-1.5 rounded-lg text-slate-400 hover:text-slate-200 hover:bg-surface-700 transition-colors"
          title="View details"
        >
          <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
          </svg>
        </NuxtLink>
        <button
          class="p-1.5 rounded-lg text-slate-400 hover:text-red-400 hover:bg-red-500/10 transition-colors"
          title="Delete"
          @click="emit('delete', workflow.id)"
        >
          <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
          </svg>
        </button>
      </div>
    </div>

    <!-- Trigger -->
    <div class="flex items-center gap-2 px-3 py-2 rounded-lg bg-surface-700/50 border border-surface-600">
      <svg class="h-3.5 w-3.5 text-slate-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
      </svg>
      <span class="text-xs text-slate-400 font-mono truncate">{{ workflow.trigger }}</span>
    </div>

    <!-- Footer -->
    <div class="flex items-center justify-between pt-1">
      <div class="flex items-center gap-3 text-[11px] text-slate-500">
        <span class="flex items-center gap-1">
          <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
          {{ workflow.run_count ?? workflow.runCount ?? 0 }} runs
        </span>
        <span v-if="workflow.creator">{{ workflow.creator.name }}</span>
      </div>

      <button
        class="flex items-center gap-1.5 text-xs font-medium px-2.5 py-1 rounded-md transition-colors"
        :class="
          workflow.status === 'active'
            ? 'text-amber-400 hover:bg-amber-500/10'
            : 'text-emerald-400 hover:bg-emerald-500/10'
        "
        @click="emit('toggle', workflow.id)"
      >
        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path v-if="workflow.status === 'active'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z" />
          <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        {{ workflow.status === 'active' ? 'Pause' : 'Activate' }}
      </button>
    </div>
  </div>
</template>
