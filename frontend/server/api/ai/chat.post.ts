import OpenAI from 'openai'

// OpenRouter is OpenAI-compatible — no extra packages needed.
// Free models: meta-llama/llama-3.3-70b-instruct:free, google/gemma-3-27b-it:free, deepseek/deepseek-r1:free
const FREE_MODEL = 'meta-llama/llama-3.3-70b-instruct:free'

export default defineEventHandler(async (event) => {
  const config = useRuntimeConfig()
  const body = await readBody(event) as {
    messages: Array<{ role: 'user' | 'assistant'; content: string }>
    system?: string
  }

  if (!config.openrouterApiKey || config.openrouterApiKey === 'your_openrouter_api_key') {
    return {
      message: "AI features require an OpenRouter API key. Add `OPENROUTER_API_KEY` to `frontend/.env` and restart. Get a free key (no card needed) at openrouter.ai/keys.",
      usage: null,
    }
  }

  if (!body.messages?.length) {
    throw createError({ statusCode: 400, message: 'Messages are required' })
  }

  const systemPrompt = body.system ?? `You are IntelliFlow AI — an enterprise automation copilot. You help business teams:
- Design and optimize workflow automations
- Analyze business performance data and surface insights
- Suggest process improvements and efficiency gains
- Answer questions about operations, logistics, and HR

Be concise, professional, and actionable. Use bullet points for lists. Keep responses under 400 words unless asked for more detail.`

  try {
    const client = new OpenAI({
      baseURL: 'https://openrouter.ai/api/v1',
      apiKey: config.openrouterApiKey,
      defaultHeaders: {
        'HTTP-Referer': 'http://localhost:3000',
        'X-Title': 'IntelliFlow AI Platform',
      },
    })

    const completion = await client.chat.completions.create({
      model: FREE_MODEL,
      messages: [
        { role: 'system', content: systemPrompt },
        ...body.messages,
      ],
      max_tokens: 1024,
      temperature: 0.7,
    })

    return {
      message: completion.choices[0]?.message?.content ?? '',
      usage: completion.usage,
    }
  } catch (err: unknown) {
    const error = err as { status?: number; code?: string; message?: string }

    if (error.status === 429) {
      return {
        message: "⚠️ Rate limit reached. Free models on OpenRouter allow limited requests per minute. Please wait a moment and try again.",
        usage: null,
      }
    }

    if (error.status === 401) {
      return {
        message: "⚠️ Invalid OpenRouter API key. Check your `OPENROUTER_API_KEY` in `frontend/.env`. Get a free key at openrouter.ai/keys.",
        usage: null,
      }
    }

    if (error.status === 402) {
      return {
        message: "⚠️ OpenRouter credits exhausted. Switch to a free model or add credits at openrouter.ai/credits. Free models include: meta-llama/llama-3.3-70b-instruct:free",
        usage: null,
      }
    }

    return {
      message: `⚠️ AI service error: ${error.message ?? 'Unknown error'}. Please try again.`,
      usage: null,
    }
  }
})
