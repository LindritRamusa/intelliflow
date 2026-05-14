<script setup lang="ts">
const { fetchNotifications } = useNotifications()
const authStore = useAuthStore()

onMounted(() => {
  if (authStore.isAuthenticated) {
    fetchNotifications()
  }
})
</script>

<template>
  <div class="min-h-screen bg-surface-900 flex">
    <AppSidebar />

    <div class="flex flex-col flex-1 min-w-0 ml-64">
      <AppTopbar />

      <main class="flex-1 pt-16 overflow-auto">
        <div class="p-6">
          <slot />
        </div>
      </main>
    </div>

    <NotificationPanel />
  </div>
</template>
