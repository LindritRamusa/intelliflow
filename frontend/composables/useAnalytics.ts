import type { AnalyticsOverview, ActivityDataPoint } from '~/types'

export const useAnalytics = () => {
  const api = useApi()
  const overview = ref<AnalyticsOverview | null>(null)
  const activityData = ref<ActivityDataPoint[]>([])
  const isLoading = ref(false)
  const error = ref<string | null>(null)

  const fetchOverview = async (period = 30): Promise<void> => {
    isLoading.value = true
    error.value = null
    try {
      const res = await api.get<AnalyticsOverview>(`/analytics/overview?period=${period}`)
      overview.value = res.data
    } catch (e) {
      error.value = e instanceof Error ? e.message : 'Failed to load analytics'
    } finally {
      isLoading.value = false
    }
  }

  const fetchActivity = async (): Promise<void> => {
    try {
      const res = await api.get<ActivityDataPoint[]>('/analytics/activity')
      activityData.value = res.data
    } catch {
      // Non-critical
    }
  }

  return { overview, activityData, isLoading, error, fetchOverview, fetchActivity }
}
