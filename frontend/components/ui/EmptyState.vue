<script setup lang="ts">
defineProps<{
  icon?: string
  title: string
  description?: string
  actionLabel?: string
  actionTo?: string
}>()

const emit = defineEmits<{ action: [] }>()
</script>

<template>
  <div class="flex flex-col items-center justify-center py-16 px-6 text-center">
    <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-surface-700 border border-surface-600">
      <svg
        class="h-6 w-6 text-slate-500"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="1.5"
          :d="icon ?? 'M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4'"
        />
      </svg>
    </div>
    <h3 class="text-sm font-semibold text-slate-200 mb-1">{{ title }}</h3>
    <p v-if="description" class="text-xs text-slate-500 max-w-xs mb-5">{{ description }}</p>
    <template v-if="actionLabel">
      <NuxtLink v-if="actionTo" :to="actionTo" class="btn-primary text-xs">
        {{ actionLabel }}
      </NuxtLink>
      <button v-else class="btn-primary text-xs" @click="emit('action')">
        {{ actionLabel }}
      </button>
    </template>
  </div>
</template>
