<script setup lang="ts">
const { organizations, currentOrg, isSwitching, fetchOrganizations, switchOrganization, createOrganization } = useOrganizations()

const isOpen = ref(false)
const showCreateForm = ref(false)
const newOrgName = ref('')
const isCreating = ref(false)

const toggle = () => {
  isOpen.value = !isOpen.value
  if (isOpen.value && organizations.value.length === 0) {
    fetchOrganizations()
  }
}

const handleSwitch = async (orgId: string) => {
  if (isSwitching.value) return
  isOpen.value = false
  await switchOrganization(orgId)
  window.location.reload()
}

const handleCreate = async () => {
  if (!newOrgName.value.trim() || isCreating.value) return
  isCreating.value = true
  const org = await createOrganization(newOrgName.value.trim())
  isCreating.value = false
  if (org) {
    newOrgName.value = ''
    showCreateForm.value = false
    isOpen.value = false
    window.location.reload()
  }
}

onMounted(() => fetchOrganizations())
</script>

<template>
  <div class="relative">
    <button
      class="flex items-center gap-2 w-full px-3 py-2 rounded-lg hover:bg-white/5 transition-colors group"
      @click="toggle"
    >
      <div class="w-6 h-6 rounded-md bg-gradient-to-br from-violet-500 to-indigo-500 flex items-center justify-center text-white text-xs font-bold shrink-0">
        {{ currentOrg?.name?.charAt(0)?.toUpperCase() ?? '?' }}
      </div>
      <div class="flex-1 min-w-0 text-left">
        <p class="text-white text-xs font-medium truncate">{{ currentOrg?.name ?? 'Select org' }}</p>
        <p class="text-gray-500 text-[10px] capitalize">{{ currentOrg?.plan ?? 'starter' }}</p>
      </div>
      <svg class="w-3.5 h-3.5 text-gray-500 shrink-0 transition-transform" :class="isOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
      </svg>
    </button>

    <div
      v-if="isOpen"
      class="absolute bottom-full left-0 right-0 mb-1 bg-[#1e2436] border border-white/10 rounded-xl shadow-2xl py-1.5 z-50"
    >
      <div v-if="organizations.length > 0" class="px-2 mb-1">
        <p class="text-[10px] text-gray-500 px-1 py-0.5 uppercase tracking-wider">Organizations</p>
      </div>

      <button
        v-for="org in organizations"
        :key="org.id"
        class="w-full flex items-center gap-2.5 px-3 py-2 hover:bg-white/5 transition-colors"
        :disabled="isSwitching"
        @click="handleSwitch(org.id)"
      >
        <div class="w-5 h-5 rounded bg-gradient-to-br from-violet-500 to-indigo-500 flex items-center justify-center text-white text-[10px] font-bold shrink-0">
          {{ org.name.charAt(0).toUpperCase() }}
        </div>
        <div class="flex-1 min-w-0 text-left">
          <p class="text-sm text-white truncate">{{ org.name }}</p>
          <p class="text-[10px] text-gray-500 capitalize">{{ org.plan }}</p>
        </div>
        <div v-if="org.isActive" class="w-1.5 h-1.5 rounded-full bg-violet-400 shrink-0" />
      </button>

      <div class="border-t border-white/[0.06] mt-1 pt-1 px-2">
        <div v-if="showCreateForm" class="px-1 py-1">
          <form class="flex gap-1.5" @submit.prevent="handleCreate">
            <input
              v-model="newOrgName"
              type="text"
              class="flex-1 bg-white/5 border border-white/10 rounded-lg px-2 py-1.5 text-xs text-white placeholder-gray-600 focus:outline-none focus:border-violet-500/50"
              placeholder="Organization name"
              autofocus
            />
            <button
              type="submit"
              :disabled="!newOrgName.trim() || isCreating"
              class="px-2.5 py-1.5 bg-violet-600 hover:bg-violet-500 disabled:opacity-50 rounded-lg text-white text-xs font-medium transition-colors"
            >
              {{ isCreating ? '...' : 'Add' }}
            </button>
          </form>
        </div>
        <button
          v-else
          class="w-full flex items-center gap-2 px-1 py-1.5 text-xs text-gray-400 hover:text-white transition-colors"
          @click="showCreateForm = true"
        >
          <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
          </svg>
          New organization
        </button>
      </div>
    </div>
  </div>
</template>
