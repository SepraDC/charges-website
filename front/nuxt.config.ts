// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  devServer: {
    host: "0.0.0.0",
  },
  vite: {
    server: {
      hmr: {
        protocol: "wss",
      },
    },
  },
  devtools: {
    enabled: true,
  },
  runtimeConfig: {
    public: {
      API_BASE_URL: process.env.API_PUBLIC_BASE_URL,
    },
    API_BASE_URL: process.env.API_BASE_URL,
  },
  colorMode: {
    preference: "light",
  },
  modules: [
    "@vueuse/nuxt",
    "@nuxt/ui",
    "@nuxt/image",
    "@nuxt/eslint",
  ],
});