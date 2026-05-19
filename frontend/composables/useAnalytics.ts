import type { AnalyticsOverview, ActivityDataPoint } from '~/types'

export interface PredictionData {
  aiQueryTrend: number
  projectedAiQueries: number
  workflowsAddedThisMonth: number
  projectedWorkflows: number
  estimatedCostSavings: number
  automationEfficiency: number
  insights: { type: string; text: string }[]
}

export const useAnalytics = () => {
  const api = useApi()
  const overview = ref<AnalyticsOverview | null>(null)
  const activityData = ref<ActivityDataPoint[]>([])
  const predictions = ref<PredictionData | null>(null)
  const isLoading = ref(false)
  const isPredicting = ref(false)
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
    } catch {}
  }

  const fetchPredictions = async (): Promise<void> => {
    isPredicting.value = true
    try {
      const res = await api.get<PredictionData>('/analytics/predictions')
      predictions.value = res.data
    } catch {} finally {
      isPredicting.value = false
    }
  }

  return { overview, activityData, predictions, isLoading, isPredicting, error, fetchOverview, fetchActivity, fetchPredictions }
}
