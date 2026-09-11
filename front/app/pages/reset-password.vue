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
                        Nouveau mot de passe
                    </h1>
                    <template v-if="noToken">
                        <UAlert
                            color="error"
                            variant="subtle"
                            title="Lien invalide ou expiré"
                            :actions="[
                                {
                                    label: 'Demander un nouveau lien',
                                    color: 'warning',
                                    to: '/forgot-password',
                                },
                            ]"
                        >
                            <NuxtLink
                                to="/forgot-password"
                                class="text-sm text-orange-300 hover:underline"
                            >
                                Demander un nouveau lien
                            </NuxtLink>
                        </UAlert>
                    </template>
                    <template v-else-if="success">
                        <UAlert type="success">
                            Mot de passe réinitialisé ! Redirection en cours...
                        </UAlert>
                    </template>
                    <template v-else>
                        <UFormField name="password">
                            <UInput
                                class="w-full"
                                v-model="state.password"
                                type="password"
                                placeholder="Nouveau mot de passe"
                            />
                        </UFormField>
                        <UFormField name="confirm">
                            <UInput
                                class="w-full"
                                v-model="state.confirm"
                                type="password"
                                placeholder="Confirmer le mot de passe"
                            />
                        </UFormField>
                        <p v-if="errorMessage" class="text-red-600">
                            {{ errorMessage }}
                        </p>
                        <div class="flex w-full items-center justify-center">
                            <UButton type="submit" label="Réinitialiser" />
                        </div>
                    </template>
                </UForm>
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
import { reactive, ref } from "vue";
import { z } from "zod";

// No guest middleware here on purpose: a reset link comes from an email and
// must work even when a session is already open.
useSeoMeta({
	title: "Nouveau mot de passe · Prélèvements",
	description:
		"Définissez un nouveau mot de passe pour votre compte de suivi de prélèvements.",
	robots: "noindex, follow",
});

const route = useRoute();
const token = route.query.token as string | undefined;
const noToken = !token;

const api = useApiRoutes();

const schema = z
	.object({
		password: z.string().min(8, "8 caractères minimum"),
		confirm: z.string().min(8, "8 caractères minimum"),
	})
	.refine((d) => d.password === d.confirm, {
		message: "Les mots de passe ne correspondent pas",
		path: ["confirm"],
	});

const state = reactive({ password: "", confirm: "" });
const success = ref(false);
const errorMessage = ref<string | null>(null);

const submitForm = async () => {
	if (!schema.safeParse(state).success || !token) return;
	errorMessage.value = null;
	try {
		await api.password.resetPassword(token, state.password);
		success.value = true;
		setTimeout(() => navigateTo("/login"), 1500);
	} catch (err: unknown) {
		const msg = (err as { data?: { message?: string } })?.data?.message;
		errorMessage.value = msg ?? "Lien invalide ou expiré.";
	}
};
</script>

<style scoped></style>
