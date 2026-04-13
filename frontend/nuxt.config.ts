export default defineNuxtConfig({
  compatibilityDate: '2024-11-01',
  devtools: { enabled: true },
  modules: ['@pinia/nuxt'],
  app: {
    baseURL: '/entradas/',
  },
  runtimeConfig: {
    public: {
      apiBase: 'http://huevostyle.daw.inspedralbes.cat/entradas/backend-api/api',
      apiBaseInternal: 'http://huevostyle.daw.inspedralbes.cat/entradas/backend-api/api'
    }
  }
})
