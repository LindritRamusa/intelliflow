<script setup lang="ts">
import type { Candidate } from '~/types'

const props = defineProps<{
  candidate: Candidate
}>()

const emit = defineEmits<{
  close: []
}>()

const scoreColor = computed(() => {
  const s = props.candidate.ai_score
  if (s === null) return '#6b7280'
  if (s >= 80) return '#10b981'
  if (s >= 60) return '#f59e0b'
  return '#ef4444'
})

const scoreLabel = computed(() => {
  const s = props.candidate.ai_score
  if (s === null) return 'Not analyzed'
  if (s >= 80) return 'Strong match'
  if (s >= 60) return 'Good potential'
  return 'Below threshold'
})
</script>

<template>
  <div class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-[#141824] border border-white/10 rounded-2xl w-full max-w-xl max-h-[85vh] overflow-y-auto shadow-2xl">
      <div class="flex items-center justify-between p-6 border-b border-white/[0.06]">
        <div>
          <h2 class="text-white font-semibold text-lg">AI Analysis</h2>
          <p class="text-gray-400 text-sm mt-0.5">{{ candidate.name }} — {{ candidate.applied_role }}</p>
        </div>
        <button class="p-2 rounded-lg text-gray-400 hover:text-white hover:bg-white/5 transition-colors" @click="emit('close')">
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>

      <div class="p-6 space-y-6">
        <div class="flex items-center gap-6 p-5 bg-white/[0.03] rounded-xl border border-white/[0.06]">
          <div class="relative w-20 h-20 shrink-0">
            <svg class="w-20 h-20 -rotate-90" viewBox="0 0 36 36">
              <circle cx="18" cy="18" r="16" fill="none" stroke="#ffffff08" stroke-width="3"/>
              <circle
                cx="18" cy="18" r="16" fill="none"
                :stroke="scoreColor"
                stroke-width="3"
                stroke-linecap="round"
                :stroke-dasharray="`${(candidate.ai_score ?? 0) * 1.005} 100.53`"
              />
            </svg>
            <div class="absolute inset-0 flex items-center justify-center">
              <span class="text-xl font-bold text-white">{{ candidate.ai_score ?? '—' }}</span>
            </div>
          </div>
          <div>
            <p class="text-white font-semibold text-lg">{{ scoreLabel }}</p>
            <p class="text-gray-400 text-sm mt-1">{{ candidate.ai_analysis }}</p>
          </div>
        </div>

        <div v-if="(candidate.ai_strengths ?? []).length > 0">
          <h3 class="text-sm font-medium text-emerald-400 mb-3 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            Strengths
          </h3>
          <ul class="space-y-2">
            <li
              v-for="(strength, i) in candidate.ai_strengths"
              :key="i"
              class="flex items-start gap-2 text-sm text-gray-300"
            >
              <span class="text-emerald-400 mt-0.5">•</span>
              {{ strength }}
            </li>
          </ul>
        </div>

        <div v-if="(candidate.ai_weaknesses ?? []).length > 0">
          <h3 class="text-sm font-medium text-amber-400 mb-3 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M12 3a9 9 0 100 18A9 9 0 0012 3z"/>
            </svg>
            Areas to Explore
          </h3>
          <ul class="space-y-2">
            <li
              v-for="(gap, i) in candidate.ai_weaknesses"
              :key="i"
              class="flex items-start gap-2 text-sm text-gray-300"
            >
              <span class="text-amber-400 mt-0.5">•</span>
              {{ gap }}
            </li>
          </ul>
        </div>

        <div v-if="(candidate.skills ?? []).length > 0">
          <h3 class="text-sm font-medium text-gray-300 mb-3">Detected Skills</h3>
          <div class="flex flex-wrap gap-1.5">
            <span
              v-for="skill in candidate.skills"
              :key="skill"
              class="px-2.5 py-1 bg-white/5 border border-white/[0.06] rounded-full text-xs text-gray-300"
            >
              {{ skill }}
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
