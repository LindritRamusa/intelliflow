<script setup lang="ts">
const props = defineProps<{
  status: string
  dot?: boolean
}>()

const config: Record<string, { label: string; badgeClass: string; dotClass: string }> = {
  active: { label: 'Active', badgeClass: 'badge-green', dotClass: 'bg-emerald-400' },
  running: { label: 'Running', badgeClass: 'badge-blue', dotClass: 'bg-brand-400' },
  paused: { label: 'Paused', badgeClass: 'badge-amber', dotClass: 'bg-amber-400' },
  draft: { label: 'Draft', badgeClass: 'badge bg-slate-700 text-slate-400', dotClass: 'bg-slate-500' },
  completed: { label: 'Completed', badgeClass: 'badge-green', dotClass: 'bg-emerald-400' },
  failed: { label: 'Failed', badgeClass: 'badge-red', dotClass: 'bg-red-400' },
  info: { label: 'Info', badgeClass: 'badge-blue', dotClass: 'bg-brand-400' },
  warning: { label: 'Warning', badgeClass: 'badge-amber', dotClass: 'bg-amber-400' },
  error: { label: 'Error', badgeClass: 'badge-red', dotClass: 'bg-red-400' },
  success: { label: 'Success', badgeClass: 'badge-green', dotClass: 'bg-emerald-400' },
  shortlisted: { label: 'Shortlisted', badgeClass: 'badge-green', dotClass: 'bg-emerald-400' },
  review: { label: 'In Review', badgeClass: 'badge-blue', dotClass: 'bg-brand-400' },
  pending: { label: 'Pending', badgeClass: 'badge-amber', dotClass: 'bg-amber-400' },
  rejected: { label: 'Rejected', badgeClass: 'badge-red', dotClass: 'bg-red-400' },
}

const resolved = computed(
  () =>
    config[props.status] ?? {
      label: props.status,
      badgeClass: 'badge bg-slate-700 text-slate-400',
      dotClass: 'bg-slate-500',
    },
)
</script>

<template>
  <span class="inline-flex items-center gap-1.5" :class="resolved.badgeClass">
    <span v-if="dot" class="h-1.5 w-1.5 rounded-full flex-shrink-0" :class="resolved.dotClass" />
    {{ resolved.label }}
  </span>
</template>
