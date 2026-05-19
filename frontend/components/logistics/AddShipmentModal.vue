<script setup lang="ts">
import type { CreateShipmentPayload } from '~/composables/useLogistics'

const emit = defineEmits<{
  close: []
  submit: [payload: CreateShipmentPayload]
}>()

const form = reactive<CreateShipmentPayload>({
  name: '',
  origin: '',
  destination: '',
  cargoType: 'General',
  cargoWeightKg: undefined,
  priority: 'normal',
  driverName: '',
  vehicleId: '',
  estimatedArrival: '',
  notes: '',
})

const cargoTypes = ['General', 'Perishable', 'Fragile', 'Hazardous', 'Electronics', 'Furniture', 'Automotive', 'Medical', 'Documents']

const handleSubmit = () => {
  if (!form.name.trim() || !form.origin.trim() || !form.destination.trim()) return
  emit('submit', { ...form })
}
</script>

<template>
  <div class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-[#141824] border border-white/10 rounded-2xl w-full max-w-xl max-h-[90vh] overflow-y-auto shadow-2xl">
      <div class="flex items-center justify-between p-6 border-b border-white/[0.06]">
        <div>
          <h2 class="text-white font-semibold text-lg">New Shipment</h2>
          <p class="text-gray-400 text-sm mt-0.5">Create a shipment for AI route optimization</p>
        </div>
        <button class="p-2 rounded-lg text-gray-400 hover:text-white hover:bg-white/5 transition-colors" @click="emit('close')">
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>

      <form class="p-6 space-y-4" @submit.prevent="handleSubmit">
        <div>
          <label class="block text-sm text-gray-300 mb-1.5">Shipment Name <span class="text-red-400">*</span></label>
          <input v-model="form.name" type="text" required class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2.5 text-white text-sm placeholder-gray-500 focus:outline-none focus:border-violet-500/50 transition-colors" placeholder="e.g. Berlin → Munich — Electronics Batch" />
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm text-gray-300 mb-1.5">Origin <span class="text-red-400">*</span></label>
            <input v-model="form.origin" type="text" required class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2.5 text-white text-sm placeholder-gray-500 focus:outline-none focus:border-violet-500/50 transition-colors" placeholder="City, Country" />
          </div>
          <div>
            <label class="block text-sm text-gray-300 mb-1.5">Destination <span class="text-red-400">*</span></label>
            <input v-model="form.destination" type="text" required class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2.5 text-white text-sm placeholder-gray-500 focus:outline-none focus:border-violet-500/50 transition-colors" placeholder="City, Country" />
          </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm text-gray-300 mb-1.5">Cargo Type</label>
            <select v-model="form.cargoType" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2.5 text-white text-sm focus:outline-none focus:border-violet-500/50 transition-colors">
              <option v-for="t in cargoTypes" :key="t" :value="t" class="bg-[#1a1f2e]">{{ t }}</option>
            </select>
          </div>
          <div>
            <label class="block text-sm text-gray-300 mb-1.5">Weight (kg)</label>
            <input v-model.number="form.cargoWeightKg" type="number" min="0" step="0.1" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2.5 text-white text-sm placeholder-gray-500 focus:outline-none focus:border-violet-500/50 transition-colors" placeholder="0.00" />
          </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm text-gray-300 mb-1.5">Priority</label>
            <select v-model="form.priority" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2.5 text-white text-sm focus:outline-none focus:border-violet-500/50 transition-colors">
              <option value="low" class="bg-[#1a1f2e]">Low</option>
              <option value="normal" class="bg-[#1a1f2e]">Normal</option>
              <option value="high" class="bg-[#1a1f2e]">High</option>
              <option value="urgent" class="bg-[#1a1f2e]">Urgent</option>
            </select>
          </div>
          <div>
            <label class="block text-sm text-gray-300 mb-1.5">Estimated Arrival</label>
            <input v-model="form.estimatedArrival" type="date" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2.5 text-white text-sm focus:outline-none focus:border-violet-500/50 transition-colors" />
          </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm text-gray-300 mb-1.5">Driver Name</label>
            <input v-model="form.driverName" type="text" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2.5 text-white text-sm placeholder-gray-500 focus:outline-none focus:border-violet-500/50 transition-colors" placeholder="Driver name" />
          </div>
          <div>
            <label class="block text-sm text-gray-300 mb-1.5">Vehicle ID</label>
            <input v-model="form.vehicleId" type="text" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2.5 text-white text-sm placeholder-gray-500 focus:outline-none focus:border-violet-500/50 transition-colors" placeholder="Plate / ID" />
          </div>
        </div>

        <div>
          <label class="block text-sm text-gray-300 mb-1.5">Notes</label>
          <textarea v-model="form.notes" rows="2" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2.5 text-white text-sm placeholder-gray-500 focus:outline-none focus:border-violet-500/50 transition-colors resize-none" placeholder="Special handling instructions..." />
        </div>

        <div class="flex gap-3 pt-2">
          <button type="button" class="flex-1 px-4 py-2.5 bg-white/5 border border-white/10 rounded-lg text-gray-300 text-sm hover:bg-white/[0.08] transition-colors" @click="emit('close')">Cancel</button>
          <button type="submit" :disabled="!form.name.trim() || !form.origin.trim() || !form.destination.trim()" class="flex-1 px-4 py-2.5 bg-violet-600 hover:bg-violet-500 disabled:opacity-50 rounded-lg text-white text-sm font-medium transition-colors">Create Shipment</button>
        </div>
      </form>
    </div>
  </div>
</template>
