import type { Article, CreateArticlePayload, ApiResponse } from '~/types'

export const useArticles = () => {
  const { api } = useApi()

  const articles = ref<Article[]>([])
  const categories = ref<string[]>([])
  const isLoading = ref(false)
  const error = ref<string | null>(null)
  const total = ref(0)

  const fetchArticles = async (params: { search?: string; category?: string; published?: boolean } = {}) => {
    isLoading.value = true
    error.value = null
    try {
      const query = new URLSearchParams()
      if (params.search) query.set('search', params.search)
      if (params.category && params.category !== 'All') query.set('category', params.category)
      if (params.published) query.set('published', '1')

      const res = await api<ApiResponse<Article[]>>(`/articles?${query.toString()}`)
      articles.value = res.data
      total.value = res.meta?.total ?? res.data.length
    } catch (e: unknown) {
      error.value = (e as Error).message
    } finally {
      isLoading.value = false
    }
  }

  const fetchCategories = async () => {
    try {
      const res = await api<{ data: string[] }>('/articles/categories')
      categories.value = ['All', ...res.data]
    } catch {}
  }

  const createArticle = async (payload: CreateArticlePayload): Promise<Article | null> => {
    try {
      const res = await api<{ data: Article }>('/articles', { method: 'POST', body: payload })
      articles.value.unshift(res.data)
      total.value++
      if (res.data.category && !categories.value.includes(res.data.category)) {
        categories.value.push(res.data.category)
      }
      return res.data
    } catch (e: unknown) {
      error.value = (e as Error).message
      return null
    }
  }

  const updateArticle = async (id: string, payload: Partial<CreateArticlePayload>): Promise<Article | null> => {
    try {
      const res = await api<{ data: Article }>(`/articles/${id}`, { method: 'PUT', body: payload })
      const idx = articles.value.findIndex(a => a.id === id)
      if (idx !== -1) articles.value[idx] = res.data
      return res.data
    } catch (e: unknown) {
      error.value = (e as Error).message
      return null
    }
  }

  const deleteArticle = async (id: string): Promise<boolean> => {
    try {
      await api(`/articles/${id}`, { method: 'DELETE' })
      articles.value = articles.value.filter(a => a.id !== id)
      total.value--
      return true
    } catch (e: unknown) {
      error.value = (e as Error).message
      return false
    }
  }

  const togglePublish = async (article: Article): Promise<boolean> => {
    const result = await updateArticle(article.id, { published: !article.published })
    return result !== null
  }

  return {
    articles: readonly(articles),
    categories: readonly(categories),
    isLoading: readonly(isLoading),
    error: readonly(error),
    total: readonly(total),
    fetchArticles,
    fetchCategories,
    createArticle,
    updateArticle,
    deleteArticle,
    togglePublish,
  }
}
