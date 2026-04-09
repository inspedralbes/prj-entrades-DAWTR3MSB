export default defineNuxtConfig({
  compatibilityDate: '2024-11-01',
  devtools: { enabled: true },
  modules: ['@pinia/nuxt'],
  app: {
    baseURL: '/entradas/',
  },
  runtimeConfig: {
    public: {
      apiBase: process.env.NUXT_PUBLIC_API_BASE || 'http://localhost:8000/api',
      apiBaseInternal: process.env.NUXT_PUBLIC_API_BASE_INTERNAL || 'http://api:8000/api'
    }
  }
})
