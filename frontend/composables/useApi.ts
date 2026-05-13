import type { ApiResponse } from '~/types'

export const useApi = () => {
  const config = useRuntimeConfig()
  const authStore = useAuthStore()

  const request = async <T>(
    endpoint: string,
    options: RequestInit = {},
  ): Promise<ApiResponse<T>> => {
    const headers: Record<string, string> = {
      'Content-Type': 'application/json',
      Accept: 'application/json',
    }

    if (authStore.token) {
      headers['Authorization'] = `Bearer ${authStore.token}`
    }

    const response = await fetch(`${config.public.apiBase}${endpoint}`, {
      ...options,
      headers: { ...headers, ...(options.headers as Record<string, string>) },
    })

    if (!response.ok) {
      throw new Error(`API error: ${response.status} ${response.statusText}`)
    }

    return response.json()
  }

  const get = <T>(endpoint: string) => request<T>(endpoint, { method: 'GET' })

  const post = <T>(endpoint: string, body: unknown) =>
    request<T>(endpoint, { method: 'POST', body: JSON.stringify(body) })

  const put = <T>(endpoint: string, body: unknown) =>
    request<T>(endpoint, { method: 'PUT', body: JSON.stringify(body) })

  const del = <T>(endpoint: string) => request<T>(endpoint, { method: 'DELETE' })

  return { get, post, put, del }
}
