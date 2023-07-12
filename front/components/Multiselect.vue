<template>
  <Combobox
    v-model="selectedItem"
    as="div"
    @update:model-value="$emit('update:modelValue', $event)"
  >
    <ComboboxLabel
      class="mb-2 block text-sm font-medium leading-6 text-gray-900"
      >{{ label }}</ComboboxLabel
    >
    <div class="relative">
      <ComboboxInput
        class="w-full rounded-md border-0 bg-gray-100 py-1.5 pl-3 pr-10 text-gray-900 shadow-sm focus:outline-orange-300 sm:text-sm sm:leading-6"
        :display-value="(person) => person?.name"
        :placeholder="placeholder"
        @change="query = $event.target.value"
      />
      <ComboboxButton
        class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-none"
      >
        <InlineSvg src="/svg/bottom-chevron.svg"></InlineSvg>
      </ComboboxButton>

      <ComboboxOptions
        v-if="filteredItem.length > 0"
        class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm"
      >
        <ComboboxOption
          v-for="item in filteredItem"
          :key="item.id"
          v-slot="{ active, selected }"
          :value="item"
          as="template"
        >
          <li
            :class="[
              'relative cursor-default select-none py-2 pl-3 pr-9',
              active ? 'bg-orange-300 text-white' : 'text-black',
            ]"
          >
            <span
              :class="[
                'block truncate',
                selected && 'font-semibold text-orange-600',
              ]"
            >
              {{ item.name }}
            </span>

            <span
              v-if="selected"
              :class="[
                'absolute inset-y-0 right-0 flex items-center pr-4',
                active ? 'text-white' : 'text-black',
              ]"
            >
            </span>
          </li>
        </ComboboxOption>
      </ComboboxOptions>
    </div>
  </Combobox>
</template>

<script setup lang="ts">
import { computed, ref } from "vue";
import {
  Combobox,
  ComboboxButton,
  ComboboxInput,
  ComboboxLabel,
  ComboboxOption,
  ComboboxOptions,
} from "@headlessui/vue";
import InlineSvg from "vue-inline-svg";

const props = defineProps<{
  items: any[];
  label?: string;
  modelValue?: any;
  placeholder?: string;
}>();

defineEmits<{
  "update:modelValue": [value: any];
}>();

const query = ref("");
const selectedItem = ref(props.modelValue || undefined);
const filteredItem = computed(() =>
  query.value === ""
    ? props.items || []
    : props.items.filter((item) => {
        return item.name.toLowerCase().includes(query.value.toLowerCase());
      })
);
</script>
