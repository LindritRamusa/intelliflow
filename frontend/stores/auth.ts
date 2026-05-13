import { defineStore } from 'pinia'

interface User {
  id: string
  email: string
  name: string
  role: 'super_admin' | 'company_admin' | 'manager' | 'employee'
  organizationId: string
}

interface AuthState {
  user: User | null
  token: string | null
  isLoading: boolean
}

export const useAuthStore = defineStore('auth', {
  state: (): AuthState => ({
    user: null,
    token: null,
    isLoading: false,
  }),

  getters: {
    isAuthenticated: (state) => !!state.token,
    isAdmin: (state) =>
      state.user?.role === 'super_admin' || state.user?.role === 'company_admin',
  },

  actions: {
    setUser(user: User, token: string) {
      this.user = user
      this.token = token
    },

    clearAuth() {
      this.user = null
      this.token = null
    },
  },
})
