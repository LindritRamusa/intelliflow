<script setup lang="ts">
defineProps<{
  open: boolean
  title: string
  description?: string
  confirmLabel?: string
  danger?: boolean
  loading?: boolean
}>()

const emit = defineEmits<{
  confirm: []
  cancel: []
}>()
</script>

<template>
  <Transition name="fade">
    <div v-if="open" class="fixed inset-0 z-50 flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="emit('cancel')" />
      <div class="relative glass-panel w-full max-w-md p-6 shadow-2xl">
        <div class="flex items-start gap-4 mb-5">
          <div
            class="flex h-10 w-10 items-center justify-center rounded-xl flex-shrink-0"
            :class="danger ? 'bg-red-500/15' : 'bg-brand-500/15'"
          >
            <svg class="h-5 w-5" :class="danger ? 'text-red-400' : 'text-brand-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path v-if="danger" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div>
            <h3 class="text-sm font-semibold text-white">{{ title }}</h3>
            <p v-if="description" class="text-xs text-slate-400 mt-1">{{ description }}</p>
          </div>
        </div>

        <div class="flex items-center justify-end gap-2">
          <button class="btn-secondary text-xs" :disabled="loading" @click="emit('cancel')">
            Cancel
          </button>
          <button
            :class="danger ? 'inline-flex items-center gap-2 rounded-lg bg-red-600 px-4 py-2 text-xs font-medium text-white hover:bg-red-700 transition-colors disabled:opacity-50' : 'btn-primary text-xs'"
            :disabled="loading"
            @click="emit('confirm')"
          >
            <LoadingSpinner v-if="loading" size="xs" />
            {{ confirmLabel ?? 'Confirm' }}
          </button>
        </div>
      </div>
    </div>
  </Transition>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.15s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
