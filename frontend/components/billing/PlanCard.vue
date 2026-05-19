<script setup lang="ts">
import type { Plan } from '~/composables/useSubscription'

const props = defineProps<{
  plan: Plan
  isRedirecting?: boolean
}>()

const emit = defineEmits<{
  select: [planKey: string]
}>()

const isPopular = computed(() => props.plan.key === 'professional')
</script>

<template>
  <div
    :class="[
      'relative border rounded-2xl p-6 flex flex-col transition-all',
      plan.isCurrent ? 'border-violet-500/50 bg-violet-500/5' : isPopular ? 'border-violet-500/30 bg-white/[0.02]' : 'border-white/[0.06] bg-white/[0.02]'
    ]"
  >
    <div v-if="isPopular && !plan.isCurrent" class="absolute -top-3 left-1/2 -translate-x-1/2">
      <span class="px-3 py-1 bg-violet-600 rounded-full text-xs text-white font-medium">Most Popular</span>
    </div>

    <div class="mb-4">
      <h3 class="text-white font-semibold text-lg">{{ plan.name }}</h3>
      <div class="flex items-baseline gap-1 mt-2">
        <span class="text-3xl font-bold text-white">${{ plan.price }}</span>
        <span class="text-gray-400 text-sm">/month</span>
      </div>
    </div>

    <ul class="space-y-2.5 flex-1 mb-6">
      <li
        v-for="feature in plan.features"
        :key="feature"
        class="flex items-start gap-2.5 text-sm text-gray-300"
      >
        <svg class="w-4 h-4 text-emerald-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        {{ feature }}
      </li>
    </ul>

    <button
      :disabled="plan.isCurrent || isRedirecting"
      :class="[
        'w-full py-2.5 rounded-xl text-sm font-medium transition-colors',
        plan.isCurrent
          ? 'bg-violet-500/20 text-violet-300 cursor-default'
          : plan.key === 'starter'
          ? 'bg-white/5 border border-white/10 text-gray-300 hover:bg-white/[0.08]'
          : 'bg-violet-600 hover:bg-violet-500 text-white disabled:opacity-50'
      ]"
      @click="!plan.isCurrent && plan.key !== 'starter' && emit('select', plan.key)"
    >
      <span v-if="plan.isCurrent">Current Plan</span>
      <span v-else-if="plan.key === 'starter'">Downgrade to Free</span>
      <span v-else-if="isRedirecting">Redirecting...</span>
      <span v-else>Upgrade to {{ plan.name }}</span>
    </button>
  </div>
</template>
