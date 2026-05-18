<script setup lang="ts">
import type { AutomationRules, RuleCondition, RuleAction } from '~/types'

const props = defineProps<{
  modelValue: AutomationRules
}>()

const emit = defineEmits<{
  'update:modelValue': [rules: AutomationRules]
}>()

const rules = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val),
})

const conditionFields = [
  { value: 'trigger.type', label: 'Trigger Type' },
  { value: 'workflow.status', label: 'Workflow Status' },
  { value: 'execution.count', label: 'Execution Count' },
  { value: 'time.hour', label: 'Time of Day (Hour)' },
  { value: 'time.dayOfWeek', label: 'Day of Week' },
  { value: 'data.field', label: 'Data Field' },
]

const conditionOperators = [
  { value: 'equals', label: 'equals' },
  { value: 'not_equals', label: 'does not equal' },
  { value: 'contains', label: 'contains' },
  { value: 'greater_than', label: 'is greater than' },
  { value: 'less_than', label: 'is less than' },
  { value: 'is_empty', label: 'is empty' },
  { value: 'is_not_empty', label: 'is not empty' },
]

const actionTypes = [
  { value: 'send_notification', label: 'Send Notification', icon: '🔔', fields: [{ key: 'message', label: 'Message', placeholder: 'Notification text...' }] },
  { value: 'send_email', label: 'Send Email', icon: '📧', fields: [{ key: 'email_to', label: 'To', placeholder: 'email@example.com' }, { key: 'subject', label: 'Subject', placeholder: 'Email subject...' }] },
  { value: 'trigger_webhook', label: 'Trigger Webhook', icon: '🌐', fields: [{ key: 'url', label: 'Webhook URL', placeholder: 'https://...' }] },
  { value: 'update_field', label: 'Update Field', icon: '✏️', fields: [{ key: 'field', label: 'Field', placeholder: 'field.name' }, { key: 'value', label: 'Value', placeholder: 'new value' }] },
  { value: 'create_task', label: 'Create Task', icon: '✅', fields: [{ key: 'title', label: 'Task Title', placeholder: 'Task description...' }] },
  { value: 'add_tag', label: 'Add Tag', icon: '🏷️', fields: [{ key: 'tag', label: 'Tag', placeholder: 'tag-name' }] },
]

const addCondition = () => {
  const newCondition: RuleCondition = {
    id: Math.random().toString(36).slice(2),
    field: 'trigger.type',
    operator: 'equals',
    value: '',
  }
  emit('update:modelValue', {
    ...rules.value,
    conditions: [...rules.value.conditions, newCondition],
  })
}

const removeCondition = (id: string) => {
  emit('update:modelValue', {
    ...rules.value,
    conditions: rules.value.conditions.filter(c => c.id !== id),
  })
}

const updateCondition = (id: string, key: keyof RuleCondition, value: string) => {
  emit('update:modelValue', {
    ...rules.value,
    conditions: rules.value.conditions.map(c =>
      c.id === id ? { ...c, [key]: value } : c
    ),
  })
}

const addAction = () => {
  const newAction: RuleAction = {
    id: Math.random().toString(36).slice(2),
    type: 'send_notification',
    config: { message: '' },
  }
  emit('update:modelValue', {
    ...rules.value,
    actions: [...rules.value.actions, newAction],
  })
}

const removeAction = (id: string) => {
  emit('update:modelValue', {
    ...rules.value,
    actions: rules.value.actions.filter(a => a.id !== id),
  })
}

const updateActionType = (id: string, type: RuleAction['type']) => {
  emit('update:modelValue', {
    ...rules.value,
    actions: rules.value.actions.map(a =>
      a.id === id ? { ...a, type, config: {} } : a
    ),
  })
}

const updateActionConfig = (id: string, key: string, value: string) => {
  emit('update:modelValue', {
    ...rules.value,
    actions: rules.value.actions.map(a =>
      a.id === id ? { ...a, config: { ...a.config, [key]: value } } : a
    ),
  })
}

const toggleLogic = () => {
  emit('update:modelValue', { ...rules.value, logic: rules.value.logic === 'AND' ? 'OR' : 'AND' })
}

const needsValue = (operator: string) => !['is_empty', 'is_not_empty'].includes(operator)

const getActionConfig = (actionType: RuleAction['type']) =>
  actionTypes.find(a => a.value === actionType)
</script>

