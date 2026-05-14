<script setup lang="ts">
const props = defineProps<{
  disabled?: boolean
  placeholder?: string
}>()

const emit = defineEmits<{ send: [message: string] }>()

const input = ref('')
const textareaRef = ref<HTMLTextAreaElement>()

const handleSend = () => {
  const trimmed = input.value.trim()
  if (!trimmed || props.disabled) return
  emit('send', trimmed)
  input.value = ''
  nextTick(() => {
    if (textareaRef.value) {
      textareaRef.value.style.height = 'auto'
    }
  })
}

const handleKeydown = (e: KeyboardEvent) => {
  if (e.key === 'Enter' && !e.shiftKey) {
    e.preventDefault()
    handleSend()
  }
}

const autoResize = () => {
  if (!textareaRef.value) return
  textareaRef.value.style.height = 'auto'
  textareaRef.value.style.height = `${Math.min(textareaRef.value.scrollHeight, 160)}px`
}
</script>

<template>
  <div class="flex items-end gap-3 bg-surface-700 border border-surface-600 rounded-xl p-3 focus-within:border-brand-500 transition-colors">
    <textarea
      ref="textareaRef"
      v-model="input"
      :placeholder="placeholder ?? 'Ask anything about your workflows, operations, or data...'"
      :disabled="disabled"
      rows="1"
      class="flex-1 bg-transparent text-sm text-slate-200 placeholder-slate-500 resize-none focus:outline-none disabled:opacity-50 leading-relaxed"
      style="min-height: 24px; max-height: 160px"
      @keydown="handleKeydown"
      @input="autoResize"
    />

    <button
      class="flex h-8 w-8 items-center justify-center rounded-lg flex-shrink-0 transition-all"
      :class="
        input.trim() && !disabled
          ? 'bg-brand-600 hover:bg-brand-700 text-white'
          : 'bg-surface-600 text-slate-500 cursor-not-allowed'
      "
      :disabled="!input.trim() || disabled"
      @click="handleSend"
    >
      <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
      </svg>
    </button>
  </div>
</template>
