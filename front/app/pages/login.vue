<template>
    <section class="bg-[url('/img/mountain.jpg')] bg-cover bg-center">
        <div
            class="mx-auto flex h-screen flex-col items-center justify-end backdrop-blur-sm md:justify-center md:px-6 md:py-8 lg:py-0"
        >
            <div
                class="h-[60vh] w-full rounded-t-xl bg-white shadow dark:border dark:border-gray-700 dark:bg-gray-800 sm:max-w-md md:mt-0 md:h-fit md:rounded-md xl:p-0"
            >
                <UForm
                    :state="state"
                    :schema="schema"
                    :ref="form"
                    class="space-y-4 p-6 sm:p-8 md:space-y-6"
                    @submit="submitForm"
                >
                    <h1
                        class="text-xl font-bold leading-tight tracking-tight text-orange-300 dark:text-white"
                    >
                        Identifiez vous
                    </h1>
                    <UFormField name="username">
                        <UInput
                            class="w-full"
                            v-model="state.username"
                            :required="true"
                            placeholder="Nom"
                        />
                    </UFormField>
                    <UFormField name="password">
                        <UInput
                            class="w-full"
                            v-model="state.password"
                            placeholder="Mot de passe"
                            type="password"
                        />
                    </UFormField>
                    <p v-if="errorLogin" class="text-red-600">
                        {{ errorLogin }}
                    </p>
                    <div class="flex w-full items-center justify-between">
                        <NuxtLink
                            to="/forgot-password"
                            class="text-sm text-orange-300 hover:underline"
                        >
                            Mot de passe oublié ?
                        </NuxtLink>
                        <UCheckbox
                            v-model="state.remember_me"
                            label="Se souvenir de moi"
                            class="text-sm"
                        />
                    </div>
                    <UButton type="submit" label="Connexion" class="w-full justify-center" />
                </UForm>
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
import { computed, reactive } from "vue";
import { z } from "zod";
import { definePageMeta } from "#imports";
import { useAuth } from "../composables/auth";
import { sanitizeRedirect } from "../utils/redirect";

definePageMeta({
	middleware: ["guest"],
});

useSeoMeta({
	title: "Connexion · Prélèvements",
	description:
		"Page de connexion au suivi personnel de prélèvements. Service indépendant, sans lien avec une banque.",
	robots: "noindex, follow",
});

const { signIn } = useAuth();
const route = useRoute();
const form = ref();

// Where the auth middleware sent us from, so we land back on the wanted page
const callbackUrl = computed(() => sanitizeRedirect(route.query.redirect));

const schema = z.object({
	username: z.string(),
	password: z.string().min(8),
	remember_me: z.boolean().optional(),
});

const state = reactive({
	username: "",
	password: "",
	remember_me: false,
});

const errorLogin = ref();

const submitForm = async () => {
	const isFormCorrect = schema.safeParse(state).success;
	if (!isFormCorrect) return;

	const { error } = await signIn(state, { callbackUrl: callbackUrl.value });

	if (error) {
		errorLogin.value =
			error?.data?.message || error?.message || "Erreur de connexion";
	}
};
</script>

<style scoped></style>
