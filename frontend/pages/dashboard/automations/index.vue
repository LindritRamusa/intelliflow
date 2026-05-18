<script setup lang="ts">
import type { Automation, CreateAutomationPayload, AutomationRules } from '~/types'

definePageMeta({ layout: 'dashboard' })

const { automations, total, isLoading, fetchAutomations, createAutomation, updateAutomation, toggleAutomation, deleteAutomation } = useAutomations()

const showCreateModal = ref(false)
const editingAutomation = ref<Automation | null>(null)
const deleteTarget = ref<string | null>(null)
const activeTab = ref<'list' | 'builder'>('list')

const defaultRules: AutomationRules = { conditions: [], actions: [], logic: 'AND' }

const newForm = reactive<CreateAutomationPayload & { rules: AutomationRules }>({
  name: '',
  description: '',
  trigger_type: '',
  action_type: 'rule_based',
  is_active: false,
  rules: structuredClone(defaultRules),
})

const triggerTypes = [
  { value: 'schedule:daily', label: 'Daily Schedule' },
  { value: 'schedule:hourly', label: 'Hourly Schedule' },
  { value: 'event:form_submit', label: 'Form Submitted' },
  { value: 'event:ticket_created', label: 'Ticket Created' },
  { value: 'event:email_received', label: 'Email Received' },
  { value: 'api:webhook', label: 'Webhook Trigger' },
  { value: 'manual', label: 'Manual Trigger' },
]

const handleCreate = async () => {
  if (!newForm.name.trim() || !newForm.trigger_type) return

  await createAutomation({
    name: newForm.name,
    description: newForm.description,
    trigger_type: newForm.trigger_type,
    action_type: 'rule_based',
    is_active: newForm.is_active,
    config: { rules: newForm.rules },
  })

  showCreateModal.value = false
  Object.assign(newForm, { name: '', description: '', trigger_type: '', is_active: false, rules: structuredClone(defaultRules) })
}

const handleToggle = async (automation: Automation) => {
  await toggleAutomation(automation.id)
}

const handleDelete = async () => {
  if (!deleteTarget.value) return
  await deleteAutomation(deleteTarget.value)
  deleteTarget.value = null
}

const handleEditRules = (automation: Automation) => {
  editingAutomation.value = automation
  activeTab.value = 'builder'
}

const editRules = computed({
  get: () => (editingAutomation.value?.config?.rules ?? defaultRules) as AutomationRules,
  set: async (rules: AutomationRules) => {
    if (!editingAutomation.value) return
    await updateAutomation(editingAutomation.value.id, {
      config: { ...editingAutomation.value.config, rules },
    })
    editingAutomation.value = {
      ...editingAutomation.value,
      config: { ...editingAutomation.value.config, rules },
    }
  },
})

const statusDotClass = (automation: Automation) => {
  if (!automation.is_active) return 'bg-gray-600'
  return automation.status === 'running' ? 'bg-emerald-400 animate-pulse' : 'bg-yellow-400'
}

onMounted(fetchAutomations)
</script>

