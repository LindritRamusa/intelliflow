import { defineStore } from 'pinia'
import type { AppNotification } from '~/types'

interface NotificationsState {
  notifications: AppNotification[]
  unreadCount: number
  isPanelOpen: boolean
  isLoading: boolean
}

export const useNotificationsStore = defineStore('notifications', {
  state: (): NotificationsState => ({
    notifications: [],
    unreadCount: 0,
    isPanelOpen: false,
    isLoading: false,
  }),

  getters: {
    unread: (state) => state.notifications.filter((n) => !n.read),
    hasUnread: (state) => state.unreadCount > 0,
  },

  actions: {
    setNotifications(notifications: AppNotification[], unread: number) {
      this.notifications = notifications
      this.unreadCount = unread
    },

    markRead(id: string) {
      const n = this.notifications.find((n) => n.id === id)
      if (n && !n.read) {
        n.read = true
        this.unreadCount = Math.max(0, this.unreadCount - 1)
      }
    },

    markAllRead() {
      this.notifications.forEach((n) => (n.read = true))
      this.unreadCount = 0
    },

    togglePanel() {
      this.isPanelOpen = !this.isPanelOpen
    },

    closePanel() {
      this.isPanelOpen = false
    },
  },
})
