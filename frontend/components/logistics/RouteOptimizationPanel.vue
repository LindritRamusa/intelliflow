<script setup lang="ts">
import type { Shipment, RouteOptimization } from '~/composables/useLogistics'

defineProps<{
  shipment: Shipment
  optimization: RouteOptimization
}>()

const emit = defineEmits<{ close: [] }>()
</script>

<template>
  <div class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-[#141824] border border-white/10 rounded-2xl w-full max-w-lg max-h-[85vh] overflow-y-auto shadow-2xl">
      <div class="flex items-center justify-between p-6 border-b border-white/[0.06]">
        <div>
          <h2 class="text-white font-semibold text-lg">AI Route Optimization</h2>
          <p class="text-gray-400 text-sm mt-0.5">{{ shipment.name }}</p>
        </div>
        <button class="p-2 rounded-lg text-gray-400 hover:text-white hover:bg-white/5 transition-colors" @click="emit('close')">
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>

      <div class="p-6 space-y-5">
        <div class="grid grid-cols-3 gap-3">
          <div class="bg-white/[0.03] border border-white/[0.06] rounded-xl p-3 text-center">
            <p class="text-2xl font-bold text-white">{{ optimization.distanceKm ? `${optimization.distanceKm}` : '—' }}</p>
            <p class="text-xs text-gray-500 mt-0.5">km</p>
          </div>
          <div class="bg-white/[0.03] border border-white/[0.06] rounded-xl p-3 text-center">
            <p class="text-2xl font-bold text-white">{{ optimization.estimatedHours ? `${optimization.estimatedHours}h` : '—' }}</p>
            <p class="text-xs text-gray-500 mt-0.5">estimated</p>
          </div>
          <div class="bg-white/[0.03] border border-white/[0.06] rounded-xl p-3 text-center">
            <p class="text-2xl font-bold text-white">{{ optimization.costEstimate ? `$${optimization.costEstimate}` : '—' }}</p>
            <p class="text-xs text-gray-500 mt-0.5">cost est.</p>
          </div>
        </div>

        <div>
          <h3 class="text-sm font-medium text-gray-300 mb-3 flex items-center gap-2">
            <svg class="w-4 h-4 text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
            </svg>
            Optimized Route
          </h3>
          <div class="space-y-2">
            <div
              v-for="(waypoint, i) in optimization.waypoints"
              :key="i"
              class="flex items-center gap-3"
            >
              <div class="flex flex-col items-center shrink-0">
                <div :class="['w-2.5 h-2.5 rounded-full border-2', i === 0 ? 'border-emerald-400 bg-emerald-400' : i === optimization.waypoints.length - 1 ? 'border-violet-400 bg-violet-400' : 'border-gray-500 bg-transparent']" />
                <div v-if="i < optimization.waypoints.length - 1" class="w-0.5 h-5 bg-white/10 my-0.5" />
              </div>
              <span class="text-sm" :class="i === 0 || i === optimization.waypoints.length - 1 ? 'text-white font-medium' : 'text-gray-400'">
                {{ waypoint }}
              </span>
            </div>
          </div>
        </div>

        <div>
          <h3 class="text-sm font-medium text-gray-300 mb-2">Summary</h3>
          <p class="text-sm text-gray-400 leading-relaxed">{{ optimization.notes }}</p>
        </div>

        <div v-if="optimization.risks.length > 0">
          <h3 class="text-sm font-medium text-amber-400 mb-2 flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            Risks
          </h3>
          <ul class="space-y-1">
            <li v-for="(risk, i) in optimization.risks" :key="i" class="flex items-start gap-2 text-sm text-gray-400">
              <span class="text-amber-400 mt-0.5 shrink-0">•</span>{{ risk }}
            </li>
          </ul>
        </div>

        <div v-if="optimization.recommendations.length > 0">
          <h3 class="text-sm font-medium text-emerald-400 mb-2 flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Recommendations
          </h3>
          <ul class="space-y-1">
            <li v-for="(rec, i) in optimization.recommendations" :key="i" class="flex items-start gap-2 text-sm text-gray-400">
              <span class="text-emerald-400 mt-0.5 shrink-0">•</span>{{ rec }}
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>
