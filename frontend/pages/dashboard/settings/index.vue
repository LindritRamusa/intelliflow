<script setup lang="ts">
definePageMeta({ layout: 'dashboard' })

const authStore = useAuthStore()
const { logout } = useAuth()
const { billing, isLoading: isBillingLoading, isRedirecting, error: billingError, statusLabel, fetchBilling, startCheckout, openPortal } = useSubscription()
const { organizations, currentOrg, fetchOrganizations, createOrganization } = useOrganizations()

const activeTab = ref('profile')
const isSaving = ref(false)
const newOrgName = ref('')
const isCreatingOrg = ref(false)

const tabs = [
  { key: 'profile', label: 'Profile' },
  { key: 'organization', label: 'Organization' },
  { key: 'billing', label: 'Billing' },
]

const profileForm = reactive({
  name: authStore.user?.name ?? '',
  email: authStore.user?.email ?? '',
})

const handleSaveProfile = async () => {
  isSaving.value = true
  await new Promise(r => setTimeout(r, 600))
  isSaving.value = false
}

const handleCreateOrg = async () => {
  if (!newOrgName.value.trim() || isCreatingOrg.value) return
  isCreatingOrg.value = true
  const result = await createOrganization(newOrgName.value.trim())
  isCreatingOrg.value = false
  if (result) {
    newOrgName.value = ''
    window.location.reload()
  }
}

const billingSuccessMessage = ref('')
const route = useRoute()

onMounted(() => {
  if (route.query.billing === 'success') {
    billingSuccessMessage.value = 'Your subscription has been activated successfully!'
    activeTab.value = 'billing'
  }
  fetchBilling()
  fetchOrganizations()
})
</script>

