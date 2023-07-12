<template>
  <section class="bg-[url('~/assets/img/mountain.jpg')] bg-cover bg-center">
    <div
      class="mx-auto flex h-screen flex-col items-center justify-end backdrop-blur-sm md:justify-center md:px-6 md:py-8 lg:py-0"
    >
      <div
        class="h-[60vh] w-full rounded-t-xl bg-white shadow dark:border dark:border-gray-700 dark:bg-gray-800 sm:max-w-md md:mt-0 md:h-fit md:rounded-md xl:p-0"
      >
        <form
          class="space-y-4 p-6 sm:p-8 md:space-y-6"
          @submit.prevent="submitForm"
        >
          <h1
            class="text-xl font-bold leading-tight tracking-tight text-orange-300 dark:text-white"
          >
            Identifiez vous
          </h1>
          <div>
            <input
              v-model="v$.username.$model"
              :required="true"
              class="text-md mb-2 block w-full rounded-md bg-gray-100 px-5 py-2 invalid:border-red-300 invalid:outline-red-500 focus:outline-orange-300"
              placeholder="Nom"
            />
            <span v-if="v$.username.$error" class="text-red-600">
              {{ v$.username.$errors[0].$message }}
            </span>
          </div>
          <div>
            <input
              v-model="v$.password.$model"
              class="text-md mb-2 block w-full rounded-md bg-gray-100 px-5 py-2 invalid:border-red-300 focus:outline-orange-300"
              placeholder="Mot de passe"
              type="password"
            />
            <span v-if="v$.password.$error" class="text-red-600">
              {{ v$.password.$errors[0]?.$message }}
            </span>
          </div>
          <span v-if="errorLogin" class="text-red-600">{{ errorLogin }}</span>
          <div class="flex w-full items-center justify-center">
            <button
              type="submit"
              class="rounded-md bg-green-400 px-5 py-2 text-white hover:bg-green-500 focus:outline-green-600"
            >
              Connexion
            </button>
          </div>
        </form>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { reactive, computed } from "vue";
import useVuelidate from "@vuelidate/core";
import { required, minLength } from "@vuelidate/validators";
import { definePageMeta } from "../.nuxt/imports";

definePageMeta({
  auth: {
    unauthenticatedOnly: true,
    navigateAuthenticatedTo: "/",
  },
});

const { signIn } = useAuth();

const rules = computed(() => {
  return {
    username: { required },
    password: { required, minLength: minLength(8) },
  };
});

const state = reactive({
  username: "",
  password: "",
});

const errorLogin = ref();

const v$ = useVuelidate(rules, state);

const submitForm = async () => {
  const isFormCorrect = await v$.value.$validate();
  if (!isFormCorrect) return;
  try {
    await signIn(
      {
        username: v$.value.username.$model,
        password: v$.value.password.$model,
      },
      { callbackUrl: "/" }
    );
  } catch (e) {
    console.error(e);
  }
};
</script>

<style scoped></style>