<template>
  <div class="space-y-6">
    <div>
      <div class="flex items-center justify-between mb-3">
        <h3 class="text-sm font-medium text-gray-300 flex items-center gap-2">
          <span class="w-5 h-5 bg-blue-500/15 border border-blue-500/20 rounded text-blue-400 text-xs flex items-center justify-center">IF</span>
          Conditions
        </h3>
        <div class="flex items-center gap-2">
          <button
            class="px-2.5 py-1 text-xs rounded-lg border transition-colors"
            :class="rules.logic === 'AND' ? 'bg-blue-500/15 border-blue-500/30 text-blue-400' : 'bg-white/5 border-white/10 text-gray-400 hover:border-white/20'"
            @click="toggleLogic"
          >
            {{ rules.logic === 'AND' ? 'ALL conditions' : 'ANY condition' }}
          </button>
        </div>
      </div>

      <div class="space-y-2">
        <div
          v-for="(condition, i) in rules.conditions"
          :key="condition.id"
          class="flex items-center gap-2 p-3 bg-white/[0.03] border border-white/[0.06] rounded-lg"
        >
          <span v-if="i > 0" class="text-xs text-gray-500 w-8 text-center shrink-0">{{ rules.logic }}</span>
          <span v-else class="w-8 shrink-0" />

          <select
            :value="condition.field"
            class="flex-1 bg-[#1a1f2e] border border-white/10 rounded-lg px-2 py-1.5 text-xs text-white focus:outline-none focus:border-violet-500/50 min-w-0"
            @change="updateCondition(condition.id, 'field', ($event.target as HTMLSelectElement).value)"
          >
            <option v-for="f in conditionFields" :key="f.value" :value="f.value" class="bg-[#1a1f2e]">{{ f.label }}</option>
          </select>

          <select
            :value="condition.operator"
            class="flex-1 bg-[#1a1f2e] border border-white/10 rounded-lg px-2 py-1.5 text-xs text-white focus:outline-none focus:border-violet-500/50 min-w-0"
            @change="updateCondition(condition.id, 'operator', ($event.target as HTMLSelectElement).value)"
          >
            <option v-for="op in conditionOperators" :key="op.value" :value="op.value" class="bg-[#1a1f2e]">{{ op.label }}</option>
          </select>

          <input
            v-if="needsValue(condition.operator)"
            :value="condition.value"
            type="text"
            class="flex-1 bg-[#1a1f2e] border border-white/10 rounded-lg px-2 py-1.5 text-xs text-white placeholder-gray-600 focus:outline-none focus:border-violet-500/50 min-w-0"
            placeholder="value"
            @input="updateCondition(condition.id, 'value', ($event.target as HTMLInputElement).value)"
          />
          <div v-else class="flex-1" />

          <button
            class="p-1 text-gray-600 hover:text-red-400 transition-colors shrink-0"
            @click="removeCondition(condition.id)"
          >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <div v-if="rules.conditions.length === 0" class="p-4 border border-dashed border-white/10 rounded-lg text-center text-gray-500 text-xs">
          No conditions set — automation will run on every trigger
        </div>

        <button
          type="button"
          class="w-full py-2 border border-dashed border-white/10 rounded-lg text-xs text-gray-500 hover:text-gray-300 hover:border-white/20 transition-colors"
          @click="addCondition"
        >
          + Add Condition
        </button>
      </div>
    </div>

    <div>
      <div class="flex items-center mb-3">
        <h3 class="text-sm font-medium text-gray-300 flex items-center gap-2">
          <span class="w-5 h-5 bg-emerald-500/15 border border-emerald-500/20 rounded text-emerald-400 text-xs flex items-center justify-center">DO</span>
          Actions
        </h3>
      </div>

      <div class="space-y-2">
        <div
          v-for="action in rules.actions"
          :key="action.id"
          class="p-3 bg-white/[0.03] border border-white/[0.06] rounded-lg space-y-2"
        >
          <div class="flex items-center gap-2">
            <select
              :value="action.type"
              class="flex-1 bg-[#1a1f2e] border border-white/10 rounded-lg px-2 py-1.5 text-xs text-white focus:outline-none focus:border-violet-500/50"
              @change="updateActionType(action.id, ($event.target as HTMLSelectElement).value as RuleAction['type'])"
            >
              <option v-for="a in actionTypes" :key="a.value" :value="a.value" class="bg-[#1a1f2e]">{{ a.icon }} {{ a.label }}</option>
            </select>
            <button
              class="p-1 text-gray-600 hover:text-red-400 transition-colors shrink-0"
              @click="removeAction(action.id)"
            >
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>

          <div v-if="getActionConfig(action.type)" class="grid gap-2" :class="(getActionConfig(action.type)?.fields.length ?? 0) > 1 ? 'grid-cols-2' : 'grid-cols-1'">
            <input
              v-for="field in getActionConfig(action.type)?.fields"
              :key="field.key"
              :value="action.config[field.key] ?? ''"
              type="text"
              class="w-full bg-[#1a1f2e] border border-white/10 rounded-lg px-2 py-1.5 text-xs text-white placeholder-gray-600 focus:outline-none focus:border-violet-500/50"
              :placeholder="field.placeholder"
              @input="updateActionConfig(action.id, field.key, ($event.target as HTMLInputElement).value)"
            />
          </div>
        </div>

        <div v-if="rules.actions.length === 0" class="p-4 border border-dashed border-white/10 rounded-lg text-center text-gray-500 text-xs">
          No actions defined — add at least one action
        </div>

        <button
          type="button"
          class="w-full py-2 border border-dashed border-white/10 rounded-lg text-xs text-gray-500 hover:text-gray-300 hover:border-white/20 transition-colors"
          @click="addAction"
        >
          + Add Action
        </button>
      </div>
    </div>
  </div>
</template>
