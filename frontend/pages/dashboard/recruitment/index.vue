<script setup lang="ts">
import type { Candidate, CandidateStatus } from '~/types'

definePageMeta({ layout: 'dashboard' })

const { candidates, stats, isLoading, isAnalyzing, error, total, fetchCandidates, fetchStats, createCandidate, updateCandidate, deleteCandidate, analyzeCv } = useCandidates()

const searchQuery = ref('')
const activeStatus = ref<string>('all')
const showAddModal = ref(false)
const analyzingId = ref<string | null>(null)
const selectedCandidate = ref<Candidate | null>(null)
const showAnalysisPanel = ref(false)
const confirmDeleteId = ref<string | null>(null)

const statusTabs = [
  { key: 'all', label: 'All' },
  { key: 'pending', label: 'Pending' },
  { key: 'review', label: 'In Review' },
  { key: 'shortlisted', label: 'Shortlisted' },
  { key: 'interview', label: 'Interview' },
  { key: 'hired', label: 'Hired' },
  { key: 'rejected', label: 'Rejected' },
]

let searchTimer: ReturnType<typeof setTimeout>
const handleSearchInput = () => {
  clearTimeout(searchTimer)
  searchTimer = setTimeout(() => fetchCandidates({ search: searchQuery.value, status: activeStatus.value }), 350)
}

const handleStatusFilter = (status: string) => {
  activeStatus.value = status
  fetchCandidates({ search: searchQuery.value, status })
}

const handleAddCandidate = async (payload: Parameters<typeof createCandidate>[0]) => {
  const result = await createCandidate(payload)
  if (result) {
    showAddModal.value = false
    fetchStats()
  }
}

const handleAnalyze = async (candidate: Candidate) => {
  analyzingId.value = candidate.id
  const result = await analyzeCv(candidate)
  analyzingId.value = null
  if (result) {
    const updated = candidates.value.find(c => c.id === candidate.id)
    if (updated) {
      selectedCandidate.value = updated
      showAnalysisPanel.value = true
    }
    fetchStats()
  }
}

const handleUpdateStatus = async (id: string, status: CandidateStatus) => {
  await updateCandidate(id, { status })
  fetchStats()
}

const handleDelete = async () => {
  if (!confirmDeleteId.value) return
  await deleteCandidate(confirmDeleteId.value)
  confirmDeleteId.value = null
  fetchStats()
}

const handleViewCandidate = (candidate: Candidate) => {
  selectedCandidate.value = candidate
  if (candidate.ai_score !== null) {
    showAnalysisPanel.value = true
  }
}

onMounted(() => {
  fetchCandidates()
  fetchStats()
})
</script>

<template>
  <div class="p-6 space-y-6">
    <PageHeader title="Recruitment AI" description="AI-powered candidate screening and ranking">
      <template #actions>
        <button
          class="flex items-center gap-2 px-4 py-2 bg-violet-600 hover:bg-violet-500 rounded-lg text-white text-sm font-medium transition-colors"
          @click="showAddModal = true"
        >
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
          </svg>
          Add Candidate
        </button>
      </template>
    </PageHeader>

    <div v-if="stats" class="grid grid-cols-2 lg:grid-cols-4 gap-4">
      <KpiCard title="Total Candidates" :value="stats.total" icon="👥" />
      <KpiCard title="Analyzed" :value="stats.analyzed" icon="🤖" :subtitle="`${stats.total ? Math.round((stats.analyzed / stats.total) * 100) : 0}% of total`" />
      <KpiCard title="Avg AI Score" :value="stats.avgScore ? `${stats.avgScore}/100` : '—'" icon="⭐" />
      <KpiCard title="Shortlisted" :value="stats.byStatus?.shortlisted ?? 0" icon="✅" />
    </div>

    <div class="bg-[#141824] border border-white/[0.06] rounded-xl">
      <div class="p-4 border-b border-white/[0.06] flex flex-col sm:flex-row items-start sm:items-center gap-3">
        <div class="relative flex-1 min-w-0">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
          </svg>
          <input
            v-model="searchQuery"
            type="text"
            class="w-full bg-white/5 border border-white/10 rounded-lg pl-9 pr-3 py-2 text-white text-sm placeholder-gray-500 focus:outline-none focus:border-violet-500/50 transition-colors"
            placeholder="Search candidates..."
            @input="handleSearchInput"
          />
        </div>
        <div class="flex items-center gap-1 overflow-x-auto">
          <button
            v-for="tab in statusTabs"
            :key="tab.key"
            :class="['px-3 py-1.5 rounded-lg text-xs whitespace-nowrap transition-colors', activeStatus === tab.key ? 'bg-violet-600 text-white' : 'text-gray-400 hover:text-white hover:bg-white/5']"
            @click="handleStatusFilter(tab.key)"
          >
            {{ tab.label }}
          </button>
        </div>
      </div>

      <div class="p-4">
        <LoadingSpinner v-if="isLoading" class="py-12" />

        <div v-else-if="error" class="py-12 text-center">
          <p class="text-red-400 text-sm">{{ error }}</p>
          <button class="mt-3 text-violet-400 text-sm hover:underline" @click="fetchCandidates()">Retry</button>
        </div>

        <EmptyState
          v-else-if="candidates.length === 0"
          title="No candidates yet"
          description="Add your first candidate to start AI-powered screening"
          icon="👤"
        >
          <template #action>
            <button class="px-4 py-2 bg-violet-600 hover:bg-violet-500 rounded-lg text-white text-sm font-medium transition-colors" @click="showAddModal = true">
              Add Candidate
            </button>
          </template>
        </EmptyState>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
          <CandidateCard
            v-for="candidate in candidates"
            :key="candidate.id"
            :candidate="candidate"
            :is-analyzing="analyzingId === candidate.id"
            @analyze="handleAnalyze"
            @update-status="handleUpdateStatus"
            @delete="confirmDeleteId = $event"
            @view="handleViewCandidate"
          />
        </div>

        <p v-if="candidates.length > 0" class="text-xs text-gray-500 text-center mt-4">
          Showing {{ candidates.length }} of {{ total }} candidates
        </p>
      </div>
    </div>

    <AddCandidateModal v-if="showAddModal" @close="showAddModal = false" @submit="handleAddCandidate" />

    <CvAnalysisPanel
      v-if="showAnalysisPanel && selectedCandidate"
      :candidate="selectedCandidate"
      @close="showAnalysisPanel = false"
    />

    <ConfirmModal
      v-if="confirmDeleteId"
      title="Delete Candidate"
      description="This will permanently remove the candidate and their analysis. This cannot be undone."
      confirm-label="Delete"
      @confirm="handleDelete"
      @cancel="confirmDeleteId = null"
    />
  </div>
</template>
