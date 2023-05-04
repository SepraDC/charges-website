// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  modules: [
    '@vueuse/nuxt',
    '@sidebase/nuxt-auth',
    '@nuxtjs/tailwindcss',
  ],
  auth: {
    baseURL: "https://api.sepradc.local/",
    provider: {
      type: 'local',
      endpoints: {
        signIn: {path: "/auth", method: "post"},
        getSession: {path: "/verify", method: "get"}
      },
    },
    globalAppMiddleware: {
      isEnabled: true
    }
  }
})
