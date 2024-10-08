<template>
  <UForm class="mt-5 flex flex-col gap-3" :state="chargeState" @submit="submitCharge">
    <div>
      <UInput
        v-model="v$.name.$model"
        size="lg"
        placeholder="Nom"
        type="text"
      />
      <span v-if="v$.name.$error" class="text-red-600">
        {{ v$.name.$errors[0]?.$message }}
      </span>
    </div>
    <div>
      <UInputMenu v-model="v$.bank.$model" size="lg" :options="banks" option-attribute="name"/>
      <span v-if="v$.bank.$error" class="text-red-600">
        {{ v$.bank.$errors[0]?.$message }}
      </span>
    </div>
    <div>
      <UInputMenu
        v-model="v$.chargeType.$model"
        size="lg"
        placeholder="Type"
        :options="chargeTypes"
        option-attribute="name"
      />
      <span v-if="v$.chargeType.$error" class="text-red-600">
        {{ v$.chargeType.$errors[0]?.$message }}
      </span>
    </div>
    <div>
      <UInput
        v-model="v$.date.$model"
        size="lg"
        placeholder="Date"
        type="date"
      />
      <span v-if="v$.date.$error" class="text-red-600">
        {{ v$.date.$errors[0]?.$message }}
      </span>
    </div>
    <div>
      <UInput
      v-model="v$.amount.$model"
        trailing-icon="i-mdi-euro"
        size="lg"
        step="0.01"
        placeholder="Montant (100€)"
        type="number"
      />
      <span v-if="v$.amount.$error" class="text-red-600">
        {{ v$.amount.$errors[0]?.$message }}
      </span>
    </div>
    <div class="flex w-full items-center justify-center">
      <UButton
        type="submit"
        class="rounded-md bg-green-400 px-5 py-2 text-white hover:bg-green-500 focus:outline-green-600"
        label="Enregistrer"
      />
    </div>
  </UForm>
</template>

<script setup lang="ts">
import type { Charge } from "@type/Charge";
import useVuelidate from "@vuelidate/core";
import { minValue, required } from "@vuelidate/validators";
import { computed, reactive } from "vue";
import { getMembers, useApiRoutes } from "../composables/api";
import { format } from "date-fns";

const api = useApiRoutes();

const props = defineProps<{ 
  initialValue?: Charge;
}>();

const emit = defineEmits<{
  onSubmit: [value];
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
  amount: props.initialValue?.amount || 0,
  state: props.initialValue?.state || false,
  bank: props.initialValue?.bank || banks.value?.[0],
  chargeType: props.initialValue?.chargeType || chargeTypes.value?.[0],
  date: props.initialValue?.date ? format(new Date(props.initialValue?.date), 'yyyy-MM-dd') : '',
});
const v$ = useVuelidate(chargeRules, chargeState);

const submitCharge = async () => {
  const isFormCorrect = await v$.value.$validate();
  if (!isFormCorrect) {
    return;
  }

  const charge: Charge = {
    name: v$.value.name.$model,
    amount: v$.value.amount.$model,
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
