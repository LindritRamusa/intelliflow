export interface Plan {
  key: string
  name: string
  price: number
  priceId: string | null
  features: string[]
  limits: { workflows: number; automations: number; members: number }
  isCurrent: boolean
}

export interface BillingData {
  plans: Plan[]
  currentPlan: string
  subscriptionStatus: string
  trialEndsAt: string | null
  subscriptionEndsAt: string | null
}

export const useSubscription = () => {
  const { api } = useApi()

  const billing = ref<BillingData | null>(null)
  const isLoading = ref(false)
  const isRedirecting = ref(false)
  const error = ref<string | null>(null)

  const fetchBilling = async () => {
    isLoading.value = true
    error.value = null
    try {
      const res = await api<{ data: BillingData }>('/billing/plans')
      billing.value = res.data
    } catch (e: unknown) {
      error.value = (e as Error).message
    } finally {
      isLoading.value = false
    }
  }

  const startCheckout = async (plan: 'professional' | 'enterprise'): Promise<void> => {
    isRedirecting.value = true
    error.value = null
    try {
      const res = await api<{ data: { url: string | null; message?: string } }>('/billing/checkout', {
        method: 'POST',
        body: { plan },
      })

      if (res.data.url) {
        window.location.href = res.data.url
      } else if (res.data.message) {
        error.value = res.data.message
      }
    } catch (e: unknown) {
      error.value = (e as Error).message
    } finally {
      isRedirecting.value = false
    }
  }

  const openPortal = async (): Promise<void> => {
    isRedirecting.value = true
    error.value = null
    try {
      const res = await api<{ data: { url: string | null; message?: string } }>('/billing/portal', {
        method: 'POST',
      })

      if (res.data.url) {
        window.location.href = res.data.url
      } else if (res.data.message) {
        error.value = res.data.message
      }
    } catch (e: unknown) {
      error.value = (e as Error).message
    } finally {
      isRedirecting.value = false
    }
  }

  const isOnPaidPlan = computed(() =>
    billing.value ? ['professional', 'enterprise'].includes(billing.value.currentPlan) : false
  )

  const statusLabel = computed(() => {
    const status = billing.value?.subscriptionStatus
    const map: Record<string, string> = {
      active: 'Active',
      trialing: 'Trial',
      past_due: 'Past due',
      canceled: 'Cancelled',
      none: 'Free plan',
    }
    return map[status ?? 'none'] ?? 'Free plan'
  })

  return {
    billing: readonly(billing),
    isLoading: readonly(isLoading),
    isRedirecting: readonly(isRedirecting),
    error: readonly(error),
    isOnPaidPlan,
    statusLabel,
    fetchBilling,
    startCheckout,
    openPortal,
  }
}