<template>
  <div class="p-6 space-y-6">
    <PageHeader title="Settings" description="Manage your profile, organization, and billing" />

    <div class="flex items-center gap-1 bg-[#141824] border border-white/[0.06] rounded-xl p-1 w-fit">
      <button
        v-for="tab in tabs"
        :key="tab.key"
        :class="['px-4 py-2 rounded-lg text-sm transition-colors', activeTab === tab.key ? 'bg-violet-600 text-white' : 'text-gray-400 hover:text-white']"
        @click="activeTab = tab.key"
      >
        {{ tab.label }}
      </button>
    </div>

    <div v-if="activeTab === 'profile'" class="max-w-lg space-y-6">
      <div class="bg-[#141824] border border-white/[0.06] rounded-xl p-6 space-y-4">
        <h2 class="text-white font-medium">Profile</h2>

        <div class="flex items-center gap-4">
          <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-violet-500 to-indigo-500 flex items-center justify-center text-white text-2xl font-bold">
            {{ authStore.user?.name?.charAt(0)?.toUpperCase() }}
          </div>
          <div>
            <p class="text-white font-medium">{{ authStore.user?.name }}</p>
            <p class="text-gray-400 text-sm">{{ authStore.user?.email }}</p>
            <span class="mt-1 inline-block px-2 py-0.5 bg-violet-500/15 border border-violet-500/20 rounded-full text-xs text-violet-400 capitalize">
              {{ authStore.user?.role?.replace('_', ' ') }}
            </span>
          </div>
        </div>

        <form class="space-y-4 pt-2" @submit.prevent="handleSaveProfile">
          <div>
            <label class="block text-sm text-gray-300 mb-1.5">Full Name</label>
            <input v-model="profileForm.name" type="text" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2.5 text-white text-sm focus:outline-none focus:border-violet-500/50 transition-colors" />
          </div>
          <div>
            <label class="block text-sm text-gray-300 mb-1.5">Email</label>
            <input v-model="profileForm.email" type="email" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2.5 text-white text-sm focus:outline-none focus:border-violet-500/50 transition-colors" />
          </div>
          <button type="submit" :disabled="isSaving" class="px-4 py-2 bg-violet-600 hover:bg-violet-500 disabled:opacity-50 rounded-lg text-white text-sm font-medium transition-colors">
            {{ isSaving ? 'Saving...' : 'Save Changes' }}
          </button>
        </form>
      </div>

      <div class="bg-[#141824] border border-red-500/10 rounded-xl p-6">
        <h2 class="text-white font-medium mb-1">Danger Zone</h2>
        <p class="text-gray-400 text-sm mb-4">This will log you out of your current session.</p>
        <button
          class="px-4 py-2 bg-red-500/10 hover:bg-red-500/20 border border-red-500/20 rounded-lg text-red-400 text-sm font-medium transition-colors"
          @click="logout()"
        >
          Sign Out
        </button>
      </div>
    </div>

    <div v-if="activeTab === 'organization'" class="max-w-2xl space-y-6">
      <div class="bg-[#141824] border border-white/[0.06] rounded-xl p-6">
        <h2 class="text-white font-medium mb-4">Your Organizations</h2>

        <div class="space-y-3 mb-6">
          <div
            v-for="org in organizations"
            :key="org.id"
            class="flex items-center gap-3 p-3 bg-white/[0.03] border border-white/[0.06] rounded-xl"
          >
            <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-violet-500 to-indigo-500 flex items-center justify-center text-white font-bold text-sm shrink-0">
              {{ org.name.charAt(0).toUpperCase() }}
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-white font-medium text-sm truncate">{{ org.name }}</p>
              <p class="text-gray-500 text-xs capitalize">{{ org.plan }} · {{ org.slug }}</p>
            </div>
            <span v-if="org.isActive" class="px-2 py-0.5 bg-violet-500/15 border border-violet-500/20 rounded-full text-xs text-violet-400">
              Active
            </span>
          </div>
        </div>

        <div class="border-t border-white/[0.06] pt-5">
          <h3 class="text-sm font-medium text-gray-300 mb-3">Create New Organization</h3>
          <form class="flex gap-3" @submit.prevent="handleCreateOrg">
            <input
              v-model="newOrgName"
              type="text"
              class="flex-1 bg-white/5 border border-white/10 rounded-lg px-3 py-2.5 text-white text-sm placeholder-gray-500 focus:outline-none focus:border-violet-500/50 transition-colors"
              placeholder="Organization name"
            />
            <button
              type="submit"
              :disabled="!newOrgName.trim() || isCreatingOrg"
              class="px-4 py-2.5 bg-violet-600 hover:bg-violet-500 disabled:opacity-50 rounded-lg text-white text-sm font-medium transition-colors whitespace-nowrap"
            >
              {{ isCreatingOrg ? 'Creating...' : 'Create' }}
            </button>
          </form>
          <p class="text-xs text-gray-500 mt-2">You will become the admin of the new organization and it will be set as active.</p>
        </div>
      </div>
    </div>

    <div v-if="activeTab === 'billing'" class="space-y-6">
      <div v-if="billingSuccessMessage" class="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl flex items-center gap-3">
        <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-emerald-400 text-sm">{{ billingSuccessMessage }}</p>
      </div>

      <div v-if="billingError" class="p-4 bg-amber-500/10 border border-amber-500/20 rounded-xl">
        <p class="text-amber-400 text-sm">{{ billingError }}</p>
      </div>

      <div class="bg-[#141824] border border-white/[0.06] rounded-xl p-5 flex items-center justify-between gap-4">
        <div>
          <p class="text-gray-400 text-sm">Current plan</p>
          <p class="text-white font-semibold text-lg capitalize mt-0.5">{{ billing?.currentPlan ?? 'Starter' }}</p>
        </div>
        <div class="text-right">
          <span :class="['px-2.5 py-1 rounded-full text-xs font-medium', billing?.subscriptionStatus === 'active' ? 'bg-emerald-500/15 text-emerald-400' : 'bg-gray-500/15 text-gray-400']">
            {{ statusLabel }}
          </span>
          <p v-if="billing?.subscriptionEndsAt" class="text-xs text-gray-500 mt-1">
            Renews {{ new Date(billing.subscriptionEndsAt).toLocaleDateString() }}
          </p>
        </div>
      </div>

      <LoadingSpinner v-if="isBillingLoading" class="py-8" />

      <div v-else-if="billing" class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <PlanCard
          v-for="plan in billing.plans"
          :key="plan.key"
          :plan="plan"
          :is-redirecting="isRedirecting"
          @select="startCheckout($event as 'professional' | 'enterprise')"
        />
      </div>

      <div v-if="billing?.subscriptionStatus === 'active'" class="bg-[#141824] border border-white/[0.06] rounded-xl p-5 flex items-center justify-between gap-4">
        <div>
          <p class="text-white font-medium text-sm">Manage Subscription</p>
          <p class="text-gray-400 text-xs mt-0.5">Update payment method, view invoices, or cancel subscription</p>
        </div>
        <button
          :disabled="isRedirecting"
          class="px-4 py-2 bg-white/5 hover:bg-white/[0.08] border border-white/10 rounded-lg text-sm text-gray-300 disabled:opacity-50 transition-colors whitespace-nowrap"
          @click="openPortal()"
        >
          {{ isRedirecting ? 'Redirecting...' : 'Billing Portal' }}
        </button>
      </div>
    </div>
  </div>
</template>
