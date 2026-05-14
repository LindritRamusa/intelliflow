import type { Workflow, CreateWorkflowPayload } from '~/types'

export const useWorkflows = () => {
  const api = useApi()
  const workflows = ref<Workflow[]>([])
  const total = ref(0)
  const isLoading = ref(false)
  const error = ref<string | null>(null)

  const fetchWorkflows = async (params?: { status?: string; search?: string }) => {
    isLoading.value = true
    error.value = null
    try {
      const query = new URLSearchParams()
      if (params?.status) query.set('status', params.status)
      if (params?.search) query.set('search', params.search)
      const endpoint = `/workflows${query.toString() ? '?' + query.toString() : ''}`
      const res = await api.get<Workflow[]>(endpoint)
      workflows.value = res.data
      total.value = res.meta?.total ?? res.data.length
    } catch (e) {
      error.value = e instanceof Error ? e.message : 'Failed to load workflows'
    } finally {
      isLoading.value = false
    }
  }

  const createWorkflow = async (payload: CreateWorkflowPayload): Promise<Workflow> => {
    const res = await api.post<Workflow>('/workflows', payload)
    workflows.value.unshift(res.data)
    total.value += 1
    return res.data
  }

  const updateWorkflow = async (id: string, payload: Partial<CreateWorkflowPayload>): Promise<Workflow> => {
    const res = await api.put<Workflow>(`/workflows/${id}`, payload)
    const idx = workflows.value.findIndex((w) => w.id === id)
    if (idx >= 0) workflows.value[idx] = res.data
    return res.data
  }

  const toggleWorkflow = async (id: string): Promise<Workflow> => {
    const res = await api.patch<Workflow>(`/workflows/${id}/toggle`)
    const idx = workflows.value.findIndex((w) => w.id === id)
    if (idx >= 0) workflows.value[idx] = res.data
    return res.data
  }

  const deleteWorkflow = async (id: string): Promise<void> => {
    await api.del(`/workflows/${id}`)
    workflows.value = workflows.value.filter((w) => w.id !== id)
    total.value -= 1
  }

  return {
    workflows,
    total,
    isLoading,
    error,
    fetchWorkflows,
    createWorkflow,
    updateWorkflow,
    toggleWorkflow,
    deleteWorkflow,
  }
}
