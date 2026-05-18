<script setup lang="ts">
import type { NotificationType } from '~/types'

definePageMeta({ layout: 'dashboard' })

const { notifications, unreadCount, isLoading, fetchNotifications, markRead, markAllRead } = useNotifications()

const activeFilter = ref<'all' | 'unread' | NotificationType>('all')

const filterTabs = [
  { key: 'all', label: 'All' },
  { key: 'unread', label: 'Unread' },
  { key: 'success', label: 'Success' },
  { key: 'info', label: 'Info' },
  { key: 'warning', label: 'Warning' },
  { key: 'error', label: 'Error' },
]

const filteredNotifications = computed(() => {
  if (activeFilter.value === 'all') return notifications.value
  if (activeFilter.value === 'unread') return notifications.value.filter(n => !n.read)
  return notifications.value.filter(n => n.type === activeFilter.value)
})

const typeConfig: Record<NotificationType, { icon: string; class: string }> = {
  info: { icon: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z', class: 'bg-blue-500/10 text-blue-400' },
  success: { icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', class: 'bg-emerald-500/10 text-emerald-400' },
  warning: { icon: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z', class: 'bg-amber-500/10 text-amber-400' },
  error: { icon: 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z', class: 'bg-red-500/10 text-red-400' },
}

const sourceTypeLabel = (notification: (typeof notifications.value)[0]): string => {
  const data = (notification as Record<string, unknown>).data as { type?: string } | undefined
  const typeMap: Record<string, string> = {
    workflow: 'Workflow',
    automation: 'Automation',
    recruitment: 'Recruitment',
    knowledge: 'Knowledge Base',
  }
  return typeMap[data?.type ?? ''] ?? 'System'
}

const formatTime = (dateStr: string): string => {
  try {
    const d = new Date(dateStr)
    const now = new Date()
    const diff = now.getTime() - d.getTime()
    const mins = Math.floor(diff / 60000)
    if (mins < 1) return 'just now'
    if (mins < 60) return `${mins}m ago`
    const hours = Math.floor(mins / 60)
    if (hours < 24) return `${hours}h ago`
    return d.toLocaleDateString()
  } catch {
    return dateStr
  }
}

onMounted(() => fetchNotifications())
</script>

<template>
  <div class="p-6 space-y-6">
    <PageHeader title="Notifications" :description="unreadCount > 0 ? `${unreadCount} unread notification${unreadCount !== 1 ? 's' : ''}` : 'All caught up'">
      <template #actions>
        <button
          v-if="unreadCount > 0"
          class="flex items-center gap-2 px-4 py-2 bg-white/5 hover:bg-white/[0.08] border border-white/10 rounded-lg text-gray-300 text-sm transition-colors"
          @click="markAllRead()"
        >
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
          </svg>
          Mark all read
        </button>
      </template>
    </PageHeader>

    <div class="flex items-center gap-1 overflow-x-auto">
      <button
        v-for="tab in filterTabs"
        :key="tab.key"
        :class="['px-3 py-1.5 rounded-lg text-xs whitespace-nowrap transition-colors', activeFilter === tab.key ? 'bg-violet-600 text-white' : 'text-gray-400 hover:text-white hover:bg-white/5']"
        @click="activeFilter = tab.key as typeof activeFilter"
      >
        {{ tab.label }}
        <span v-if="tab.key === 'unread' && unreadCount > 0" class="ml-1 px-1.5 py-0.5 bg-violet-400/20 rounded-full text-[10px] text-violet-300">
          {{ unreadCount }}
        </span>
      </button>
    </div>

    <div class="space-y-2">
      <div v-if="isLoading" class="space-y-2">
        <div v-for="i in 6" :key="i" class="bg-[#141824] border border-white/[0.06] rounded-xl p-4 flex items-start gap-4 animate-pulse">
          <div class="w-9 h-9 bg-white/5 rounded-xl shrink-0" />
          <div class="flex-1 space-y-2">
            <div class="h-4 bg-white/5 rounded w-1/3" />
            <div class="h-3 bg-white/5 rounded w-2/3" />
          </div>
        </div>
      </div>

      <template v-else-if="filteredNotifications.length > 0">
        <div
          v-for="n in filteredNotifications"
          :key="n.id"
          :class="[
            'bg-[#141824] border rounded-xl p-4 flex items-start gap-4 cursor-pointer transition-all group',
            !n.read ? 'border-violet-500/20 bg-violet-500/[0.03]' : 'border-white/[0.06] hover:border-white/10'
          ]"
          @click="markRead(n.id)"
        >
          <div :class="['w-9 h-9 rounded-xl flex items-center justify-center shrink-0', typeConfig[n.type].class]">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" :d="typeConfig[n.type].icon"/>
            </svg>
          </div>

          <div class="flex-1 min-w-0">
            <div class="flex items-start justify-between gap-3">
              <div class="min-w-0">
                <p :class="['text-sm font-medium truncate', !n.read ? 'text-white' : 'text-gray-200']">
                  {{ n.title }}
                </p>
                <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">{{ n.message }}</p>
              </div>
              <div class="flex items-center gap-2 shrink-0">
                <span class="text-[10px] px-1.5 py-0.5 bg-white/5 rounded text-gray-500">
                  {{ sourceTypeLabel(n) }}
                </span>
                <span class="text-xs text-gray-500">{{ formatTime(n.createdAt) }}</span>
                <div v-if="!n.read" class="w-2 h-2 rounded-full bg-violet-400 shrink-0" />
              </div>
            </div>
          </div>
        </div>
      </template>

      <EmptyState
        v-else
        title="No notifications"
        :description="activeFilter === 'unread' ? 'All notifications have been read' : 'Notifications will appear here automatically'"
        icon="🔔"
      />
    </div>
  </div>
</template>
