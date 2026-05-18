import type { Automation, CreateAutomationPayload } from '~/types'

export const useAutomations = () => {
  const api = useApi()
  const automations = ref<Automation[]>([])
  const total = ref(0)
  const isLoading = ref(false)
  const error = ref<string | null>(null)

  const fetchAutomations = async () => {
    isLoading.value = true
    error.value = null
    try {
      const res = await api.get<Automation[]>('/automations')
      automations.value = res.data
      total.value = res.meta?.total ?? res.data.length
    } catch (e) {
      error.value = e instanceof Error ? e.message : 'Failed to load automations'
    } finally {
      isLoading.value = false
    }
  }

  const createAutomation = async (payload: CreateAutomationPayload): Promise<Automation> => {
    const res = await api.post<Automation>('/automations', payload)
    automations.value.unshift(res.data)
    total.value += 1
    return res.data
  }

  const toggleAutomation = async (id: string): Promise<Automation> => {
    const res = await api.patch<Automation>(`/automations/${id}/toggle`)
    const idx = automations.value.findIndex((a) => a.id === id)
    if (idx >= 0) automations.value[idx] = res.data
    return res.data
  }

  const updateAutomation = async (id: string, payload: Partial<CreateAutomationPayload>): Promise<Automation> => {
    const res = await api.put<Automation>(`/automations/${id}`, payload)
    const idx = automations.value.findIndex((a) => a.id === id)
    if (idx >= 0) automations.value[idx] = res.data
    return res.data
  }

  const deleteAutomation = async (id: string): Promise<void> => {
    await api.del(`/automations/${id}`)
    automations.value = automations.value.filter((a) => a.id !== id)
    total.value -= 1
  }

  return {
    automations,
    total,
    isLoading,
    error,
    fetchAutomations,
    createAutomation,
    updateAutomation,
    toggleAutomation,
    deleteAutomation,
  }
}
