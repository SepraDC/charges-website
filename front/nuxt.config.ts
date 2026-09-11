// https://nuxt.com/docs/api/configuration/nuxt-config
const siteUrl = process.env.NUXT_PUBLIC_SITE_URL || "https://prel.sepradc.ovh";

const siteName = "Prélèvements";
const siteDescription =
    "Suivi personnel de prélèvements et charges mensuelles. Service indépendant, sans lien avec une banque : aucune donnée bancaire, montants saisis à la main.";

export default defineNuxtConfig({
    ssr: false, // Disable SSR
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
    app: {
        head: {
            htmlAttrs: {
                lang: "fr",
            },
            title: `${siteName} – suivi personnel de charges mensuelles`,
            meta: [
                { charset: "utf-8" },
                { name: "viewport", content: "width=device-width, initial-scale=1" },
                { name: "description", content: siteDescription },
                { name: "robots", content: "index, follow, max-snippet:-1" },
                { name: "application-name", content: siteName },
                { name: "theme-color", content: "#3b82f6" },
                { property: "og:type", content: "website" },
                { property: "og:site_name", content: siteName },
                { property: "og:locale", content: "fr_FR" },
                { property: "og:url", content: siteUrl },
                {
                    property: "og:title",
                    content: `${siteName} – suivi personnel de charges mensuelles`,
                },
                { property: "og:description", content: siteDescription },
                { property: "og:image", content: `${siteUrl}/img/mountain.jpg` },
                { name: "twitter:card", content: "summary_large_image" },
                {
                    name: "twitter:title",
                    content: `${siteName} – suivi personnel de charges mensuelles`,
                },
                { name: "twitter:description", content: siteDescription },
                { name: "twitter:image", content: `${siteUrl}/img/mountain.jpg` },
            ],
            link: [
                { rel: "canonical", href: siteUrl },
                { rel: "icon", type: "image/x-icon", href: "/favicon.ico" },
                { rel: "icon", type: "image/svg+xml", href: "/icon.svg" },
                {
                    rel: "apple-touch-icon",
                    sizes: "180x180",
                    href: "/apple-touch-icon-180x180.png",
                },
            ],
        },
    },
    runtimeConfig: {
        public: {
            apiBaseURL: process.env.API_PUBLIC_BASE_URL,
            siteUrl,
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
    pwa: {
        manifest: {
            name: `${siteName} – suivi personnel de charges`,
            short_name: siteName,
            description: siteDescription,
            lang: "fr",
            start_url: "/",
            display: "standalone",
            background_color: "#ffffff",
            theme_color: "#3b82f6",
            icons: [
                { src: "/pwa-64x64.png", sizes: "64x64", type: "image/png" },
                { src: "/pwa-192x192.png", sizes: "192x192", type: "image/png" },
                {
                    src: "/pwa-512x512.png",
                    sizes: "512x512",
                    type: "image/png",
                    purpose: "any",
                },
                {
                    src: "/maskable-icon-512x512.png",
                    sizes: "512x512",
                    type: "image/png",
                    purpose: "maskable",
                },
            ],
        },
    },
});
