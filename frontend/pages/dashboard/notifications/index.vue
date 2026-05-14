<script setup lang="ts">
definePageMeta({ layout: 'dashboard', middleware: 'auth' })

const { notifications, unreadCount, isLoading, fetchNotifications, markRead, markAllRead } = useNotifications()

onMounted(() => fetchNotifications())

const typeIcon = (type: string) => {
  const map: Record<string, string> = {
    info: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
    success: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
    warning: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
    error: 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
  }
  return map[type] ?? map.info
}

const typeColor = (type: string) => {
  const map: Record<string, string> = {
    info: 'bg-brand-600/15 text-brand-400',
    success: 'bg-emerald-600/15 text-emerald-400',
    warning: 'bg-amber-600/15 text-amber-400',
    error: 'bg-red-600/15 text-red-400',
  }
  return map[type] ?? map.info
}
</script>

<template>
  <div>
    <PageHeader title="Notifications" :description="`${unreadCount} unread`">
      <button
        v-if="unreadCount > 0"
        class="btn-secondary text-xs"
        @click="markAllRead()"
      >
        Mark all as read
      </button>
    </PageHeader>

    <div v-if="isLoading" class="space-y-3">
      <div v-for="i in 5" :key="i" class="card animate-pulse flex items-start gap-4">
        <div class="h-9 w-9 bg-surface-700 rounded-xl flex-shrink-0" />
        <div class="flex-1 space-y-1.5">
          <div class="h-4 bg-surface-700 rounded w-1/2" />
          <div class="h-3 bg-surface-700 rounded w-3/4" />
        </div>
      </div>
    </div>

    <div v-else-if="notifications.length" class="space-y-2">
      <div
        v-for="n in notifications"
        :key="n.id"
        class="card flex items-start gap-4 cursor-pointer hover:border-surface-500 transition-all"
        :class="!n.read ? 'border-brand-500/20 bg-brand-500/5' : ''"
        @click="markRead(n.id)"
      >
        <div
          class="flex h-9 w-9 items-center justify-center rounded-xl flex-shrink-0"
          :class="typeColor(n.type)"
        >
          <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" :d="typeIcon(n.type)" />
          </svg>
        </div>
        <div class="flex-1 min-w-0">
          <div class="flex items-start justify-between gap-2">
            <p class="text-sm font-medium" :class="!n.read ? 'text-white' : 'text-slate-200'">
              {{ n.title }}
            </p>
            <div class="flex items-center gap-2 flex-shrink-0">
              <p class="text-[11px] text-slate-500">{{ n.createdAt }}</p>
              <div v-if="!n.read" class="h-2 w-2 rounded-full bg-brand-500" />
            </div>
          </div>
          <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">{{ n.message }}</p>
        </div>
      </div>
    </div>

    <EmptyState
      v-else
      icon="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
      title="No notifications"
      description="You're all caught up! Notifications will appear here."
    />
  </div>
</template>
