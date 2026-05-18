import type { Candidate, CvAnalysisResult, CreateCandidatePayload, RecruitmentStats, ApiResponse } from '~/types'

export const useCandidates = () => {
  const { api } = useApi()

  const candidates = ref<Candidate[]>([])
  const stats = ref<RecruitmentStats | null>(null)
  const isLoading = ref(false)
  const isAnalyzing = ref(false)
  const error = ref<string | null>(null)
  const total = ref(0)

  const fetchCandidates = async (params: { search?: string; status?: string } = {}) => {
    isLoading.value = true
    error.value = null
    try {
      const query = new URLSearchParams()
      if (params.search) query.set('search', params.search)
      if (params.status && params.status !== 'all') query.set('status', params.status)

      const res = await api<ApiResponse<Candidate[]>>(`/candidates?${query.toString()}`)
      candidates.value = res.data
      total.value = res.meta?.total ?? res.data.length
    } catch (e: unknown) {
      error.value = (e as Error).message
    } finally {
      isLoading.value = false
    }
  }

  const fetchStats = async () => {
    try {
      const res = await api<{ data: RecruitmentStats }>('/recruitment/stats')
      stats.value = res.data
    } catch {}
  }

  const createCandidate = async (payload: CreateCandidatePayload): Promise<Candidate | null> => {
    try {
      const res = await api<{ data: Candidate }>('/candidates', { method: 'POST', body: payload })
      candidates.value.unshift(res.data)
      total.value++
      return res.data
    } catch (e: unknown) {
      error.value = (e as Error).message
      return null
    }
  }

  const updateCandidate = async (id: string, payload: Partial<Candidate>): Promise<boolean> => {
    try {
      const res = await api<{ data: Candidate }>(`/candidates/${id}`, { method: 'PUT', body: payload })
      const idx = candidates.value.findIndex(c => c.id === id)
      if (idx !== -1) candidates.value[idx] = res.data
      return true
    } catch (e: unknown) {
      error.value = (e as Error).message
      return false
    }
  }

  const deleteCandidate = async (id: string): Promise<boolean> => {
    try {
      await api(`/candidates/${id}`, { method: 'DELETE' })
      candidates.value = candidates.value.filter(c => c.id !== id)
      total.value--
      return true
    } catch (e: unknown) {
      error.value = (e as Error).message
      return false
    }
  }

  const analyzeCv = async (candidate: Candidate): Promise<CvAnalysisResult | null> => {
    if (!candidate.cv_text?.trim()) {
      error.value = 'No CV text to analyze'
      return null
    }

    isAnalyzing.value = true
    error.value = null

    try {
      const analysis = await $fetch<CvAnalysisResult>('/api/ai/analyze-cv', {
        method: 'POST',
        body: { cvText: candidate.cv_text, role: candidate.applied_role },
      })

      await api(`/candidates/${candidate.id}/analysis`, {
        method: 'PATCH',
        body: {
          ai_score: analysis.score,
          ai_analysis: analysis.assessment,
          ai_strengths: analysis.strengths,
          ai_weaknesses: analysis.weaknesses,
          skills: analysis.skills,
          recommendation: analysis.recommendation,
        },
      })

      const idx = candidates.value.findIndex(c => c.id === candidate.id)
      if (idx !== -1) {
        candidates.value[idx] = {
          ...candidates.value[idx],
          ai_score: analysis.score,
          ai_analysis: analysis.assessment,
          ai_strengths: analysis.strengths,
          ai_weaknesses: analysis.weaknesses,
          skills: analysis.skills,
        }
      }

      return analysis
    } catch (e: unknown) {
      error.value = (e as Error).message
      return null
    } finally {
      isAnalyzing.value = false
    }
  }

  return {
    candidates: readonly(candidates),
    stats: readonly(stats),
    isLoading: readonly(isLoading),
    isAnalyzing: readonly(isAnalyzing),
    error: readonly(error),
    total: readonly(total),
    fetchCandidates,
    fetchStats,
    createCandidate,
    updateCandidate,
    deleteCandidate,
    analyzeCv,
  }
}
