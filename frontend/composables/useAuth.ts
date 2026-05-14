import type { User } from '~/types'

interface LoginPayload {
  email: string
  password: string
}

interface RegisterPayload {
  name: string
  email: string
  password: string
  password_confirmation: string
  organization_name: string
}

export const useAuth = () => {
  const authStore = useAuthStore()
  const api = useApi()
  const router = useRouter()

  const login = async (payload: LoginPayload): Promise<void> => {
    authStore.isLoading = true
    authStore.error = null
    try {
      const res = await api.post<{ user: User; token: string }>('/auth/login', payload)
      authStore.setAuth(res.data.user, res.data.token)
      await router.push('/dashboard')
    } catch (e) {
      authStore.error = e instanceof Error ? e.message : 'Login failed'
      throw e
    } finally {
      authStore.isLoading = false
    }
  }

  const register = async (payload: RegisterPayload): Promise<void> => {
    authStore.isLoading = true
    authStore.error = null
    try {
      const res = await api.post<{ user: User; token: string }>('/auth/register', payload)
      authStore.setAuth(res.data.user, res.data.token)
      await router.push('/dashboard')
    } catch (e) {
      authStore.error = e instanceof Error ? e.message : 'Registration failed'
      throw e
    } finally {
      authStore.isLoading = false
    }
  }

  const logout = async (): Promise<void> => {
    try {
      await api.post('/auth/logout', {})
    } catch {
      // Token may already be invalid
    } finally {
      authStore.clearAuth()
      await router.push('/auth/login')
    }
  }

  const fetchMe = async (): Promise<void> => {
    try {
      const res = await api.get<User>('/auth/me')
      if (authStore.token) {
        authStore.setAuth(res.data, authStore.token)
      }
    } catch {
      authStore.clearAuth()
    }
  }

  return { login, register, logout, fetchMe }
}
