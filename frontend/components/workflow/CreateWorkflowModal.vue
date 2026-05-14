<script setup lang="ts">
import type { CreateWorkflowPayload } from '~/types'

defineProps<{
  open: boolean
  loading?: boolean
}>()

const emit = defineEmits<{
  submit: [payload: CreateWorkflowPayload]
  cancel: []
}>()

const form = reactive<CreateWorkflowPayload>({
  name: '',
  description: '',
  trigger: '',
  status: 'draft',
})

const triggerOptions = [
  { value: 'schedule:daily', label: 'Daily Schedule' },
  { value: 'schedule:weekly', label: 'Weekly Schedule' },
  { value: 'event:form_submit', label: 'Form Submission' },
  { value: 'event:email_received', label: 'Email Received' },
  { value: 'event:ticket_created', label: 'Ticket Created' },
  { value: 'event:data_threshold', label: 'Data Threshold' },
  { value: 'manual', label: 'Manual Trigger' },
  { value: 'api:webhook', label: 'Webhook' },
]

const handleSubmit = () => {
  if (!form.name.trim() || !form.trigger) return
  emit('submit', { ...form })
}

const resetForm = () => {
  form.name = ''
  form.description = ''
  form.trigger = ''
  form.status = 'draft'
}

watch(() => emit, () => {
  if (!emit) resetForm()
})
</script>

<template>
  <Transition name="fade">
    <div v-if="open" class="fixed inset-0 z-50 flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="emit('cancel')" />
      <div class="relative glass-panel w-full max-w-lg p-6 shadow-2xl">
        <!-- Header -->
        <div class="flex items-center justify-between mb-5">
          <div>
            <h3 class="text-sm font-semibold text-white">Create Workflow</h3>
            <p class="text-xs text-slate-500 mt-0.5">Define a new automation workflow</p>
          </div>
          <button
            class="p-1.5 rounded-lg text-slate-400 hover:text-slate-200 hover:bg-surface-700 transition-colors"
            @click="emit('cancel')"
          >
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Form -->
        <form class="space-y-4" @submit.prevent="handleSubmit">
          <div>
            <label class="block text-xs font-medium text-slate-300 mb-1.5">Workflow Name *</label>
            <input
              v-model="form.name"
              type="text"
              class="input"
              placeholder="e.g. Daily Report Automation"
              required
            />
          </div>

          <div>
            <label class="block text-xs font-medium text-slate-300 mb-1.5">Description</label>
            <textarea
              v-model="form.description"
              class="input resize-none"
              rows="2"
              placeholder="What does this workflow do?"
            />
          </div>

          <div>
            <label class="block text-xs font-medium text-slate-300 mb-1.5">Trigger *</label>
            <select v-model="form.trigger" class="input" required>
              <option value="" disabled>Select a trigger...</option>
              <option v-for="opt in triggerOptions" :key="opt.value" :value="opt.value">
                {{ opt.label }}
              </option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-medium text-slate-300 mb-1.5">Initial Status</label>
            <div class="flex gap-2">
              <label
                v-for="s in ['draft', 'active']"
                :key="s"
                class="flex items-center gap-2 px-3 py-2 rounded-lg border cursor-pointer transition-colors text-xs font-medium capitalize"
                :class="
                  form.status === s
                    ? 'border-brand-500 bg-brand-500/10 text-brand-300'
                    : 'border-surface-600 bg-surface-700 text-slate-400 hover:border-surface-500'
                "
              >
                <input v-model="form.status" type="radio" :value="s" class="sr-only" />
                {{ s }}
              </label>
            </div>
          </div>

          <div class="flex items-center justify-end gap-2 pt-2">
            <button type="button" class="btn-secondary text-xs" :disabled="loading" @click="emit('cancel')">
              Cancel
            </button>
            <button type="submit" class="btn-primary text-xs" :disabled="loading || !form.name || !form.trigger">
              <LoadingSpinner v-if="loading" size="xs" />
              Create Workflow
            </button>
          </div>
        </form>
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
