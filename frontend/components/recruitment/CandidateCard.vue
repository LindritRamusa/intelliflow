<script setup lang="ts">
import type { Candidate, CandidateStatus } from '~/types'

const props = defineProps<{
  candidate: Candidate
  isAnalyzing?: boolean
}>()

const emit = defineEmits<{
  analyze: [candidate: Candidate]
  updateStatus: [id: string, status: CandidateStatus]
  delete: [id: string]
  view: [candidate: Candidate]
}>()

const scoreColor = computed(() => {
  const s = props.candidate.ai_score
  if (s === null) return 'text-gray-400'
  if (s >= 80) return 'text-emerald-400'
  if (s >= 60) return 'text-yellow-400'
  return 'text-red-400'
})

const scoreBg = computed(() => {
  const s = props.candidate.ai_score
  if (s === null) return 'bg-gray-800'
  if (s >= 80) return 'bg-emerald-500/10 border border-emerald-500/20'
  if (s >= 60) return 'bg-yellow-500/10 border border-yellow-500/20'
  return 'bg-red-500/10 border border-red-500/20'
})

const statusConfig: Record<CandidateStatus, { label: string; class: string }> = {
  pending: { label: 'Pending', class: 'bg-gray-500/15 text-gray-400' },
  review: { label: 'In Review', class: 'bg-blue-500/15 text-blue-400' },
  shortlisted: { label: 'Shortlisted', class: 'bg-emerald-500/15 text-emerald-400' },
  interview: { label: 'Interview', class: 'bg-purple-500/15 text-purple-400' },
  rejected: { label: 'Rejected', class: 'bg-red-500/15 text-red-400' },
  hired: { label: 'Hired', class: 'bg-amber-500/15 text-amber-400' },
}

const handleAnalyze = () => emit('analyze', props.candidate)

const statusOptions: CandidateStatus[] = ['pending', 'review', 'shortlisted', 'interview', 'rejected', 'hired']
const showStatusMenu = ref(false)
</script>

<template>
  <div class="bg-[#1a1f2e] border border-white/[0.06] rounded-xl p-5 hover:border-white/10 transition-colors group">
    <div class="flex items-start justify-between gap-3 mb-4">
      <div class="flex items-center gap-3 min-w-0">
        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-violet-500 to-indigo-500 flex items-center justify-center text-white font-semibold text-sm shrink-0">
          {{ candidate.name.charAt(0).toUpperCase() }}
        </div>
        <div class="min-w-0">
          <button
            class="text-white font-medium text-sm truncate hover:text-violet-300 transition-colors block"
            @click="emit('view', candidate)"
          >
            {{ candidate.name }}
          </button>
          <p class="text-gray-400 text-xs truncate">{{ candidate.applied_role }}</p>
        </div>
      </div>

      <div :class="['px-2.5 py-1 rounded-full text-xs font-medium shrink-0', scoreBg, scoreColor]">
        <span v-if="candidate.ai_score !== null">{{ candidate.ai_score }}/100</span>
        <span v-else class="text-gray-500">No score</span>
      </div>
    </div>

    <div class="flex flex-wrap gap-1.5 mb-4 min-h-[24px]">
      <span
        v-for="skill in (candidate.skills ?? []).slice(0, 4)"
        :key="skill"
        class="px-2 py-0.5 bg-white/5 rounded text-xs text-gray-300"
      >
        {{ skill }}
      </span>
      <span v-if="(candidate.skills?.length ?? 0) > 4" class="px-2 py-0.5 bg-white/5 rounded text-xs text-gray-500">
        +{{ (candidate.skills?.length ?? 0) - 4 }} more
      </span>
    </div>

    <div class="flex items-center justify-between gap-2">
      <div class="relative">
        <button
          :class="['px-2.5 py-1 rounded-full text-xs font-medium cursor-pointer', statusConfig[candidate.status].class]"
          @click="showStatusMenu = !showStatusMenu"
        >
          {{ statusConfig[candidate.status].label }}
        </button>
        <div
          v-if="showStatusMenu"
          class="absolute bottom-full left-0 mb-1 bg-[#1e2436] border border-white/10 rounded-lg shadow-xl py-1 z-10 min-w-[140px]"
        >
          <button
            v-for="s in statusOptions"
            :key="s"
            class="w-full text-left px-3 py-1.5 text-xs text-gray-300 hover:bg-white/5 transition-colors"
            @click="emit('updateStatus', candidate.id, s); showStatusMenu = false"
          >
            {{ statusConfig[s].label }}
          </button>
        </div>
      </div>

      <div class="flex items-center gap-1">
        <button
          class="p-1.5 rounded-lg text-gray-500 hover:text-violet-400 hover:bg-violet-500/10 transition-colors disabled:opacity-40"
          :disabled="isAnalyzing || !candidate.cv_text"
          :title="candidate.cv_text ? 'Run AI Analysis' : 'No CV text to analyze'"
          @click="handleAnalyze"
        >
          <svg v-if="isAnalyzing" class="w-4 h-4 animate-spin" viewBox="0 0 24 24" fill="none">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
          </svg>
          <svg v-else class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
          </svg>
        </button>
        <button
          class="p-1.5 rounded-lg text-gray-500 hover:text-red-400 hover:bg-red-500/10 transition-colors"
          @click="emit('delete', candidate.id)"
        >
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
          </svg>
        </button>
      </div>
    </div>
  </div>
</template>
