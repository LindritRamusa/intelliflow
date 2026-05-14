import type { AppNotification } from '~/types'

export const useNotifications = () => {
  const api = useApi()
  const store = useNotificationsStore()

  const fetchNotifications = async (): Promise<void> => {
    store.isLoading = true
    try {
      const res = await api.get<AppNotification[]>('/notifications')
      store.setNotifications(res.data, res.meta?.unread ?? 0)
    } catch {
      // Silently fail — notifications are non-critical
    } finally {
      store.isLoading = false
    }
  }

  const markRead = async (id: string): Promise<void> => {
    store.markRead(id)
    try {
      await api.patch(`/notifications/${id}/read`)
    } catch {
      // Optimistic update already applied
    }
  }

  const markAllRead = async (): Promise<void> => {
    store.markAllRead()
    try {
      await api.patch('/notifications/read-all')
    } catch {
      // Optimistic update already applied
    }
  }

  return {
    notifications: computed(() => store.notifications),
    unreadCount: computed(() => store.unreadCount),
    isPanelOpen: computed(() => store.isPanelOpen),
    isLoading: computed(() => store.isLoading),
    fetchNotifications,
    markRead,
    markAllRead,
    togglePanel: () => store.togglePanel(),
    closePanel: () => store.closePanel(),
  }
}
