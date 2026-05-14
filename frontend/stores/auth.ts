import { defineStore } from 'pinia'
import type { User, Organization } from '~/types'

interface AuthState {
  user: User | null
  token: string | null
  organization: Organization | null
  isLoading: boolean
  error: string | null
}

export const useAuthStore = defineStore('auth', {
  state: (): AuthState => ({
    user: null,
    token: null,
    organization: null,
    isLoading: false,
    error: null,
  }),

  getters: {
    isAuthenticated: (state): boolean => !!state.token,
    isAdmin: (state): boolean =>
      state.user?.role === 'super_admin' || state.user?.role === 'company_admin',
    isSuperAdmin: (state): boolean => state.user?.role === 'super_admin',
    displayName: (state): string => state.user?.name ?? '',
    orgName: (state): string => state.organization?.name ?? state.user?.organization?.name ?? '',
  },

  actions: {
    setAuth(user: User, token: string) {
      this.user = user
      this.token = token
      this.organization = user.organization ?? null
      if (import.meta.client) {
        localStorage.setItem('auth_token', token)
        localStorage.setItem('auth_user', JSON.stringify(user))
      }
    },

    clearAuth() {
      this.user = null
      this.token = null
      this.organization = null
      this.error = null
      if (import.meta.client) {
        localStorage.removeItem('auth_token')
        localStorage.removeItem('auth_user')
      }
    },

    initFromStorage() {
      if (!import.meta.client) return
      const token = localStorage.getItem('auth_token')
      const userJson = localStorage.getItem('auth_user')
      if (token && userJson) {
        try {
          this.token = token
          this.user = JSON.parse(userJson) as User
          this.organization = this.user?.organization ?? null
        } catch {
          this.clearAuth()
        }
      }
    },
  },
})
