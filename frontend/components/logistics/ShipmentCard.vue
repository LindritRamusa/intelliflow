<script setup lang="ts">
import type { Shipment } from '~/composables/useLogistics'

const props = defineProps<{
  shipment: Shipment
  isOptimizing?: boolean
}>()

const emit = defineEmits<{
  optimize: [shipment: Shipment]
  updateStatus: [id: string, status: Shipment['status']]
  delete: [id: string]
  view: [shipment: Shipment]
}>()

const priorityConfig = {
  low: { label: 'Low', class: 'bg-gray-500/15 text-gray-400' },
  normal: { label: 'Normal', class: 'bg-blue-500/15 text-blue-400' },
  high: { label: 'High', class: 'bg-amber-500/15 text-amber-400' },
  urgent: { label: 'Urgent', class: 'bg-red-500/15 text-red-400' },
}

const statusConfig = {
  pending: { label: 'Pending', class: 'bg-gray-500/15 text-gray-400', dot: 'bg-gray-400' },
  in_transit: { label: 'In Transit', class: 'bg-blue-500/15 text-blue-400', dot: 'bg-blue-400 animate-pulse' },
  delivered: { label: 'Delivered', class: 'bg-emerald-500/15 text-emerald-400', dot: 'bg-emerald-400' },
  cancelled: { label: 'Cancelled', class: 'bg-red-500/15 text-red-400', dot: 'bg-red-400' },
}

const statusOptions: Shipment['status'][] = ['pending', 'in_transit', 'delivered', 'cancelled']
const showStatusMenu = ref(false)
</script>

<template>
  <div class="bg-[#1a1f2e] border border-white/[0.06] rounded-xl p-5 hover:border-white/10 transition-colors">
    <div class="flex items-start justify-between gap-3 mb-4">
      <div class="min-w-0">
        <button
          class="text-white font-medium text-sm truncate hover:text-violet-300 transition-colors block text-left"
          @click="emit('view', shipment)"
        >
          {{ shipment.name }}
        </button>
        <div class="flex items-center gap-1.5 mt-1 text-xs text-gray-400">
          <span class="truncate max-w-[80px]">{{ shipment.origin }}</span>
          <svg class="w-3 h-3 shrink-0 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
          </svg>
          <span class="truncate max-w-[80px]">{{ shipment.destination }}</span>
        </div>
      </div>
      <span :class="['px-2 py-0.5 rounded-full text-xs font-medium shrink-0', priorityConfig[shipment.priority].class]">
        {{ priorityConfig[shipment.priority].label }}
      </span>
    </div>

    <div class="grid grid-cols-2 gap-2 mb-4 text-xs">
      <div class="bg-white/[0.03] rounded-lg p-2">
        <p class="text-gray-500 mb-0.5">Cargo</p>
        <p class="text-white font-medium truncate">{{ shipment.cargoType }}</p>
      </div>
      <div class="bg-white/[0.03] rounded-lg p-2">
        <p class="text-gray-500 mb-0.5">{{ shipment.distanceKm ? 'Distance' : 'Weight' }}</p>
        <p class="text-white font-medium">
          {{ shipment.distanceKm ? `${shipment.distanceKm} km` : shipment.cargoWeightKg ? `${shipment.cargoWeightKg} kg` : '—' }}
        </p>
      </div>
      <div class="bg-white/[0.03] rounded-lg p-2">
        <p class="text-gray-500 mb-0.5">ETA</p>
        <p class="text-white font-medium">{{ shipment.estimatedArrival ?? '—' }}</p>
      </div>
      <div class="bg-white/[0.03] rounded-lg p-2">
        <p class="text-gray-500 mb-0.5">Cost</p>
        <p class="text-white font-medium">{{ shipment.costEstimate ? `$${shipment.costEstimate}` : '—' }}</p>
      </div>
    </div>

    <div v-if="shipment.optimizedRoute" class="mb-4 px-3 py-2 bg-violet-500/5 border border-violet-500/15 rounded-lg">
      <div class="flex items-center gap-1.5 text-xs text-violet-400 mb-1">
        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
        </svg>
        AI optimized route
      </div>
      <p class="text-xs text-gray-400 line-clamp-2">{{ shipment.aiNotes }}</p>
    </div>

    <div class="flex items-center justify-between">
      <div class="relative">
        <button
          class="flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium cursor-pointer"
          :class="statusConfig[shipment.status].class"
          @click="showStatusMenu = !showStatusMenu"
        >
          <div :class="['w-1.5 h-1.5 rounded-full', statusConfig[shipment.status].dot]" />
          {{ statusConfig[shipment.status].label }}
        </button>
        <div
          v-if="showStatusMenu"
          class="absolute bottom-full left-0 mb-1 bg-[#1e2436] border border-white/10 rounded-lg shadow-xl py-1 z-10 min-w-[130px]"
        >
          <button
            v-for="s in statusOptions"
            :key="s"
            class="w-full text-left px-3 py-1.5 text-xs text-gray-300 hover:bg-white/5 transition-colors"
            @click="emit('updateStatus', shipment.id, s); showStatusMenu = false"
          >
            {{ statusConfig[s].label }}
          </button>
        </div>
      </div>

      <div class="flex items-center gap-1">
        <button
          class="p-1.5 rounded-lg text-gray-500 hover:text-violet-400 hover:bg-violet-500/10 transition-colors disabled:opacity-40"
          :disabled="isOptimizing"
          title="AI Route Optimization"
          @click="emit('optimize', shipment)"
        >
          <svg v-if="isOptimizing" class="w-4 h-4 animate-spin" viewBox="0 0 24 24" fill="none">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
          </svg>
          <svg v-else class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
          </svg>
        </button>
        <button
          class="p-1.5 rounded-lg text-gray-500 hover:text-red-400 hover:bg-red-500/10 transition-colors"
          @click="emit('delete', shipment.id)"
        >
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
          </svg>
        </button>
      </div>
    </div>
  </div>
</template>
