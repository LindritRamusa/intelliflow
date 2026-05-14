<script setup lang="ts">
definePageMeta({ layout: 'dashboard', middleware: 'auth' })

const searchQuery = ref('')
const selectedCategory = ref('All')

const categories = ['All', 'Processes', 'HR Policies', 'Technical', 'Onboarding', 'Security']

const articles = ref([
  { id: '1', title: 'Employee Onboarding Process', category: 'Onboarding', excerpt: 'Step-by-step guide for onboarding new team members, including account setup, tool access, and first-week schedule.', updatedAt: '2 days ago', views: 142 },
  { id: '2', title: 'Data Security & Compliance Guidelines', category: 'Security', excerpt: 'Company policies on data handling, access control, GDPR compliance, and incident reporting procedures.', updatedAt: '1 week ago', views: 89 },
  { id: '3', title: 'Workflow Automation Best Practices', category: 'Technical', excerpt: 'Guidelines for building reliable, maintainable workflow automations using IntelliFlow. Includes naming conventions and testing strategies.', updatedAt: '3 days ago', views: 203 },
  { id: '4', title: 'Performance Review Framework', category: 'HR Policies', excerpt: 'How to conduct quarterly performance reviews, set goals, and provide constructive feedback using our review template.', updatedAt: '2 weeks ago', views: 67 },
  { id: '5', title: 'API Integration Guide', category: 'Technical', excerpt: 'How to connect third-party services via webhooks and REST APIs. Includes authentication, rate limiting, and error handling.', updatedAt: '5 days ago', views: 178 },
  { id: '6', title: 'Incident Response Procedure', category: 'Processes', excerpt: 'Documented process for identifying, escalating, and resolving operational incidents within defined SLAs.', updatedAt: '1 month ago', views: 45 },
])

const filteredArticles = computed(() => {
  let result = articles.value
  if (selectedCategory.value !== 'All') {
    result = result.filter((a) => a.category === selectedCategory.value)
  }
  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase()
    result = result.filter((a) => a.title.toLowerCase().includes(q) || a.excerpt.toLowerCase().includes(q))
  }
  return result
})
</script>

<template>
  <div>
    <PageHeader title="Knowledge Base" description="Company documentation with AI-powered search">
      <button class="btn-primary text-xs">
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        New Article
      </button>
    </PageHeader>

    <!-- Search -->
    <div class="flex items-center gap-2 bg-surface-700 border border-surface-600 rounded-xl px-4 py-3 mb-5 focus-within:border-brand-500 transition-colors">
      <svg class="h-4 w-4 text-slate-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
      </svg>
      <input
        v-model="searchQuery"
        type="text"
        placeholder="Search documentation with AI semantic search..."
        class="bg-transparent text-sm text-slate-200 placeholder-slate-500 focus:outline-none flex-1"
      />
      <span class="text-[10px] text-slate-600 hidden sm:block">Powered by AI embeddings</span>
    </div>

    <!-- Categories -->
    <div class="flex items-center gap-2 mb-5 flex-wrap">
      <button
        v-for="cat in categories"
        :key="cat"
        class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors"
        :class="selectedCategory === cat ? 'bg-brand-600 text-white' : 'bg-surface-700 border border-surface-600 text-slate-400 hover:text-slate-200'"
        @click="selectedCategory = cat"
      >
        {{ cat }}
      </button>
    </div>

    <!-- Articles grid -->
    <div v-if="filteredArticles.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div
        v-for="article in filteredArticles"
        :key="article.id"
        class="card hover:border-brand-500/30 hover:bg-surface-700/40 transition-all cursor-pointer flex flex-col gap-3"
      >
        <div class="flex items-start justify-between gap-2">
          <h3 class="text-sm font-semibold text-slate-200 leading-snug">{{ article.title }}</h3>
          <span class="badge-blue text-[10px] flex-shrink-0">{{ article.category }}</span>
        </div>
        <p class="text-xs text-slate-500 leading-relaxed line-clamp-3 flex-1">{{ article.excerpt }}</p>
        <div class="flex items-center justify-between text-[11px] text-slate-600 pt-1 border-t border-surface-600">
          <span>Updated {{ article.updatedAt }}</span>
          <span class="flex items-center gap-1">
            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
            {{ article.views }}
          </span>
        </div>
      </div>
    </div>

    <EmptyState
      v-else
      icon="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
      title="No articles found"
      :description="searchQuery ? 'Try a different search term' : 'Create the first article for your team'"
    />
  </div>
</template>
