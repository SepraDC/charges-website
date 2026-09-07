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
                    class="space-y-4 p-6 sm:p-8 md:space-y-6"
                    @submit="submitForm"
                >
                    <h1
                        class="text-xl font-bold leading-tight tracking-tight text-orange-300 dark:text-white"
                    >
                        Mot de passe oublié
                    </h1>
                    <template v-if="!submitted">
                        <UFormField name="email">
                            <UInput
                                class="w-full"
                                v-model="state.email"
                                type="email"
                                placeholder="Adresse email"
                            />
                        </UFormField>
                        <div class="flex w-full items-center justify-between">
                            <NuxtLink
                                to="/login"
                                class="text-sm text-orange-300 hover:underline"
                            >
                                Retour à la connexion
                            </NuxtLink>
                            <UButton type="submit" label="Envoyer le lien" />
                        </div>
                    </template>
                    <template v-else>
                        <p class="text-green-600">
                            Si un compte est associé à cet email, un lien de réinitialisation vous a été envoyé.
                        </p>
                        <NuxtLink to="/login" class="text-sm text-orange-300 hover:underline">
                            Retour à la connexion
                        </NuxtLink>
                    </template>
                </UForm>
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
import { reactive, ref } from "vue";
import { z } from "zod";
import { definePageMeta } from "#imports";

definePageMeta({
	middleware: ["guest"],
});

useSeoMeta({
	title: "Mot de passe oublié · Prélèvements",
	description:
		"Réinitialisation du mot de passe de votre compte de suivi de prélèvements.",
	robots: "noindex, follow",
});

const api = useApiRoutes();

const schema = z.object({
	email: z.string().email("Email invalide"),
});

const state = reactive({ email: "" });
const submitted = ref(false);

const submitForm = async () => {
	if (!schema.safeParse(state).success) return;
	try {
		await api.password.forgotPassword(state.email);
	} catch {
		// Swallow errors — never reveal if the email exists
	}
	submitted.value = true;
};
</script>

<style scoped></style>
