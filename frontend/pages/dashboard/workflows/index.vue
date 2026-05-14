<script setup lang="ts">
definePageMeta({ layout: 'dashboard', middleware: 'auth' })

const { workflows, total, isLoading, error, fetchWorkflows, createWorkflow, toggleWorkflow, deleteWorkflow } = useWorkflows()

const showCreateModal = ref(false)
const isCreating = ref(false)
const deleteTarget = ref<string | null>(null)
const isDeleting = ref(false)
const searchQuery = ref('')
const statusFilter = ref('')

const debouncedSearch = useDebounce(searchQuery, 400)

watch([debouncedSearch, statusFilter], () => {
  fetchWorkflows({ search: debouncedSearch.value, status: statusFilter.value })
})

onMounted(() => {
  fetchWorkflows()
})

const handleCreate = async (payload: ReturnType<typeof useWorkflows>['workflows']['value'][0] | Record<string, unknown>) => {
  isCreating.value = true
  try {
    await createWorkflow(payload as Parameters<typeof createWorkflow>[0])
    showCreateModal.value = false
  } catch {
    // Error shown via alert or toast in future
  } finally {
    isCreating.value = false
  }
}

const handleDelete = async () => {
  if (!deleteTarget.value) return
  isDeleting.value = true
  try {
    await deleteWorkflow(deleteTarget.value)
    deleteTarget.value = null
  } finally {
    isDeleting.value = false
  }
}

const handleToggle = async (id: string) => {
  await toggleWorkflow(id)
}
</script>

<template>
  <div>
    <PageHeader title="Workflows" :description="`${total} total workflows`">
      <button class="btn-primary text-xs" @click="showCreateModal = true">
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        New Workflow
      </button>
    </PageHeader>

    <!-- Filters -->
    <div class="flex items-center gap-3 mb-5">
      <div class="flex items-center gap-2 bg-surface-700 border border-surface-600 rounded-lg px-3 py-2 flex-1 max-w-xs focus-within:border-brand-500 transition-colors">
        <svg class="h-3.5 w-3.5 text-slate-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search workflows..."
          class="bg-transparent text-xs text-slate-200 placeholder-slate-500 focus:outline-none flex-1"
        />
      </div>

      <div class="flex items-center gap-1 p-1 bg-surface-700 rounded-lg border border-surface-600">
        <button
          v-for="s in [{ label: 'All', value: '' }, { label: 'Active', value: 'active' }, { label: 'Paused', value: 'paused' }, { label: 'Draft', value: 'draft' }]"
          :key="s.value"
          class="px-2.5 py-1 text-xs font-medium rounded-md transition-colors"
          :class="statusFilter === s.value ? 'bg-brand-600 text-white' : 'text-slate-400 hover:text-slate-200'"
          @click="statusFilter = s.value"
        >
          {{ s.label }}
        </button>
      </div>
    </div>

    <!-- Grid -->
    <div v-if="isLoading" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
      <div v-for="i in 6" :key="i" class="card animate-pulse">
        <div class="flex items-center gap-2 mb-3">
          <div class="h-4 bg-surface-700 rounded w-2/3" />
          <div class="h-4 bg-surface-700 rounded w-12 ml-auto" />
        </div>
        <div class="h-3 bg-surface-700 rounded w-full mb-1.5" />
        <div class="h-3 bg-surface-700 rounded w-3/4 mb-4" />
        <div class="h-8 bg-surface-700 rounded" />
      </div>
    </div>

    <div v-else-if="workflows.length" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
      <WorkflowCard
        v-for="wf in workflows"
        :key="wf.id"
        :workflow="wf"
        @toggle="handleToggle"
        @delete="deleteTarget = $event"
      />
    </div>

    <EmptyState
      v-else
      icon="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"
      title="No workflows yet"
      description="Create your first workflow to start automating tasks"
      action-label="Create Workflow"
      @action="showCreateModal = true"
    />

    <CreateWorkflowModal
      :open="showCreateModal"
      :loading="isCreating"
      @submit="handleCreate"
      @cancel="showCreateModal = false"
    />

    <ConfirmModal
      :open="!!deleteTarget"
      title="Delete Workflow"
      description="This will permanently delete the workflow and all associated automations. This cannot be undone."
      confirm-label="Delete"
      danger
      :loading="isDeleting"
      @confirm="handleDelete"
      @cancel="deleteTarget = null"
    />
  </div>
</template>
