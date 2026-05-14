import type { DashboardStats } from '~/types'

export const useDashboard = () => {
  const api = useApi()
  const stats = ref<DashboardStats | null>(null)
  const isLoading = ref(false)
  const error = ref<string | null>(null)

  const fetchStats = async (): Promise<void> => {
    isLoading.value = true
    error.value = null
    try {
      const res = await api.get<DashboardStats>('/dashboard/stats')
      stats.value = res.data
    } catch (e) {
      error.value = e instanceof Error ? e.message : 'Failed to load dashboard stats'
    } finally {
      isLoading.value = false
    }
  }

  return { stats, isLoading, error, fetchStats }
}
