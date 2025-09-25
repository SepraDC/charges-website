// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
	devServer: {
		host: "0.0.0.0",
	},
	vite: {
		server: {
			allowedHosts: true,
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
			apiBaseURL: process.env.API_PUBLIC_BASE_URL,
		},
		apiBaseURL: process.env.API_BASE_URL,
	},
	imports: {
		autoImport: true,
	},
	ui: {
		colorMode: false,
		theme: {
			colors: ["primary", "secondary", "warning", "info", "error", "neutral"],
		},
	},
	modules: ["@vueuse/nuxt", "@nuxt/ui", "@nuxt/image", "@vite-pwa/nuxt"],
	css: ["~/assets/css/main.css"],
});
