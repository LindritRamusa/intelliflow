<script setup lang="ts">
const { notifications, isPanelOpen, isLoading, closePanel, markRead, markAllRead } = useNotifications()

const typeIcon = (type: string) => {
  const icons: Record<string, string> = {
    info: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
    success: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
    warning: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
    error: 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
  }
  return icons[type] ?? icons.info
}

const typeColor = (type: string) => {
  const colors: Record<string, string> = {
    info: 'text-brand-400',
    success: 'text-emerald-400',
    warning: 'text-amber-400',
    error: 'text-red-400',
  }
  return colors[type] ?? colors.info
}
</script>

<template>
  <!-- Overlay -->
  <Transition name="fade">
    <div
      v-if="isPanelOpen"
      class="fixed inset-0 z-40 bg-black/30 backdrop-blur-sm"
      @click="closePanel()"
    />
  </Transition>

  <!-- Panel -->
  <Transition name="slide-right">
    <div
      v-if="isPanelOpen"
      class="fixed right-0 top-0 h-full w-96 bg-surface-800 border-l border-surface-600 z-50 flex flex-col shadow-2xl"
    >
      <!-- Header -->
      <div class="flex items-center justify-between px-5 py-4 border-b border-surface-600">
        <div>
          <h2 class="text-sm font-semibold text-white">Notifications</h2>
          <p class="text-xs text-slate-500">{{ notifications.length }} total</p>
        </div>
        <div class="flex items-center gap-2">
          <button
            class="text-xs text-brand-400 hover:text-brand-300 transition-colors font-medium"
            @click="markAllRead()"
          >
            Mark all read
          </button>
          <button
            class="p-1.5 rounded-lg text-slate-400 hover:text-slate-200 hover:bg-surface-700 transition-colors"
            @click="closePanel()"
          >
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>

      <!-- List -->
      <div class="flex-1 overflow-y-auto">
        <div v-if="isLoading" class="flex items-center justify-center h-32">
          <LoadingSpinner size="sm" />
        </div>

        <template v-else-if="notifications.length">
          <button
            v-for="n in notifications"
            :key="n.id"
            class="w-full flex items-start gap-3 px-5 py-4 border-b border-surface-700 hover:bg-surface-700/50 transition-colors text-left"
            :class="!n.read ? 'bg-surface-700/30' : ''"
            @click="markRead(n.id)"
          >
            <div class="flex-shrink-0 mt-0.5">
              <svg class="h-4 w-4" :class="typeColor(n.type)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" :d="typeIcon(n.type)" />
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-xs font-medium text-slate-200 mb-0.5" :class="!n.read ? 'text-white' : ''">
                {{ n.title }}
              </p>
              <p class="text-xs text-slate-500 line-clamp-2">{{ n.message }}</p>
              <p class="text-[10px] text-slate-600 mt-1">{{ n.createdAt }}</p>
            </div>
            <div v-if="!n.read" class="h-2 w-2 rounded-full bg-brand-500 flex-shrink-0 mt-1.5" />
          </button>
        </template>

        <EmptyState
          v-else
          icon="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
          title="No notifications"
          description="You're all caught up!"
        />
      </div>
    </div>
  </Transition>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.slide-right-enter-active,
.slide-right-leave-active {
  transition: transform 0.25s ease;
}
.slide-right-enter-from,
.slide-right-leave-to {
  transform: translateX(100%);
}
</style>
