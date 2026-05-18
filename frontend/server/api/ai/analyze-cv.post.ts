import OpenAI from 'openai'

const MODEL = 'meta-llama/llama-3.3-70b-instruct:free'

export default defineEventHandler(async (event) => {
  const config = useRuntimeConfig()
  const body = await readBody(event) as { cvText: string; role?: string }

  if (!config.openrouterApiKey || config.openrouterApiKey === 'your_openrouter_api_key') {
    return {
      score: 0,
      strengths: [],
      weaknesses: [],
      skills: [],
      assessment: 'AI analysis requires an OpenRouter API key. Add OPENROUTER_API_KEY to frontend/.env.',
      recommendation: 'review',
    }
  }

  if (!body.cvText?.trim()) {
    throw createError({ statusCode: 400, message: 'CV text is required for analysis' })
  }

  const client = new OpenAI({
    baseURL: 'https://openrouter.ai/api/v1',
    apiKey: config.openrouterApiKey,
    defaultHeaders: {
      'HTTP-Referer': 'http://localhost:3000',
      'X-Title': 'IntelliFlow AI Platform',
    },
  })

  const prompt = `You are an expert HR recruiter and talent acquisition specialist. Analyze the following CV/resume objectively.

Target Role: ${body.role || 'General position'}

CV Content:
---
${body.cvText.slice(0, 3000)}
---

Provide a structured analysis in valid JSON only (no markdown, no extra text):
{
  "score": <integer 0-100>,
  "strengths": [<3-5 specific strength strings>],
  "weaknesses": [<2-3 specific gap/weakness strings>],
  "skills": [<list of extracted technical and soft skills>],
  "assessment": "<2-3 sentence professional suitability assessment>",
  "recommendation": "<exactly one of: shortlist, review, or reject>"
}`

  try {
    const completion = await client.chat.completions.create({
      model: MODEL,
      messages: [{ role: 'user', content: prompt }],
      max_tokens: 800,
      temperature: 0.2,
    })

    const content = completion.choices[0]?.message?.content ?? '{}'
    const jsonMatch = content.match(/\{[\s\S]*\}/)
    if (!jsonMatch) {
      return { score: 65, strengths: [], weaknesses: [], skills: [], assessment: content, recommendation: 'review' }
    }

    const parsed = JSON.parse(jsonMatch[0])
    return {
      score: Math.min(100, Math.max(0, Number(parsed.score) || 65)),
      strengths: Array.isArray(parsed.strengths) ? parsed.strengths : [],
      weaknesses: Array.isArray(parsed.weaknesses) ? parsed.weaknesses : [],
      skills: Array.isArray(parsed.skills) ? parsed.skills : [],
      assessment: String(parsed.assessment || ''),
      recommendation: ['shortlist', 'review', 'reject'].includes(parsed.recommendation)
        ? parsed.recommendation
        : 'review',
    }
  } catch (err: unknown) {
    const e = err as { status?: number; message?: string }
    if (e.status === 429) {
      return { score: 0, strengths: [], weaknesses: [], skills: [], assessment: '⚠️ Rate limit reached. Please wait a moment and try again.', recommendation: 'review' }
    }
    return { score: 0, strengths: [], weaknesses: [], skills: [], assessment: `⚠️ Analysis failed: ${e.message ?? 'Unknown error'}`, recommendation: 'review' }
  }
})
