<script setup lang="ts">
import type { Article, CreateArticlePayload } from '~/types'

definePageMeta({ layout: 'dashboard' })

const { articles, categories, isLoading, error, total, fetchArticles, fetchCategories, createArticle, updateArticle, deleteArticle, togglePublish } = useArticles()

const searchQuery = ref('')
const activeCategory = ref('All')
const showEditorModal = ref(false)
const editingArticle = ref<Article | null>(null)
const viewingArticle = ref<Article | null>(null)
const confirmDeleteId = ref<string | null>(null)

let searchTimer: ReturnType<typeof setTimeout>
const handleSearch = () => {
  clearTimeout(searchTimer)
  searchTimer = setTimeout(() => fetchArticles({ search: searchQuery.value, category: activeCategory.value }), 350)
}

const handleCategoryFilter = (cat: string) => {
  activeCategory.value = cat
  fetchArticles({ search: searchQuery.value, category: cat })
}

const handleOpenEditor = (article?: Article) => {
  editingArticle.value = article ?? null
  showEditorModal.value = true
}

const handleEditorSubmit = async (payload: CreateArticlePayload) => {
  if (editingArticle.value) {
    await updateArticle(editingArticle.value.id, payload)
  } else {
    await createArticle(payload)
  }
  showEditorModal.value = false
  editingArticle.value = null
  fetchCategories()
}

const handleTogglePublish = async (article: Article) => {
  await togglePublish(article)
}

const handleDelete = async () => {
  if (!confirmDeleteId.value) return
  await deleteArticle(confirmDeleteId.value)
  confirmDeleteId.value = null
}

const publishedCount = computed(() => articles.value.filter(a => a.published).length)
const draftCount = computed(() => articles.value.filter(a => !a.published).length)
const totalViews = computed(() => articles.value.reduce((sum, a) => sum + (a.views ?? 0), 0))

onMounted(() => {
  fetchArticles()
  fetchCategories()
})
</script>

<template>
  <div class="p-6 space-y-6">
    <PageHeader title="Knowledge Base" description="Create and manage your organization's knowledge articles">
      <template #actions>
        <button
          class="flex items-center gap-2 px-4 py-2 bg-violet-600 hover:bg-violet-500 rounded-lg text-white text-sm font-medium transition-colors"
          @click="handleOpenEditor()"
        >
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
          </svg>
          New Article
        </button>
      </template>
    </PageHeader>

    <div class="grid grid-cols-3 gap-4">
      <KpiCard title="Total Articles" :value="total" icon="📄" />
      <KpiCard title="Published" :value="publishedCount" icon="✅" />
      <KpiCard title="Total Views" :value="totalViews" icon="👁️" />
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
            placeholder="Search articles..."
            @input="handleSearch"
          />
        </div>
        <div class="flex items-center gap-1 overflow-x-auto">
          <button
            v-for="cat in categories"
            :key="cat"
            :class="['px-3 py-1.5 rounded-lg text-xs whitespace-nowrap transition-colors', activeCategory === cat ? 'bg-violet-600 text-white' : 'text-gray-400 hover:text-white hover:bg-white/5']"
            @click="handleCategoryFilter(cat)"
          >
            {{ cat }}
          </button>
        </div>
      </div>

      <div class="p-4">
        <LoadingSpinner v-if="isLoading" class="py-12" />

        <div v-else-if="error" class="py-12 text-center">
          <p class="text-red-400 text-sm">{{ error }}</p>
          <button class="mt-3 text-violet-400 text-sm hover:underline" @click="fetchArticles()">Retry</button>
        </div>

        <EmptyState
          v-else-if="articles.length === 0"
          title="No articles yet"
          description="Create your first knowledge base article to get started"
          icon="📝"
        >
          <template #action>
            <button class="px-4 py-2 bg-violet-600 hover:bg-violet-500 rounded-lg text-white text-sm font-medium transition-colors" @click="handleOpenEditor()">
              New Article
            </button>
          </template>
        </EmptyState>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
          <ArticleCard
            v-for="article in articles"
            :key="article.id"
            :article="article"
            @edit="handleOpenEditor"
            @delete="confirmDeleteId = $event"
            @toggle-publish="handleTogglePublish"
            @view="viewingArticle = $event"
          />
        </div>

        <p v-if="articles.length > 0" class="text-xs text-gray-500 text-center mt-4">
          {{ draftCount }} draft{{ draftCount !== 1 ? 's' : '' }} · {{ publishedCount }} published
        </p>
      </div>
    </div>

    <ArticleEditorModal
      v-if="showEditorModal"
      :article="editingArticle"
      @close="showEditorModal = false; editingArticle = null"
      @submit="handleEditorSubmit"
    />

    <div
      v-if="viewingArticle"
      class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4"
      @click.self="viewingArticle = null"
    >
      <div class="bg-[#141824] border border-white/10 rounded-2xl w-full max-w-3xl max-h-[88vh] overflow-y-auto shadow-2xl">
        <div class="flex items-center justify-between p-6 border-b border-white/[0.06]">
          <div>
            <span class="px-2 py-0.5 bg-violet-500/10 border border-violet-500/20 rounded-full text-xs text-violet-400">
              {{ viewingArticle.category }}
            </span>
          </div>
          <button class="p-2 rounded-lg text-gray-400 hover:text-white hover:bg-white/5 transition-colors" @click="viewingArticle = null">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
        <div class="p-6">
          <h1 class="text-white font-bold text-2xl mb-2">{{ viewingArticle.title }}</h1>
          <div class="flex items-center gap-4 text-xs text-gray-500 mb-6">
            <span>By {{ viewingArticle.author?.name ?? 'Unknown' }}</span>
            <span>{{ viewingArticle.updatedAgo }}</span>
            <span>{{ viewingArticle.views }} views</span>
          </div>
          <div class="prose prose-invert prose-sm max-w-none text-gray-300 leading-relaxed whitespace-pre-wrap">
            {{ viewingArticle.content }}
          </div>
        </div>
      </div>
    </div>

    <ConfirmModal
      v-if="confirmDeleteId"
      title="Delete Article"
      description="This will permanently delete the article. This cannot be undone."
      confirm-label="Delete"
      @confirm="handleDelete"
      @cancel="confirmDeleteId = null"
    />
  </div>
</template>
