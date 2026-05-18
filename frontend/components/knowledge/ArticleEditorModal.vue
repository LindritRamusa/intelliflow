<script setup lang="ts">
import type { Article, CreateArticlePayload } from '~/types'

const props = defineProps<{
  article?: Article | null
}>()

const emit = defineEmits<{
  close: []
  submit: [payload: CreateArticlePayload]
}>()

const isSubmitting = ref(false)

const defaultCategories = ['General', 'Engineering', 'HR', 'Finance', 'Operations', 'Sales', 'Marketing', 'Product', 'Legal']

const form = reactive<CreateArticlePayload>({
  title: props.article?.title ?? '',
  content: props.article?.content ?? '',
  excerpt: props.article?.excerpt ?? '',
  category: props.article?.category ?? 'General',
  published: props.article?.published ?? false,
})

const isEditing = computed(() => !!props.article)

const wordCount = computed(() => form.content.trim().split(/\s+/).filter(Boolean).length)

const handleSubmit = async () => {
  if (!form.title.trim() || !form.content.trim()) return
  isSubmitting.value = true
  try {
    emit('submit', { ...form })
  } finally {
    isSubmitting.value = false
  }
}
</script>

<template>
  <div class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-[#141824] border border-white/10 rounded-2xl w-full max-w-3xl max-h-[92vh] overflow-y-auto shadow-2xl">
      <div class="flex items-center justify-between p-6 border-b border-white/[0.06]">
        <div>
          <h2 class="text-white font-semibold text-lg">{{ isEditing ? 'Edit Article' : 'New Article' }}</h2>
          <p class="text-gray-400 text-sm mt-0.5">{{ isEditing ? 'Update knowledge base article' : 'Create a new knowledge base article' }}</p>
        </div>
        <button class="p-2 rounded-lg text-gray-400 hover:text-white hover:bg-white/5 transition-colors" @click="emit('close')">
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>

      <form class="p-6 space-y-5" @submit.prevent="handleSubmit">
        <div>
          <label class="block text-sm text-gray-300 mb-1.5">Title <span class="text-red-400">*</span></label>
          <input
            v-model="form.title"
            type="text"
            class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2.5 text-white text-sm placeholder-gray-500 focus:outline-none focus:border-violet-500/50 focus:bg-white/[0.07] transition-colors"
            placeholder="Article title..."
            required
          />
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm text-gray-300 mb-1.5">Category <span class="text-red-400">*</span></label>
            <select
              v-model="form.category"
              class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2.5 text-white text-sm focus:outline-none focus:border-violet-500/50 transition-colors"
            >
              <option v-for="cat in defaultCategories" :key="cat" :value="cat" class="bg-[#1a1f2e]">{{ cat }}</option>
            </select>
          </div>
          <div class="flex items-end pb-1">
            <label class="flex items-center gap-3 cursor-pointer">
              <div
                :class="['relative w-11 h-6 rounded-full transition-colors', form.published ? 'bg-violet-600' : 'bg-white/10']"
                @click="form.published = !form.published"
              >
                <div :class="['absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform', form.published ? 'translate-x-5' : 'translate-x-0']" />
              </div>
              <span class="text-sm text-gray-300">Publish immediately</span>
            </label>
          </div>
        </div>

        <div>
          <label class="block text-sm text-gray-300 mb-1.5">Excerpt</label>
          <input
            v-model="form.excerpt"
            type="text"
            class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2.5 text-white text-sm placeholder-gray-500 focus:outline-none focus:border-violet-500/50 focus:bg-white/[0.07] transition-colors"
            placeholder="Short summary (auto-generated if empty)..."
          />
        </div>

        <div>
          <div class="flex items-center justify-between mb-1.5">
            <label class="text-sm text-gray-300">Content <span class="text-red-400">*</span></label>
            <span class="text-xs text-gray-500">{{ wordCount }} words</span>
          </div>
          <textarea
            v-model="form.content"
            rows="14"
            class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2.5 text-white text-sm placeholder-gray-500 focus:outline-none focus:border-violet-500/50 focus:bg-white/[0.07] transition-colors resize-none font-mono"
            placeholder="Write your article content here. Markdown is supported..."
            required
          />
        </div>

        <div class="flex items-center gap-3 pt-2">
          <button
            type="button"
            class="flex-1 px-4 py-2.5 bg-white/5 border border-white/10 rounded-lg text-gray-300 text-sm hover:bg-white/[0.08] transition-colors"
            @click="emit('close')"
          >
            Cancel
          </button>
          <button
            type="submit"
            :disabled="isSubmitting || !form.title.trim() || !form.content.trim()"
            class="flex-1 px-4 py-2.5 bg-violet-600 hover:bg-violet-500 disabled:opacity-50 rounded-lg text-white text-sm font-medium transition-colors"
          >
            {{ isSubmitting ? 'Saving...' : isEditing ? 'Save Changes' : 'Create Article' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
