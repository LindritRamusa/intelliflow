<script setup lang="ts">
import type { Article } from '~/types'

defineProps<{
  article: Article
}>()

const emit = defineEmits<{
  edit: [article: Article]
  delete: [id: string]
  togglePublish: [article: Article]
  view: [article: Article]
}>()
</script>

<template>
  <div class="bg-[#1a1f2e] border border-white/[0.06] rounded-xl p-5 hover:border-white/10 transition-colors group flex flex-col">
    <div class="flex items-start justify-between gap-3 mb-3">
      <span class="px-2 py-0.5 bg-violet-500/10 border border-violet-500/20 rounded-full text-xs text-violet-400 shrink-0">
        {{ article.category }}
      </span>
      <span :class="['px-2 py-0.5 rounded-full text-xs font-medium shrink-0', article.published ? 'bg-emerald-500/15 text-emerald-400' : 'bg-gray-500/15 text-gray-400']">
        {{ article.published ? 'Published' : 'Draft' }}
      </span>
    </div>

    <button
      class="text-white font-medium text-sm text-left hover:text-violet-300 transition-colors mb-2 line-clamp-2"
      @click="emit('view', article)"
    >
      {{ article.title }}
    </button>

    <p v-if="article.excerpt" class="text-gray-400 text-xs line-clamp-2 mb-4 flex-1">
      {{ article.excerpt }}
    </p>
    <div v-else class="flex-1" />

    <div class="flex items-center justify-between pt-3 border-t border-white/[0.04]">
      <div class="flex items-center gap-3 text-xs text-gray-500">
        <span class="flex items-center gap-1">
          <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
          </svg>
          {{ article.views }}
        </span>
        <span>{{ article.updatedAgo ?? 'just now' }}</span>
      </div>
      <div class="flex items-center gap-1">
        <button
          class="p-1.5 rounded-lg text-gray-500 hover:text-violet-400 hover:bg-violet-500/10 transition-colors"
          :title="article.published ? 'Unpublish' : 'Publish'"
          @click="emit('togglePublish', article)"
        >
          <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
          </svg>
        </button>
        <button
          class="p-1.5 rounded-lg text-gray-500 hover:text-blue-400 hover:bg-blue-500/10 transition-colors"
          @click="emit('edit', article)"
        >
          <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
          </svg>
        </button>
        <button
          class="p-1.5 rounded-lg text-gray-500 hover:text-red-400 hover:bg-red-500/10 transition-colors"
          @click="emit('delete', article.id)"
        >
          <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
          </svg>
        </button>
      </div>
    </div>
  </div>
</template>
