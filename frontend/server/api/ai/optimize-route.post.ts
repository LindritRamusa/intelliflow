import OpenAI from 'openai'

const MODEL = 'meta-llama/llama-3.3-70b-instruct:free'

export default defineEventHandler(async (event) => {
  const config = useRuntimeConfig()
  const body = await readBody(event) as {
    name: string
    origin: string
    destination: string
    cargoType?: string
    cargoWeightKg?: number
    priority?: string
    estimatedArrival?: string
  }

  if (!config.openrouterApiKey || config.openrouterApiKey === 'your_openrouter_api_key') {
    return {
      waypoints: [body.origin, body.destination],
      estimatedHours: null,
      distanceKm: null,
      costEstimate: null,
      notes: 'AI optimization requires an OpenRouter API key.',
      risks: [],
      recommendations: [],
    }
  }

  const client = new OpenAI({
    baseURL: 'https://openrouter.ai/api/v1',
    apiKey: config.openrouterApiKey,
    defaultHeaders: {
      'HTTP-Referer': 'http://localhost:3000',
      'X-Title': 'IntelliFlow AI Platform',
    },
  })

  const prompt = `You are a logistics optimization expert. Analyze this shipment and provide an optimized route plan.

Shipment Details:
- Name: ${body.name}
- Origin: ${body.origin}
- Destination: ${body.destination}
- Cargo Type: ${body.cargoType ?? 'General'}
- Cargo Weight: ${body.cargoWeightKg ? `${body.cargoWeightKg} kg` : 'Unknown'}
- Priority: ${body.priority ?? 'normal'}
- Required by: ${body.estimatedArrival ?? 'Flexible'}

Provide a response in valid JSON only (no markdown):
{
  "waypoints": [<list of 2-5 location strings from origin to destination>],
  "estimatedHours": <number>,
  "distanceKm": <number>,
  "costEstimate": <number in USD>,
  "notes": "<2-3 sentence route summary>",
  "risks": [<1-3 risk factor strings>],
  "recommendations": [<2-3 optimization tip strings>]
}`

  try {
    const completion = await client.chat.completions.create({
      model: MODEL,
      messages: [{ role: 'user', content: prompt }],
      max_tokens: 600,
      temperature: 0.3,
    })

    const content = completion.choices[0]?.message?.content ?? '{}'
    const jsonMatch = content.match(/\{[\s\S]*\}/)
    if (!jsonMatch) {
      return { waypoints: [body.origin, body.destination], notes: content, risks: [], recommendations: [] }
    }

    const parsed = JSON.parse(jsonMatch[0])
    return {
      waypoints: Array.isArray(parsed.waypoints) ? parsed.waypoints : [body.origin, body.destination],
      estimatedHours: Number(parsed.estimatedHours) || null,
      distanceKm: Number(parsed.distanceKm) || null,
      costEstimate: Number(parsed.costEstimate) || null,
      notes: String(parsed.notes || ''),
      risks: Array.isArray(parsed.risks) ? parsed.risks : [],
      recommendations: Array.isArray(parsed.recommendations) ? parsed.recommendations : [],
    }
  } catch (err: unknown) {
    const e = err as { status?: number; message?: string }
    if (e.status === 429) {
      return { waypoints: [body.origin, body.destination], notes: '⚠️ Rate limit reached. Please wait and try again.', risks: [], recommendations: [] }
    }
    return { waypoints: [body.origin, body.destination], notes: `⚠️ Optimization failed: ${e.message ?? 'Unknown error'}`, risks: [], recommendations: [] }
  }
})
