<script setup lang="ts">
import type { CreateAutomationPayload } from '~/types'

definePageMeta({ layout: 'dashboard', middleware: 'auth' })

const { automations, total, isLoading, fetchAutomations, createAutomation, toggleAutomation, deleteAutomation } = useAutomations()
const showCreateModal = ref(false)
const isCreating = ref(false)
const deleteTarget = ref<string | null>(null)
const isDeleting = ref(false)

const newForm = reactive<CreateAutomationPayload>({
  name: '',
  description: '',
  trigger_type: '',
  action_type: '',
  is_active: false,
})

const triggerTypes = ['schedule:daily', 'schedule:hourly', 'event:form_submit', 'event:ticket_created', 'event:email_received', 'api:webhook', 'manual']
const actionTypes = ['send_notification', 'generate_report', 'assign_ticket', 'update_record', 'send_email', 'call_webhook', 'create_task']

const handleCreate = async () => {
  isCreating.value = true
  try {
    await createAutomation({ ...newForm })
    showCreateModal.value = false
    newForm.name = ''
    newForm.description = ''
    newForm.trigger_type = ''
    newForm.action_type = ''
  } finally {
    isCreating.value = false
  }
}

const handleDelete = async () => {
  if (!deleteTarget.value) return
  isDeleting.value = true
  try {
    await deleteAutomation(deleteTarget.value)
    deleteTarget.value = null
  } finally {
    isDeleting.value = false
  }
}

onMounted(() => fetchAutomations())
</script>

<template>
  <div>
    <PageHeader title="Automations" :description="`${total} total automations`">
      <button class="btn-primary text-xs" @click="showCreateModal = true">
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        New Automation
      </button>
    </PageHeader>

    <!-- Loading -->
    <div v-if="isLoading" class="space-y-3">
      <div v-for="i in 4" :key="i" class="card animate-pulse">
        <div class="flex items-center gap-4">
          <div class="h-9 w-9 bg-surface-700 rounded-xl flex-shrink-0" />
          <div class="flex-1 space-y-1.5">
            <div class="h-4 bg-surface-700 rounded w-1/3" />
            <div class="h-3 bg-surface-700 rounded w-2/3" />
          </div>
          <div class="h-7 w-16 bg-surface-700 rounded" />
        </div>
      </div>
    </div>

    <!-- Table -->
    <div v-else-if="automations.length" class="card overflow-hidden p-0">
      <table class="w-full">
        <thead>
          <tr class="border-b border-surface-600">
            <th class="text-left text-[11px] font-medium text-slate-500 uppercase tracking-wider px-4 py-3">Automation</th>
            <th class="text-left text-[11px] font-medium text-slate-500 uppercase tracking-wider px-4 py-3 hidden md:table-cell">Trigger</th>
            <th class="text-left text-[11px] font-medium text-slate-500 uppercase tracking-wider px-4 py-3 hidden lg:table-cell">Action</th>
            <th class="text-left text-[11px] font-medium text-slate-500 uppercase tracking-wider px-4 py-3">Status</th>
            <th class="text-left text-[11px] font-medium text-slate-500 uppercase tracking-wider px-4 py-3 hidden sm:table-cell">Executions</th>
            <th class="px-4 py-3" />
          </tr>
        </thead>
        <tbody class="divide-y divide-surface-700">
          <tr
            v-for="automation in automations"
            :key="automation.id"
            class="hover:bg-surface-700/40 transition-colors"
          >
            <td class="px-4 py-3">
              <p class="text-sm font-medium text-slate-200">{{ automation.name }}</p>
              <p v-if="automation.description" class="text-xs text-slate-500 truncate max-w-xs">{{ automation.description }}</p>
            </td>
            <td class="px-4 py-3 hidden md:table-cell">
              <span class="text-xs font-mono text-slate-400">{{ automation.trigger_type }}</span>
            </td>
            <td class="px-4 py-3 hidden lg:table-cell">
              <span class="text-xs font-mono text-slate-400">{{ automation.action_type }}</span>
            </td>
            <td class="px-4 py-3">
              <StatusBadge :status="automation.is_active ? 'active' : 'paused'" dot />
            </td>
            <td class="px-4 py-3 hidden sm:table-cell">
              <span class="text-xs text-slate-400 tabular-nums">{{ automation.execution_count ?? 0 }}</span>
            </td>
            <td class="px-4 py-3">
              <div class="flex items-center gap-1 justify-end">
                <button
                  class="p-1.5 rounded-lg text-xs transition-colors"
                  :class="automation.is_active ? 'text-amber-400 hover:bg-amber-500/10' : 'text-emerald-400 hover:bg-emerald-500/10'"
                  :title="automation.is_active ? 'Disable' : 'Enable'"
                  @click="toggleAutomation(automation.id)"
                >
                  <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path v-if="automation.is_active" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </button>
                <button
                  class="p-1.5 rounded-lg text-slate-400 hover:text-red-400 hover:bg-red-500/10 transition-colors"
                  @click="deleteTarget = automation.id"
                >
                  <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <EmptyState
      v-else
      icon="M13 10V3L4 14h7v7l9-11h-7z"
      title="No automations yet"
      description="Create automations to run tasks automatically based on triggers"
      action-label="Create Automation"
      @action="showCreateModal = true"
    />

    <!-- Create Modal -->
    <Transition name="fade">
      <div v-if="showCreateModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="showCreateModal = false" />
        <div class="relative glass-panel w-full max-w-lg p-6 shadow-2xl">
          <div class="flex items-center justify-between mb-5">
            <h3 class="text-sm font-semibold text-white">Create Automation</h3>
            <button class="p-1.5 rounded-lg text-slate-400 hover:text-slate-200 hover:bg-surface-700 transition-colors" @click="showCreateModal = false">
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
          <form class="space-y-4" @submit.prevent="handleCreate">
            <div>
              <label class="block text-xs font-medium text-slate-300 mb-1.5">Name *</label>
              <input v-model="newForm.name" type="text" class="input" placeholder="e.g. Daily Digest" required />
            </div>
            <div>
              <label class="block text-xs font-medium text-slate-300 mb-1.5">Description</label>
              <textarea v-model="newForm.description" class="input resize-none" rows="2" />
            </div>
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-medium text-slate-300 mb-1.5">Trigger *</label>
                <select v-model="newForm.trigger_type" class="input" required>
                  <option value="" disabled>Select trigger</option>
                  <option v-for="t in triggerTypes" :key="t" :value="t">{{ t }}</option>
                </select>
              </div>
              <div>
                <label class="block text-xs font-medium text-slate-300 mb-1.5">Action *</label>
                <select v-model="newForm.action_type" class="input" required>
                  <option value="" disabled>Select action</option>
                  <option v-for="a in actionTypes" :key="a" :value="a">{{ a }}</option>
                </select>
              </div>
            </div>
            <div class="flex items-center justify-end gap-2 pt-2">
              <button type="button" class="btn-secondary text-xs" @click="showCreateModal = false">Cancel</button>
              <button type="submit" class="btn-primary text-xs" :disabled="isCreating">
                <LoadingSpinner v-if="isCreating" size="xs" />
                Create
              </button>
            </div>
          </form>
        </div>
      </div>
    </Transition>

    <ConfirmModal
      :open="!!deleteTarget"
      title="Delete Automation"
      description="This will permanently delete this automation."
      confirm-label="Delete"
      danger
      :loading="isDeleting"
      @confirm="handleDelete"
      @cancel="deleteTarget = null"
    />
  </div>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.15s ease }
.fade-enter-from, .fade-leave-to { opacity: 0 }
</style>
