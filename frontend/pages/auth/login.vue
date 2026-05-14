<script setup lang="ts">
definePageMeta({ layout: 'default', middleware: 'guest' })

const { login } = useAuth()
const authStore = useAuthStore()

const form = reactive({
  email: '',
  password: '',
})

const handleSubmit = async () => {
  await login(form)
}
</script>

<template>
  <div class="min-h-screen bg-surface-900 flex items-center justify-center p-4 bg-gradient-mesh">
    <div class="w-full max-w-md">
      <!-- Logo -->
      <div class="flex items-center gap-3 mb-8 justify-center">
        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-600/20">
          <svg class="h-5 w-5 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
          </svg>
        </div>
        <div>
          <p class="text-base font-bold text-white">IntelliFlow</p>
          <p class="text-[10px] font-medium uppercase tracking-widest text-brand-400">AI Platform</p>
        </div>
      </div>

      <!-- Card -->
      <div class="glass-panel p-8">
        <div class="mb-6">
          <h1 class="text-xl font-bold text-white">Welcome back</h1>
          <p class="text-sm text-slate-400 mt-1">Sign in to your workspace</p>
        </div>

        <!-- Error -->
        <div
          v-if="authStore.error"
          class="mb-4 flex items-start gap-2.5 px-3 py-3 rounded-lg bg-red-500/10 border border-red-500/20"
        >
          <svg class="h-4 w-4 text-red-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <p class="text-xs text-red-300">{{ authStore.error }}</p>
        </div>

        <form class="space-y-4" @submit.prevent="handleSubmit">
          <div>
            <label class="block text-xs font-medium text-slate-300 mb-1.5">Email</label>
            <input
              v-model="form.email"
              type="email"
              class="input"
              placeholder="you@company.com"
              autocomplete="email"
              required
            />
          </div>

          <div>
            <label class="block text-xs font-medium text-slate-300 mb-1.5">Password</label>
            <input
              v-model="form.password"
              type="password"
              class="input"
              placeholder="••••••••"
              autocomplete="current-password"
              required
            />
          </div>

          <button
            type="submit"
            class="btn-primary w-full justify-center text-sm py-2.5 mt-2"
            :disabled="authStore.isLoading"
          >
            <LoadingSpinner v-if="authStore.isLoading" size="xs" />
            {{ authStore.isLoading ? 'Signing in...' : 'Sign in' }}
          </button>
        </form>

        <p class="text-center text-xs text-slate-500 mt-5">
          Don't have an account?
          <NuxtLink to="/auth/register" class="text-brand-400 hover:text-brand-300 font-medium transition-colors">
            Create one
          </NuxtLink>
        </p>
      </div>

      <p class="text-center text-[11px] text-slate-600 mt-6">
        Enterprise AI Automation Platform
      </p>
    </div>
  </div>
</template>
