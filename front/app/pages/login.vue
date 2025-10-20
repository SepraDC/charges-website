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
                    <div class="flex w-full items-center justify-center">
                        <UButton type="submit" label="Connexion" />
                    </div>
                </UForm>
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
import { reactive } from "vue";
import { z } from "zod";
import { definePageMeta } from "#imports";
import { useAuth } from "../composables/auth";

definePageMeta({
	auth: {
		unauthenticatedOnly: true,
		navigateAuthenticatedTo: "/",
	},
});

const { signIn } = useAuth();
const form = ref();

const schema = z.object({
	username: z.string(),
	password: z.string().min(8),
});

const state = reactive({
	username: "",
	password: "",
});

const errorLogin = ref();

const submitForm = async () => {
	const isFormCorrect = schema.safeParse(state).success;
	if (!isFormCorrect) return;

	const { error } = await signIn(state, { callbackUrl: "/" });

	if (error) {
		errorLogin.value =
			error?.data?.message || error?.message || "Erreur de connexion";
	}
};
</script>

<style scoped></style>
