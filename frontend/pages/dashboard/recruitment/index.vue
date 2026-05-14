<script setup lang="ts">
definePageMeta({ layout: 'dashboard', middleware: 'auth' })

const { messages, isStreaming, sendMessage } = useAiChat()

const candidates = ref([
  { id: '1', name: 'Sarah Chen', role: 'Senior Engineer', score: 94, skills: ['TypeScript', 'React', 'Node.js'], status: 'shortlisted' },
  { id: '2', name: 'Marcus Weber', role: 'Backend Developer', score: 87, skills: ['PHP', 'Laravel', 'PostgreSQL'], status: 'review' },
  { id: '3', name: 'Aisha Nkomo', role: 'Full Stack Developer', score: 81, skills: ['Vue.js', 'Python', 'Docker'], status: 'review' },
  { id: '4', name: 'Liam Thornton', role: 'DevOps Engineer', score: 76, skills: ['Kubernetes', 'AWS', 'Terraform'], status: 'pending' },
])

const statusColors: Record<string, string> = {
  shortlisted: 'badge-green',
  review: 'badge-blue',
  pending: 'badge-amber',
  rejected: 'badge-red',
}

const recruitmentPrompts = [
  'Generate interview questions for a Senior React Engineer',
  'What skills should I prioritize for a DevOps role?',
  'Compare these candidates for cultural fit',
  'Create an onboarding checklist for a backend developer',
]

const handleAiQuery = (prompt: string) => {
  sendMessage(prompt)
}

const cvAnalysisInput = ref('')
const isAnalyzing = ref(false)
const analysisResult = ref('')

const handleAnalyzeCV = async () => {
  if (!cvAnalysisInput.value.trim()) return
  isAnalyzing.value = true
  await sendMessage(`Analyze this CV and rate it on a scale of 1-10, listing key strengths and weaknesses:\n\n${cvAnalysisInput.value}`)
  isAnalyzing.value = false
  const lastMsg = messages.value.filter(m => m.role === 'assistant').at(-1)
  if (lastMsg) analysisResult.value = lastMsg.content
}
</script>

<template>
  <div>
    <PageHeader title="Recruitment AI" description="AI-powered candidate screening and hiring intelligence" />

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
      <!-- Candidate Pipeline -->
      <div class="xl:col-span-2 space-y-4">
        <div class="card">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-white">Candidate Pipeline</h3>
            <span class="badge-blue">{{ candidates.length }} candidates</span>
          </div>

          <div class="space-y-3">
            <div
              v-for="c in candidates"
              :key="c.id"
              class="flex items-center gap-4 p-3 rounded-xl border border-surface-600 bg-surface-700/30 hover:bg-surface-700/50 transition-colors"
            >
              <!-- Avatar -->
              <div class="h-9 w-9 rounded-full bg-brand-600/20 flex items-center justify-center flex-shrink-0">
                <span class="text-sm font-semibold text-brand-300">{{ c.name.charAt(0) }}</span>
              </div>

              <!-- Info -->
              <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2">
                  <p class="text-sm font-medium text-slate-200">{{ c.name }}</p>
                  <span :class="statusColors[c.status]">{{ c.status }}</span>
                </div>
                <p class="text-xs text-slate-500">{{ c.role }}</p>
                <div class="flex items-center gap-1.5 mt-1.5 flex-wrap">
                  <span
                    v-for="skill in c.skills"
                    :key="skill"
                    class="text-[10px] px-1.5 py-0.5 rounded-md bg-surface-600 text-slate-400 font-mono"
                  >
                    {{ skill }}
                  </span>
                </div>
              </div>

              <!-- Score -->
              <div class="flex flex-col items-center flex-shrink-0">
                <div class="text-lg font-bold" :class="c.score >= 90 ? 'text-emerald-400' : c.score >= 80 ? 'text-brand-400' : 'text-amber-400'">
                  {{ c.score }}
                </div>
                <p class="text-[10px] text-slate-500">AI Score</p>
              </div>
            </div>
          </div>
        </div>

        <!-- CV Analyzer -->
        <div class="card">
          <h3 class="text-sm font-semibold text-white mb-1">CV Analyzer</h3>
          <p class="text-xs text-slate-500 mb-4">Paste a CV below for instant AI analysis and scoring</p>

          <textarea
            v-model="cvAnalysisInput"
            class="input resize-none mb-3"
            rows="5"
            placeholder="Paste candidate CV text here..."
          />

          <button
            class="btn-primary text-xs"
            :disabled="!cvAnalysisInput.trim() || isAnalyzing || isStreaming"
            @click="handleAnalyzeCV"
          >
            <LoadingSpinner v-if="isAnalyzing" size="xs" />
            <svg v-else class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
            </svg>
            Analyze with AI
          </button>

          <div v-if="analysisResult" class="mt-4 p-4 rounded-xl bg-surface-700/50 border border-brand-500/20">
            <p class="text-[11px] font-medium text-brand-400 mb-2 uppercase tracking-wider">AI Analysis Result</p>
            <p class="text-xs text-slate-300 whitespace-pre-wrap leading-relaxed">{{ analysisResult }}</p>
          </div>
        </div>
      </div>

      <!-- AI Recruitment Assistant -->
      <div class="card flex flex-col">
        <h3 class="text-sm font-semibold text-white mb-1">AI Hiring Assistant</h3>
        <p class="text-xs text-slate-500 mb-4">Get instant insights on hiring decisions</p>

        <div class="space-y-2 mb-4">
          <button
            v-for="prompt in recruitmentPrompts"
            :key="prompt"
            class="w-full text-left text-xs px-3 py-2.5 rounded-lg border border-surface-600 bg-surface-700/50 hover:bg-surface-700 hover:border-brand-500/30 text-slate-400 hover:text-slate-200 transition-all"
            @click="handleAiQuery(prompt)"
          >
            {{ prompt }}
          </button>
        </div>

        <NuxtLink to="/dashboard/ai-assistant" class="btn-secondary text-xs justify-center mt-auto">
          Open Full AI Assistant
        </NuxtLink>
      </div>
    </div>
  </div>
</template>
