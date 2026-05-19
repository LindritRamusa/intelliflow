import type { Organization } from '~/types'

interface OrgWithStatus extends Organization {
  subscriptionStatus: string
  isActive: boolean
}

export const useOrganizations = () => {
  const { api } = useApi()
  const authStore = useAuthStore()

  const organizations = ref<OrgWithStatus[]>([])
  const isLoading = ref(false)
  const isSwitching = ref(false)
  const error = ref<string | null>(null)

  const currentOrg = computed(() =>
    organizations.value.find(o => o.isActive) ?? organizations.value[0] ?? null
  )

  const fetchOrganizations = async () => {
    isLoading.value = true
    try {
      const res = await api<{ data: OrgWithStatus[] }>('/organizations')
      organizations.value = res.data
    } catch (e: unknown) {
      error.value = (e as Error).message
    } finally {
      isLoading.value = false
    }
  }

  const createOrganization = async (name: string): Promise<OrgWithStatus | null> => {
    try {
      const res = await api<{ data: OrgWithStatus }>('/organizations', { method: 'POST', body: { name } })
      organizations.value.push(res.data)
      organizations.value = organizations.value.map(o => ({ ...o, isActive: o.id === res.data.id }))
      await refreshUser()
      return res.data
    } catch (e: unknown) {
      error.value = (e as Error).message
      return null
    }
  }

  const switchOrganization = async (orgId: string): Promise<boolean> => {
    isSwitching.value = true
    try {
      await api(`/organizations/${orgId}/switch`, { method: 'PATCH' })
      organizations.value = organizations.value.map(o => ({ ...o, isActive: o.id === orgId }))
      await refreshUser()
      return true
    } catch (e: unknown) {
      error.value = (e as Error).message
      return false
    } finally {
      isSwitching.value = false
    }
  }

  const refreshUser = async () => {
    try {
      const { api: apiCall } = useApi()
      const res = await apiCall<{ data: Parameters<typeof authStore.setAuth>[0] }>('/auth/me')
      if (authStore.token) {
        authStore.setAuth(res.data, authStore.token)
      }
    } catch {}
  }

  return {
    organizations: readonly(organizations),
    currentOrg,
    isLoading: readonly(isLoading),
    isSwitching: readonly(isSwitching),
    error: readonly(error),
    fetchOrganizations,
    createOrganization,
    switchOrganization,
  }
}
