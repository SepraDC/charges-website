<template>
  <div class="flex flex-col gap-5">
    <div
      :class="{ error: v$.username.$errors.length }"
      class="flex flex-col gap-5"
    >
      <div>
        <input
          v-model="v$.username.$model"
          class="bg-gray-200 rounded-md px-5 py-2 invalid:border-red-300"
          placeholder="Nom"
        />
        <span v-if="v$.username.$error">
          {{ v$.username.$errors[0].$message }}
        </span>
      </div>
      <div>
        <input
          v-model="v$.password.$model"
          class="bg-gray-200 rounded-md px-5 py-2 invalid:border-red-300"
          placeholder="Mot de passe"
          type="password"
        />
        <span v-if="v$.password.$error" class="text-red-600">
          {{ v$.password.$errors[0].$message }}
        </span>
      </div>
    </div>
    <span>{{ auth.data }}</span>
    <button class="bg-green-400 text-white p-2 rounded-md" @click="submitForm">
      Username and Password
    </button>
  </div>
</template>

<script setup lang="ts">
import { reactive, computed } from "vue";
import useVuelidate from "@vuelidate/core";
import { required, minLength } from "@vuelidate/validators";

definePageMeta({
  auth: false,
});

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

const v$ = useVuelidate(rules, state);
const { signIn } = useAuth();

const { ...auth } = useAuthState();

const submitForm = async () => {
  const isFormCorrect = await v$.value.$validate();
  if (!isFormCorrect) return;
  await signIn(
    {
      username: v$.value.username.$model,
      password: v$.value.password.$model,
    },
    { callbackUrl: "/" }
  );
};
</script>