<template>
  <div class="p-6 space-y-6">
    <PageHeader title="Automations" description="Configure automated workflows with visual rule builder">
      <template #actions>
        <button
          class="flex items-center gap-2 px-4 py-2 bg-violet-600 hover:bg-violet-500 rounded-lg text-white text-sm font-medium transition-colors"
          @click="showCreateModal = true"
        >
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
          </svg>
          New Automation
        </button>
      </template>
    </PageHeader>

    <div class="flex items-center gap-1 bg-[#141824] border border-white/[0.06] rounded-xl p-1 w-fit">
      <button
        :class="['px-4 py-2 rounded-lg text-sm transition-colors', activeTab === 'list' ? 'bg-violet-600 text-white' : 'text-gray-400 hover:text-white']"
        @click="activeTab = 'list'"
      >
        All Automations
      </button>
      <button
        :class="['px-4 py-2 rounded-lg text-sm transition-colors flex items-center gap-1.5', activeTab === 'builder' ? 'bg-violet-600 text-white' : 'text-gray-400 hover:text-white']"
        @click="activeTab = 'builder'"
      >
        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        Rule Builder
        <span v-if="editingAutomation" class="px-1.5 py-0.5 bg-violet-400/20 rounded text-xs text-violet-300">active</span>
      </button>
    </div>

    <div v-if="activeTab === 'list'">
      <LoadingSpinner v-if="isLoading" class="py-12" />

      <EmptyState
        v-else-if="automations.length === 0"
        title="No automations yet"
        description="Create your first automation with the rule builder"
        icon="⚡"
      >
        <template #action>
          <button class="px-4 py-2 bg-violet-600 hover:bg-violet-500 rounded-lg text-white text-sm font-medium transition-colors" @click="showCreateModal = true">
            New Automation
          </button>
        </template>
      </EmptyState>

      <div v-else class="space-y-3">
        <div
          v-for="automation in automations"
          :key="automation.id"
          class="bg-[#141824] border border-white/[0.06] rounded-xl p-5 flex items-center gap-4 hover:border-white/10 transition-colors group"
        >
          <div class="flex items-center gap-3 flex-1 min-w-0">
            <div :class="['w-2 h-2 rounded-full shrink-0', statusDotClass(automation)]" />
            <div class="min-w-0">
              <p class="text-white font-medium text-sm truncate">{{ automation.name }}</p>
              <p class="text-gray-500 text-xs mt-0.5 truncate">
                {{ automation.trigger_type ?? automation.triggerType }}
                <span class="mx-1">→</span>
                {{ (automation.config?.rules as AutomationRules)?.actions?.length ?? 0 }} action(s)
              </p>
            </div>
          </div>

          <div class="flex items-center gap-2 shrink-0">
            <span class="text-xs text-gray-500 hidden sm:block">
              {{ automation.execution_count ?? automation.executionCount ?? 0 }} runs
            </span>
            <button
              class="p-1.5 rounded-lg text-gray-500 hover:text-violet-400 hover:bg-violet-500/10 transition-colors"
              title="Edit rules"
              @click="handleEditRules(automation); activeTab = 'builder'"
            >
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
              </svg>
            </button>
            <button
              :class="['relative w-10 h-5 rounded-full transition-colors', (automation.is_active ?? automation.isActive) ? 'bg-violet-600' : 'bg-white/10']"
              @click="handleToggle(automation)"
            >
              <div :class="['absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform', (automation.is_active ?? automation.isActive) ? 'translate-x-5' : 'translate-x-0']" />
            </button>
            <button
              class="p-1.5 rounded-lg text-gray-500 hover:text-red-400 hover:bg-red-500/10 transition-colors"
              @click="deleteTarget = automation.id"
            >
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
              </svg>
            </button>
          </div>
        </div>

        <p class="text-xs text-gray-500 text-center pt-1">{{ total }} automation{{ total !== 1 ? 's' : '' }} total</p>
      </div>
    </div>

    <div v-if="activeTab === 'builder'" class="bg-[#141824] border border-white/[0.06] rounded-xl p-6">
      <div v-if="editingAutomation" class="mb-6">
        <div class="flex items-center justify-between mb-1">
          <h2 class="text-white font-semibold">{{ editingAutomation.name }}</h2>
          <button
            class="text-xs text-gray-400 hover:text-white transition-colors"
            @click="editingAutomation = null"
          >
            Clear selection
          </button>
        </div>
        <p class="text-gray-400 text-sm">Configure conditions and actions for this automation</p>
      </div>
      <div v-else class="mb-6 p-4 bg-blue-500/5 border border-blue-500/15 rounded-lg">
        <p class="text-blue-300 text-sm">Select an automation from the list to edit its rules, or use the builder to design before creating.</p>
      </div>

      <RuleBuilder v-model="editRules" />

      <div v-if="editingAutomation" class="mt-6 flex justify-end">
        <p class="text-xs text-gray-500">Changes are saved automatically</p>
      </div>
    </div>

    <div v-if="showCreateModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
      <div class="bg-[#141824] border border-white/10 rounded-2xl w-full max-w-lg shadow-2xl">
        <div class="flex items-center justify-between p-6 border-b border-white/[0.06]">
          <h2 class="text-white font-semibold text-lg">New Automation</h2>
          <button class="p-2 rounded-lg text-gray-400 hover:text-white hover:bg-white/5 transition-colors" @click="showCreateModal = false">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
        <form class="p-6 space-y-4" @submit.prevent="handleCreate">
          <div>
            <label class="block text-sm text-gray-300 mb-1.5">Name <span class="text-red-400">*</span></label>
            <input v-model="newForm.name" type="text" required class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2.5 text-white text-sm placeholder-gray-500 focus:outline-none focus:border-violet-500/50 transition-colors" placeholder="Automation name" />
          </div>
          <div>
            <label class="block text-sm text-gray-300 mb-1.5">Description</label>
            <input v-model="newForm.description" type="text" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2.5 text-white text-sm placeholder-gray-500 focus:outline-none focus:border-violet-500/50 transition-colors" placeholder="Optional description" />
          </div>
          <div>
            <label class="block text-sm text-gray-300 mb-1.5">Trigger <span class="text-red-400">*</span></label>
            <select v-model="newForm.trigger_type" required class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2.5 text-white text-sm focus:outline-none focus:border-violet-500/50 transition-colors">
              <option value="" class="bg-[#1a1f2e]">Select a trigger</option>
              <option v-for="t in triggerTypes" :key="t.value" :value="t.value" class="bg-[#1a1f2e]">{{ t.label }}</option>
            </select>
          </div>
          <label class="flex items-center gap-3 cursor-pointer">
            <div :class="['relative w-11 h-6 rounded-full transition-colors', newForm.is_active ? 'bg-violet-600' : 'bg-white/10']" @click="newForm.is_active = !newForm.is_active">
              <div :class="['absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform', newForm.is_active ? 'translate-x-5' : 'translate-x-0']" />
            </div>
            <span class="text-sm text-gray-300">Activate immediately</span>
          </label>
          <div class="flex gap-3 pt-2">
            <button type="button" class="flex-1 px-4 py-2.5 bg-white/5 border border-white/10 rounded-lg text-gray-300 text-sm hover:bg-white/[0.08] transition-colors" @click="showCreateModal = false">Cancel</button>
            <button type="submit" :disabled="!newForm.name.trim() || !newForm.trigger_type" class="flex-1 px-4 py-2.5 bg-violet-600 hover:bg-violet-500 disabled:opacity-50 rounded-lg text-white text-sm font-medium transition-colors">Create</button>
          </div>
        </form>
      </div>
    </div>

    <ConfirmModal v-if="deleteTarget" title="Delete Automation" description="This will permanently delete the automation and its rules." confirm-label="Delete" @confirm="handleDelete" @cancel="deleteTarget = null" />
  </div>
</template>
