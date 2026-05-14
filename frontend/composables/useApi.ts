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
      const data = await response.json().catch(() => ({})) as Record<string, unknown>
      const errors = data.errors as Record<string, string[]> | undefined
      const firstError = errors ? Object.values(errors)[0]?.[0] : undefined
      throw new Error(firstError ?? (data.message as string) ?? `Error ${response.status}`)
    }

    return response.json() as Promise<ApiResponse<T>>
  }

  const get = <T>(endpoint: string) => request<T>(endpoint, { method: 'GET' })

  const post = <T>(endpoint: string, body: unknown) =>
    request<T>(endpoint, { method: 'POST', body: JSON.stringify(body) })

  const put = <T>(endpoint: string, body: unknown) =>
    request<T>(endpoint, { method: 'PUT', body: JSON.stringify(body) })

  const patch = <T>(endpoint: string, body?: unknown) =>
    request<T>(endpoint, { method: 'PATCH', body: body ? JSON.stringify(body) : undefined })

  const del = <T>(endpoint: string) => request<T>(endpoint, { method: 'DELETE' })

  return { get, post, put, patch, del }
}
