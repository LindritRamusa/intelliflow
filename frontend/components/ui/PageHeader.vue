<script setup lang="ts">
defineProps<{
  title: string
  description?: string
}>()
</script>

<template>
  <div class="flex items-start justify-between mb-6 gap-4">
    <div>
      <h2 class="text-xl font-bold text-white">{{ title }}</h2>
      <p v-if="description" class="text-sm text-slate-400 mt-0.5">{{ description }}</p>
    </div>
    <div class="flex items-center gap-2 flex-shrink-0">
      <slot />
    </div>
  </div>
</template>
