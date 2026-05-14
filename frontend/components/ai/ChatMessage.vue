<script setup lang="ts">
import type { ChatMessage } from '~/types'

defineProps<{
  message: ChatMessage
}>()
</script>

<template>
  <div
    class="flex gap-3"
    :class="message.role === 'user' ? 'flex-row-reverse' : 'flex-row'"
  >
    <!-- Avatar -->
    <div
      class="flex h-7 w-7 items-center justify-center rounded-full flex-shrink-0 mt-0.5"
      :class="message.role === 'user' ? 'bg-brand-600' : 'bg-surface-600 border border-surface-500'"
    >
      <svg v-if="message.role === 'assistant'" class="h-3.5 w-3.5 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
      </svg>
      <svg v-else class="h-3.5 w-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
      </svg>
    </div>

    <!-- Bubble -->
    <div
      class="max-w-[75%] rounded-2xl px-4 py-3 text-sm leading-relaxed"
      :class="
        message.role === 'user'
          ? 'bg-brand-600 text-white rounded-tr-sm'
          : 'bg-surface-700 border border-surface-600 text-slate-200 rounded-tl-sm'
      "
    >
      <!-- Loading dots -->
      <div v-if="message.isLoading" class="flex items-center gap-1 py-1">
        <span class="h-1.5 w-1.5 rounded-full bg-slate-400 animate-bounce" style="animation-delay: 0ms" />
        <span class="h-1.5 w-1.5 rounded-full bg-slate-400 animate-bounce" style="animation-delay: 150ms" />
        <span class="h-1.5 w-1.5 rounded-full bg-slate-400 animate-bounce" style="animation-delay: 300ms" />
      </div>

      <template v-else>
        <!-- Render markdown-style content with basic formatting -->
        <div class="whitespace-pre-wrap" v-html="message.content.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>').replace(/\*(.*?)\*/g, '<em>$1</em>')" />
        <p class="text-[10px] mt-1.5 opacity-50 text-right">
          {{ message.timestamp.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) }}
        </p>
      </template>
    </div>
  </div>
</template>
