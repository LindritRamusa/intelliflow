<script setup lang="ts">
import type { Shipment, RouteOptimization } from '~/composables/useLogistics'

definePageMeta({ layout: 'dashboard' })

const { shipments, stats, isLoading, isOptimizing, error, total, fetchShipments, fetchStats, createShipment, updateShipment, deleteShipment, optimizeRoute } = useLogistics()

const showAddModal = ref(false)
const activeStatus = ref('all')
const searchQuery = ref('')
const optimizingId = ref<string | null>(null)
const activeOptimization = ref<RouteOptimization | null>(null)
const activeOptimizationShipment = ref<Shipment | null>(null)
const confirmDeleteId = ref<string | null>(null)

const statusTabs = [
  { key: 'all', label: 'All' },
  { key: 'pending', label: 'Pending' },
  { key: 'in_transit', label: 'In Transit' },
  { key: 'delivered', label: 'Delivered' },
  { key: 'cancelled', label: 'Cancelled' },
]

let searchTimer: ReturnType<typeof setTimeout>
const handleSearch = () => {
  clearTimeout(searchTimer)
  searchTimer = setTimeout(() => fetchShipments({ search: searchQuery.value, status: activeStatus.value }), 350)
}

const handleStatusFilter = (status: string) => {
  activeStatus.value = status
  fetchShipments({ status, search: searchQuery.value })
}

const handleCreate = async (payload: Parameters<typeof createShipment>[0]) => {
  const result = await createShipment(payload)
  if (result) {
    showAddModal.value = false
    fetchStats()
  }
}

const handleOptimize = async (shipment: Shipment) => {
  optimizingId.value = shipment.id
  const result = await optimizeRoute(shipment)
  optimizingId.value = null
  if (result) {
    activeOptimization.value = result
    activeOptimizationShipment.value = shipments.value.find(s => s.id === shipment.id) ?? shipment
    fetchStats()
  }
}

const handleUpdateStatus = async (id: string, status: Shipment['status']) => {
  await updateShipment(id, { status })
  fetchStats()
}

const handleDelete = async () => {
  if (!confirmDeleteId.value) return
  await deleteShipment(confirmDeleteId.value)
  confirmDeleteId.value = null
  fetchStats()
}

onMounted(() => {
  fetchShipments()
  fetchStats()
})
</script>

<template>
  <div class="p-6 space-y-6">
    <PageHeader title="Logistics Optimizer" description="AI-powered shipment tracking and route optimization">
      <template #actions>
        <button
          class="flex items-center gap-2 px-4 py-2 bg-violet-600 hover:bg-violet-500 rounded-lg text-white text-sm font-medium transition-colors"
          @click="showAddModal = true"
        >
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
          </svg>
          New Shipment
        </button>
      </template>
    </PageHeader>

    <div v-if="stats" class="grid grid-cols-2 lg:grid-cols-4 gap-4">
      <KpiCard title="Total Shipments" :value="stats.total" icon="📦" />
      <KpiCard title="In Transit" :value="stats.inTransit" icon="🚛" />
      <KpiCard title="Delivered" :value="stats.delivered" icon="✅" />
      <KpiCard title="Urgent / High" :value="stats.urgent" icon="🔴" />
    </div>

    <div class="bg-[#141824] border border-white/[0.06] rounded-xl">
      <div class="p-4 border-b border-white/[0.06] flex flex-col sm:flex-row items-start sm:items-center gap-3">
        <div class="relative flex-1 min-w-0">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
          </svg>
          <input
            v-model="searchQuery"
            type="text"
            class="w-full bg-white/5 border border-white/10 rounded-lg pl-9 pr-3 py-2 text-white text-sm placeholder-gray-500 focus:outline-none focus:border-violet-500/50 transition-colors"
            placeholder="Search shipments..."
            @input="handleSearch"
          />
        </div>
        <div class="flex items-center gap-1 overflow-x-auto">
          <button
            v-for="tab in statusTabs"
            :key="tab.key"
            :class="['px-3 py-1.5 rounded-lg text-xs whitespace-nowrap transition-colors', activeStatus === tab.key ? 'bg-violet-600 text-white' : 'text-gray-400 hover:text-white hover:bg-white/5']"
            @click="handleStatusFilter(tab.key)"
          >
            {{ tab.label }}
          </button>
        </div>
      </div>

      <div class="p-4">
        <LoadingSpinner v-if="isLoading" class="py-12" />

        <div v-else-if="error" class="py-12 text-center">
          <p class="text-red-400 text-sm">{{ error }}</p>
          <button class="mt-3 text-violet-400 text-sm hover:underline" @click="fetchShipments()">Retry</button>
        </div>

        <EmptyState
          v-else-if="shipments.length === 0"
          title="No shipments yet"
          description="Create a shipment and let AI optimize the route"
          icon="📦"
        >
          <template #action>
            <button class="px-4 py-2 bg-violet-600 hover:bg-violet-500 rounded-lg text-white text-sm font-medium transition-colors" @click="showAddModal = true">
              New Shipment
            </button>
          </template>
        </EmptyState>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
          <ShipmentCard
            v-for="shipment in shipments"
            :key="shipment.id"
            :shipment="shipment"
            :is-optimizing="optimizingId === shipment.id"
            @optimize="handleOptimize"
            @update-status="handleUpdateStatus"
            @delete="confirmDeleteId = $event"
            @view="activeOptimizationShipment = $event; activeOptimization = $event.optimizedRoute"
          />
        </div>

        <p v-if="shipments.length > 0" class="text-xs text-gray-500 text-center mt-4">
          Showing {{ shipments.length }} of {{ total }} shipments
        </p>
      </div>
    </div>

    <AddShipmentModal v-if="showAddModal" @close="showAddModal = false" @submit="handleCreate" />

    <RouteOptimizationPanel
      v-if="activeOptimization && activeOptimizationShipment"
      :shipment="activeOptimizationShipment"
      :optimization="activeOptimization"
      @close="activeOptimization = null; activeOptimizationShipment = null"
    />

    <ConfirmModal
      v-if="confirmDeleteId"
      title="Delete Shipment"
      description="This will permanently delete the shipment record."
      confirm-label="Delete"
      @confirm="handleDelete"
      @cancel="confirmDeleteId = null"
    />
  </div>
</template>
