<script setup lang="ts">
definePageMeta({ layout: 'dashboard', middleware: 'auth' })

const authStore = useAuthStore()
const { logout } = useAuth()

const profileForm = reactive({
  name: authStore.user?.name ?? '',
  email: authStore.user?.email ?? '',
})

const isSaving = ref(false)

const handleSaveProfile = async () => {
  isSaving.value = true
  await new Promise((r) => setTimeout(r, 800))
  isSaving.value = false
}

const tabs = ['Profile', 'Organization', 'API Keys', 'Notifications', 'Billing']
const activeTab = ref('Profile')
</script>

<template>
  <div>
    <PageHeader title="Settings" description="Manage your account and platform configuration" />

    <div class="flex gap-6">
      <!-- Sidebar tabs -->
      <div class="w-44 flex-shrink-0">
        <nav class="space-y-0.5">
          <button
            v-for="tab in tabs"
            :key="tab"
            class="w-full text-left px-3 py-2 text-sm rounded-lg transition-colors font-medium"
            :class="activeTab === tab ? 'bg-brand-600/15 text-brand-300' : 'text-slate-400 hover:text-slate-200 hover:bg-surface-700'"
            @click="activeTab = tab"
          >
            {{ tab }}
          </button>
        </nav>
      </div>

      <!-- Content -->
      <div class="flex-1 min-w-0">
        <!-- Profile -->
        <template v-if="activeTab === 'Profile'">
          <div class="card">
            <h3 class="text-sm font-semibold text-white mb-4">Profile Settings</h3>
            <form class="space-y-4 max-w-md" @submit.prevent="handleSaveProfile">
              <!-- Avatar -->
              <div class="flex items-center gap-4 mb-5">
                <div class="h-16 w-16 rounded-full bg-brand-600/20 flex items-center justify-center border-2 border-brand-500/20">
                  <span class="text-2xl font-bold text-brand-300">
                    {{ authStore.user?.name?.charAt(0).toUpperCase() }}
                  </span>
                </div>
                <div>
                  <p class="text-sm font-medium text-slate-200">{{ authStore.user?.name }}</p>
                  <p class="text-xs text-slate-500 capitalize">{{ authStore.user?.role?.replace('_', ' ') }}</p>
                  <button type="button" class="text-xs text-brand-400 hover:text-brand-300 mt-1 transition-colors">
                    Change avatar
                  </button>
                </div>
              </div>

              <div>
                <label class="block text-xs font-medium text-slate-300 mb-1.5">Full Name</label>
                <input v-model="profileForm.name" type="text" class="input" />
              </div>
              <div>
                <label class="block text-xs font-medium text-slate-300 mb-1.5">Email Address</label>
                <input v-model="profileForm.email" type="email" class="input" />
              </div>

              <div class="flex items-center gap-2 pt-2">
                <button type="submit" class="btn-primary text-xs" :disabled="isSaving">
                  <LoadingSpinner v-if="isSaving" size="xs" />
                  Save Changes
                </button>
              </div>
            </form>
          </div>

          <div class="card mt-4 border-red-500/20">
            <h3 class="text-sm font-semibold text-white mb-1">Danger Zone</h3>
            <p class="text-xs text-slate-500 mb-4">Irreversible account actions</p>
            <button
              class="flex items-center gap-2 text-sm text-red-400 hover:text-red-300 font-medium transition-colors"
              @click="logout()"
            >
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
              </svg>
              Sign out of all devices
            </button>
          </div>
        </template>

        <!-- Organization -->
        <template v-else-if="activeTab === 'Organization'">
          <div class="card">
            <h3 class="text-sm font-semibold text-white mb-4">Organization Settings</h3>
            <div class="space-y-3 max-w-md">
              <div class="flex items-center justify-between py-3 border-b border-surface-600">
                <span class="text-xs text-slate-400">Organization Name</span>
                <span class="text-xs font-medium text-slate-200">{{ authStore.orgName }}</span>
              </div>
              <div class="flex items-center justify-between py-3 border-b border-surface-600">
                <span class="text-xs text-slate-400">Current Plan</span>
                <span class="badge-blue capitalize">{{ authStore.organization?.plan ?? 'starter' }}</span>
              </div>
              <div class="flex items-center justify-between py-3">
                <span class="text-xs text-slate-400">Your Role</span>
                <span class="text-xs font-medium text-slate-200 capitalize">{{ authStore.user?.role?.replace('_', ' ') }}</span>
              </div>
            </div>
          </div>
        </template>

        <!-- API Keys -->
        <template v-else-if="activeTab === 'API Keys'">
          <div class="card">
            <h3 class="text-sm font-semibold text-white mb-1">API Keys</h3>
            <p class="text-xs text-slate-500 mb-4">Manage API keys for external integrations</p>
            <EmptyState
              icon="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"
              title="No API keys yet"
              description="Generate an API key to integrate IntelliFlow with external services"
              action-label="Generate Key"
            />
          </div>
        </template>

        <!-- Billing placeholder -->
        <template v-else>
          <div class="card">
            <h3 class="text-sm font-semibold text-white mb-1">{{ activeTab }}</h3>
            <EmptyState title="Coming soon" description="This section is under development" />
          </div>
        </template>
      </div>
    </div>
  </div>
</template>
