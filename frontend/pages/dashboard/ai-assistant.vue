<script setup lang="ts">
definePageMeta({ layout: 'dashboard', middleware: 'auth' })

const { messages, isStreaming, error, sendMessage, clearMessages } = useAiChat()

const messagesEndRef = ref<HTMLElement>()
const chatContainerRef = ref<HTMLElement>()

const suggestedPrompts = [
  'Summarize pending workflow bottlenecks',
  'What automations should I set up for daily reporting?',
  'Analyze our most common operational inefficiencies',
  'Generate a recruitment screening checklist for a senior engineer',
  'How can I optimize our logistics scheduling?',
  'What KPIs should I track for team productivity?',
]

const scrollToBottom = () => {
  nextTick(() => {
    if (messagesEndRef.value) {
      messagesEndRef.value.scrollIntoView({ behavior: 'smooth' })
    }
  })
}

watch(messages, scrollToBottom, { deep: true })

const handleSend = (msg: string) => {
  sendMessage(msg)
}

const handleSuggestedPrompt = (prompt: string) => {
  sendMessage(prompt)
}
</script>

<template>
  <div class="flex flex-col h-[calc(100vh-theme(spacing.16)-theme(spacing.12))]">
    <!-- Header -->
    <div class="flex items-center justify-between mb-4 flex-shrink-0">
      <div>
        <h2 class="text-lg font-bold text-white flex items-center gap-2">
          <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse" />
          IntelliFlow AI
        </h2>
        <p class="text-xs text-slate-500">Enterprise automation copilot · Llama 3.3 70B via OpenRouter</p>
      </div>
      <button
        v-if="messages.length"
        class="flex items-center gap-1.5 text-xs text-slate-400 hover:text-slate-200 transition-colors px-3 py-1.5 rounded-lg hover:bg-surface-700"
        @click="clearMessages"
      >
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
        </svg>
        Clear
      </button>
    </div>

    <!-- Chat area -->
    <div
      ref="chatContainerRef"
      class="flex-1 overflow-y-auto card mb-4 p-4 space-y-4 min-h-0"
    >
      <!-- Welcome screen -->
      <div v-if="!messages.length" class="flex flex-col items-center justify-center h-full py-8 text-center">
        <div class="h-16 w-16 rounded-2xl bg-brand-600/15 border border-brand-500/20 flex items-center justify-center mb-4">
          <svg class="h-8 w-8 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
          </svg>
        </div>
        <h3 class="text-base font-semibold text-white mb-2">How can I help today?</h3>
        <p class="text-sm text-slate-400 max-w-md mb-6">
          I can analyze your workflows, surface business insights, assist with recruitment, optimize logistics, and more.
        </p>

        <!-- Suggested prompts -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 w-full max-w-xl">
          <button
            v-for="prompt in suggestedPrompts"
            :key="prompt"
            class="text-left px-4 py-3 rounded-xl border border-surface-600 bg-surface-700/50 hover:bg-surface-700 hover:border-brand-500/30 transition-all text-xs text-slate-400 hover:text-slate-200"
            @click="handleSuggestedPrompt(prompt)"
          >
            {{ prompt }}
          </button>
        </div>
      </div>

      <!-- Messages -->
      <template v-else>
        <ChatMessage
          v-for="message in messages"
          :key="message.id"
          :message="message"
        />
        <div ref="messagesEndRef" />
      </template>
    </div>

    <!-- Error -->
    <div
      v-if="error"
      class="mb-3 flex items-center gap-2 px-3 py-2.5 rounded-lg bg-red-500/10 border border-red-500/20 flex-shrink-0"
    >
      <svg class="h-3.5 w-3.5 text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      <p class="text-xs text-red-300">{{ error }}</p>
    </div>

    <!-- Input -->
    <div class="flex-shrink-0">
      <ChatInput :disabled="isStreaming" @send="handleSend" />
      <p class="text-[10px] text-slate-600 mt-2 text-center">
        IntelliFlow AI may make mistakes. Verify important information independently.
      </p>
    </div>
  </div>
</template>
