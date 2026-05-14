<script setup lang="ts">
definePageMeta({ layout: 'dashboard', middleware: 'auth' })

const route = useRoute()
const api = useApi()
const workflow = ref<import('~/types').Workflow | null>(null)
const isLoading = ref(true)
const error = ref<string | null>(null)

onMounted(async () => {
  try {
    const res = await api.get<import('~/types').Workflow>(`/workflows/${route.params.id}`)
    workflow.value = res.data
  } catch (e) {
    error.value = e instanceof Error ? e.message : 'Workflow not found'
  } finally {
    isLoading.value = false
  }
})
</script>

<template>
  <div>
    <!-- Back -->
    <NuxtLink
      to="/dashboard/workflows"
      class="flex items-center gap-1.5 text-xs text-slate-400 hover:text-slate-200 transition-colors mb-5"
    >
      <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
      </svg>
      Back to Workflows
    </NuxtLink>

    <div v-if="isLoading" class="flex items-center justify-center py-20">
      <LoadingSpinner label="Loading workflow..." />
    </div>

    <div v-else-if="error" class="card text-center py-12">
      <p class="text-sm text-red-400">{{ error }}</p>
    </div>

    <template v-else-if="workflow">
      <!-- Header -->
      <div class="flex items-start justify-between mb-6 gap-4">
        <div>
          <div class="flex items-center gap-3 mb-1">
            <h2 class="text-xl font-bold text-white">{{ workflow.name }}</h2>
            <StatusBadge :status="workflow.status" dot />
          </div>
          <p v-if="workflow.description" class="text-sm text-slate-400">{{ workflow.description }}</p>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Details -->
        <div class="lg:col-span-2 space-y-4">
          <!-- Trigger info -->
          <div class="card">
            <h3 class="text-sm font-semibold text-white mb-3">Trigger Configuration</h3>
            <div class="flex items-center gap-3 p-3 bg-surface-700/50 rounded-lg border border-surface-600">
              <svg class="h-4 w-4 text-brand-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
              </svg>
              <div>
                <p class="text-xs font-medium text-slate-200">{{ workflow.trigger }}</p>
                <p class="text-[11px] text-slate-500">Automation trigger</p>
              </div>
            </div>
          </div>

          <!-- Automations -->
          <div class="card">
            <h3 class="text-sm font-semibold text-white mb-3">
              Linked Automations
              <span class="ml-2 text-xs text-slate-500 font-normal">
                ({{ (workflow as Record<string, unknown>).automations?.length ?? 0 }})
              </span>
            </h3>
            <EmptyState
              v-if="!((workflow as Record<string, unknown>).automations as unknown[])?.length"
              title="No automations linked"
              description="Create automations to link to this workflow"
              action-label="Add Automation"
              action-to="/dashboard/automations"
            />
          </div>
        </div>

        <!-- Metadata -->
        <div class="space-y-4">
          <div class="card">
            <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Details</h3>
            <div class="space-y-3">
              <div v-for="detail in [
                { label: 'Status', value: workflow.status },
                { label: 'Run Count', value: `${workflow.run_count ?? 0} executions` },
                { label: 'Created', value: workflow.created_at ? new Date(workflow.created_at).toLocaleDateString() : '—' },
                { label: 'Last Run', value: workflow.last_run_at ? new Date(workflow.last_run_at).toLocaleDateString() : 'Never' },
              ]" :key="detail.label">
                <div class="flex items-center justify-between">
                  <span class="text-xs text-slate-500">{{ detail.label }}</span>
                  <span class="text-xs text-slate-300 font-medium">{{ detail.value }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>
