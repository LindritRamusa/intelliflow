import type { ChatMessage, AiLog } from '~/types'

export const useAiChat = () => {
  const api = useApi()
  const messages = ref<ChatMessage[]>([])
  const isStreaming = ref(false)
  const error = ref<string | null>(null)

  const systemContext = `You are IntelliFlow AI — an enterprise automation copilot. You help teams:
- Design and optimize workflow automations
- Analyze business performance data and surface insights
- Suggest process improvements and efficiency gains
- Answer questions about business operations, logistics, and HR

Be concise, professional, data-driven, and actionable. Use bullet points for lists. Keep responses under 400 words unless asked for detail.`

  const sendMessage = async (content: string): Promise<void> => {
    if (!content.trim() || isStreaming.value) return

    const userMessage: ChatMessage = {
      id: crypto.randomUUID(),
      role: 'user',
      content: content.trim(),
      timestamp: new Date(),
    }
    messages.value.push(userMessage)

    const loadingMessage: ChatMessage = {
      id: crypto.randomUUID(),
      role: 'assistant',
      content: '',
      timestamp: new Date(),
      isLoading: true,
    }
    messages.value.push(loadingMessage)
    isStreaming.value = true
    error.value = null

    const historyForApi = messages.value
      .filter((m) => !m.isLoading)
      .map((m) => ({ role: m.role, content: m.content }))

    try {
      const response = await $fetch<{ message: string; usage?: { total_tokens: number } }>(
        '/api/ai/chat',
        {
          method: 'POST',
          body: {
            messages: historyForApi,
            system: systemContext,
          },
        },
      )

      const idx = messages.value.findIndex((m) => m.id === loadingMessage.id)
      if (idx >= 0) {
        messages.value[idx] = {
          ...loadingMessage,
          content: response.message,
          isLoading: false,
        }
      }

      await api.post<AiLog>('/ai/logs', {
        prompt: content,
        response: response.message,
        model: 'meta-llama/llama-3.3-70b-instruct',
        tokens_used: 0,
      })
    } catch (e) {
      const idx = messages.value.findIndex((m) => m.id === loadingMessage.id)
      if (idx >= 0) messages.value.splice(idx, 1)
      error.value = e instanceof Error ? e.message : 'Failed to get AI response'
    } finally {
      isStreaming.value = false
    }
  }

  const clearMessages = (): void => {
    messages.value = []
    error.value = null
  }

  return { messages, isStreaming, error, sendMessage, clearMessages }
}
