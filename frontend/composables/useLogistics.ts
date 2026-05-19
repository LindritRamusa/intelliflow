import type { ApiResponse } from '~/types'

export interface Shipment {
  id: string
  name: string
  origin: string
  destination: string
  cargoType: string
  cargoWeightKg: number | null
  priority: 'low' | 'normal' | 'high' | 'urgent'
  status: 'pending' | 'in_transit' | 'delivered' | 'cancelled'
  driverName: string | null
  vehicleId: string | null
  estimatedArrival: string | null
  actualArrival: string | null
  distanceKm: number | null
  costEstimate: number | null
  optimizedRoute: RouteOptimization | null
  aiNotes: string | null
  notes: string | null
  createdAt: string
  updatedAt: string
}

export interface RouteOptimization {
  waypoints: string[]
  estimatedHours: number | null
  distanceKm: number | null
  costEstimate: number | null
  notes: string
  risks: string[]
  recommendations: string[]
}

export interface LogisticsStats {
  total: number
  inTransit: number
  delivered: number
  pending: number
  urgent: number
  avgCost: number
  byStatus: Record<string, number>
  byPriority: Record<string, number>
}

export interface CreateShipmentPayload {
  name: string
  origin: string
  destination: string
  cargoType?: string
  cargoWeightKg?: number
  priority?: 'low' | 'normal' | 'high' | 'urgent'
  driverName?: string
  vehicleId?: string
  estimatedArrival?: string
  distanceKm?: number
  costEstimate?: number
  notes?: string
}

export const useLogistics = () => {
  const { api } = useApi()

  const shipments = ref<Shipment[]>([])
  const stats = ref<LogisticsStats | null>(null)
  const isLoading = ref(false)
  const isOptimizing = ref(false)
  const error = ref<string | null>(null)
  const total = ref(0)

  const fetchShipments = async (params: { search?: string; status?: string; priority?: string } = {}) => {
    isLoading.value = true
    error.value = null
    try {
      const query = new URLSearchParams()
      if (params.search) query.set('search', params.search)
      if (params.status && params.status !== 'all') query.set('status', params.status)
      if (params.priority && params.priority !== 'all') query.set('priority', params.priority)

      const res = await api<ApiResponse<Shipment[]>>(`/shipments?${query.toString()}`)
      shipments.value = res.data
      total.value = res.meta?.total ?? res.data.length
    } catch (e: unknown) {
      error.value = (e as Error).message
    } finally {
      isLoading.value = false
    }
  }

  const fetchStats = async () => {
    try {
      const res = await api<{ data: LogisticsStats }>('/logistics/stats')
      stats.value = res.data
    } catch {}
  }

  const createShipment = async (payload: CreateShipmentPayload): Promise<Shipment | null> => {
    try {
      const res = await api<{ data: Shipment }>('/shipments', { method: 'POST', body: payload })
      shipments.value.unshift(res.data)
      total.value++
      return res.data
    } catch (e: unknown) {
      error.value = (e as Error).message
      return null
    }
  }

  const updateShipment = async (id: string, payload: Partial<CreateShipmentPayload & { status: Shipment['status']; actualArrival: string }>): Promise<boolean> => {
    try {
      const res = await api<{ data: Shipment }>(`/shipments/${id}`, { method: 'PUT', body: payload })
      const idx = shipments.value.findIndex(s => s.id === id)
      if (idx !== -1) shipments.value[idx] = res.data
      return true
    } catch (e: unknown) {
      error.value = (e as Error).message
      return false
    }
  }

  const deleteShipment = async (id: string): Promise<boolean> => {
    try {
      await api(`/shipments/${id}`, { method: 'DELETE' })
      shipments.value = shipments.value.filter(s => s.id !== id)
      total.value--
      return true
    } catch (e: unknown) {
      error.value = (e as Error).message
      return false
    }
  }

  const optimizeRoute = async (shipment: Shipment): Promise<RouteOptimization | null> => {
    isOptimizing.value = true
    error.value = null
    try {
      const optimization = await $fetch<RouteOptimization>('/api/ai/optimize-route', {
        method: 'POST',
        body: {
          name: shipment.name,
          origin: shipment.origin,
          destination: shipment.destination,
          cargoType: shipment.cargoType,
          cargoWeightKg: shipment.cargoWeightKg,
          priority: shipment.priority,
          estimatedArrival: shipment.estimatedArrival,
        },
      })

      await api(`/shipments/${shipment.id}/optimization`, {
        method: 'PATCH',
        body: {
          optimized_route: optimization,
          ai_notes: optimization.notes,
          distance_km: optimization.distanceKm,
          cost_estimate: optimization.costEstimate,
        },
      })

      const idx = shipments.value.findIndex(s => s.id === shipment.id)
      if (idx !== -1) {
        shipments.value[idx] = {
          ...shipments.value[idx],
          optimizedRoute: optimization,
          aiNotes: optimization.notes,
          distanceKm: optimization.distanceKm,
          costEstimate: optimization.costEstimate,
        }
      }

      return optimization
    } catch (e: unknown) {
      error.value = (e as Error).message
      return null
    } finally {
      isOptimizing.value = false
    }
  }

  return {
    shipments: readonly(shipments),
    stats: readonly(stats),
    isLoading: readonly(isLoading),
    isOptimizing: readonly(isOptimizing),
    error: readonly(error),
    total: readonly(total),
    fetchShipments,
    fetchStats,
    createShipment,
    updateShipment,
    deleteShipment,
    optimizeRoute,
  }
}
