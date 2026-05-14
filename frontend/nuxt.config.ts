export default defineNuxtConfig({
  devtools: { enabled: true },

  components: [
    { path: '~/components', pathPrefix: false },
  ],

  modules: [
    '@nuxtjs/tailwindcss',
    '@pinia/nuxt',
    '@vueuse/nuxt',
    '@nuxtjs/color-mode',
  ],

  colorMode: {
    preference: 'dark',
    fallback: 'dark',
    classSuffix: '',
  },

  typescript: {
    strict: true,
    typeCheck: false,
  },

  runtimeConfig: {
    openrouterApiKey: process.env.OPENROUTER_API_KEY,
    public: {
      apiBase: process.env.NUXT_PUBLIC_API_BASE || 'http://localhost:8000/api',
    },
  },

  tailwindcss: {
    cssPath: '~/assets/css/main.css',
    configPath: '~/tailwind.config.ts',
  },

  app: {
    head: {
      title: 'IntelliFlow — AI Automation Platform',
      meta: [
        { charset: 'utf-8' },
        { name: 'viewport', content: 'width=device-width, initial-scale=1' },
        {
          name: 'description',
          content: 'Enterprise AI automation SaaS platform for intelligent workflows, copilots, and business analytics.',
        },
      ],
      link: [
        { rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' },
        { rel: 'preconnect', href: 'https://fonts.googleapis.com' },
        {
          rel: 'stylesheet',
          href: 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap',
        },
      ],
    },
  },

  compatibilityDate: '2025-01-01',
})
