<template>
  <form class="mt-5 flex flex-col gap-3" @submit.prevent="submitCharge">
    <div>
      <input
        v-model="v$.name.$model"
        class="text-md mb-2 block w-full rounded-md bg-gray-100 px-5 py-2 invalid:border-red-300 focus:outline-orange-300"
        placeholder="Nom"
        type="text"
      />
      <span v-if="v$.name.$error" class="text-red-600">
        {{ v$.name.$errors[0]?.$message }}
      </span>
    </div>
    <div>
      <Multiselect v-model="v$.bank.$model" :items="banks"></Multiselect>
      <span v-if="v$.bank.$error" class="text-red-600">
        {{ v$.bank.$errors[0]?.$message }}
      </span>
    </div>
    <div>
      <Multiselect
        v-model="v$.chargeType.$model"
        placeholder="Banque"
        :items="chargeTypes"
      ></Multiselect>
      <span v-if="v$.chargeType.$error" class="text-red-600">
        {{ v$.chargeType.$errors[0]?.$message }}
      </span>
    </div>
    <div>
      <input
        v-model="v$.date.$model"
        class="text-md mb-2 block w-full rounded-md bg-gray-100 px-5 py-2 invalid:border-red-300 focus:outline-orange-300"
        placeholder="Date"
        type="date"
      />
      <span v-if="v$.date.$error" class="text-red-600">
        {{ v$.date.$errors[0]?.$message }}
      </span>
    </div>
    <div>
      <input
        v-model="v$.amount.$model"
        step="0.01"
        class="text-md mb-2 block w-full rounded-md bg-gray-100 px-5 py-2 invalid:border-red-300 focus:outline-orange-300"
        placeholder="Montant (100€)"
        type="number"
      />
      <span v-if="v$.amount.$error" class="text-red-600">
        {{ v$.amount.$errors[0]?.$message }}
      </span>
    </div>
    <div class="flex w-full items-center justify-center">
      <button
        type="submit"
        class="rounded-md bg-green-400 px-5 py-2 text-white hover:bg-green-500 focus:outline-green-600"
      >
        Enregistrer
      </button>
    </div>
  </form>
</template>

<script setup lang="ts">
import { minValue, required } from "@vuelidate/validators";
import useVuelidate from "@vuelidate/core";
import { computed, reactive } from "vue";
import { Bank } from "../@type/Bank";
import { useApiRoutes, getMembers } from "../composables/api";
import { Charge } from "../@type/Charge";
import Multiselect from "./Multiselect.vue";

const api = useApiRoutes();

const props = defineProps<{
  initialValue?: Charge;
}>();

const emit = defineEmits<{
  onSubmit: [value: any];
}>();

const currentUser = ref();

const { authUser } = useAuthState();
currentUser.value = authUser.value;

const chargeRules = computed(() => ({
  name: { required },
  amount: { required, minValue: minValue(0) },
  state: { required },
  bank: { required },
  chargeType: { required },
  date: { required },
}));

const { data: banks } = useAsyncData("banks", () => {
  return getMembers(api.banks.getCollection());
});

const { data: chargeTypes } = useAsyncData("chargeTypes", () => {
  return getMembers(api.chargeTypes.getCollection());
});

const chargeState = reactive({
  name: props.initialValue?.name || "",
  amount: props.initialValue?.amount || "",
  state: props.initialValue?.state || false,
  bank: props.initialValue?.bank || banks.value?.[0],
  chargeType: props.initialValue?.chargeType || chargeTypes.value?.[0],
  date: props.initialValue?.date || "",
});
const v$ = useVuelidate(chargeRules, chargeState);

const submitCharge = async () => {
  const isFormCorrect = await v$.value.$validate();
  if (!isFormCorrect) {
    return;
  }

  const charge = {
    name: v$.value.name.$model,
    amount: v$.value.amount.$model.toString(),
    state: v$.value.state.$model,
    date: v$.value.date.$model,
    bank: v$.value.bank.$model?.["@id"],
    chargeType: v$.value.chargeType.$model?.["@id"],
    user: currentUser.value["@id"],
  };
  emit("onSubmit", { value: charge });
};
</script>

<style scoped></style>
