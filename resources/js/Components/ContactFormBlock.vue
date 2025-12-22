<template>
  <div class="space-y-4">

    <!-- TELEPHONE -->
    <FormField label="Telephone" name="telephone" v-model="local.telephone" :error="errors.telephone" />

    <!-- BANK FIELDS -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <FormField label="Account Name" name="account_name" v-model="local.account_name" :error="errors.account_name" />
      <FormField label="Bank Name" name="bank_name" v-model="local.bank_name" :error="errors.bank_name" />
      <FormField label="IBAN" name="iban" v-model="local.iban" :error="errors.iban" />
      <FormField label="Currency" name="currency" v-model="local.currency" :error="errors.currency" />
      <FormField label="Swift Code" name="swift_code" v-model="local.swift_code" :error="errors.swift_code" />
    </div>

    <!-- MAP PICKER -->
    <div>
      <label class="text-xs text-gray-500">Location</label>
      <MapPicker
        :lat="local.lat"
        :lng="local.long"
        :address="local.location"
        @update:lat="val => local.lat = val"
        @update:lng="val => local.long = val"
        @update:address="val => local.location = val"
      />
    </div>

    <!-- Location Fields -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <FormField label="Location Text" name="location" v-model="local.location" :error="errors.location" />
      <FormField label="Latitude" type="number" v-model="local.lat" :error="errors.lat" />
      <FormField label="Longitude" type="number" v-model="local.long" :error="errors.long" />
    </div>

    <!-- Save -->
    <button
      @click="submitForm"
      class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700"
    >
      Save {{ country }}
    </button>

    <!-- Server Error -->
    <div v-if="serverError" class="text-red-600 text-sm mt-2">
      {{ serverError }}
    </div>

    <!-- Success Msg -->
    <div v-if="success" class="text-green-600 text-sm mt-2">
      Saved successfully!
    </div>

  </div>
</template>

<script setup>
import { reactive, watch, ref } from "vue";
import * as yup from "yup";
import { useForm } from "vee-validate";

import MapPicker from "@/Components/MapPicker.vue";
import FormField from "@/Components/FormField.vue";

const props = defineProps({
  country: String,
  data: Object,
});

const emit = defineEmits(["update:data", "save"]);

/* Local copy */
const local = reactive({ ...props.data });

watch(local, () => emit("update:data", local), { deep: true });

/* Validation schema */
const schema = yup.object({
  telephone: yup.string().required("Telephone is required"),
  account_name: yup.string().required("Account Name required"),
  bank_name: yup.string().required("Bank Name required"),
  iban: yup.string().required("IBAN required"),
  currency: yup.string().required("Currency required"),
  swift_code: yup.string().required("Swift Code required"),
  location: yup.string().required("Location text is required"),
  lat: yup.number().required("Latitude required"),
  long: yup.number().required("Longitude required"),
});

const { validate } = useForm({ validationSchema: schema });

const errors = reactive({});
const serverError = ref("");
const success = ref(false);

async function submitForm() {
  success.value = false;
  serverError.value = "";
  Object.keys(errors).forEach(k => delete errors[k]);

  const result = await validate(local);

  if (!result.valid) {
    Object.assign(errors, result.errors);
    return;
  }

  try {
    await emit("save", local);
    success.value = true;
  } catch (e) {
    serverError.value = "Server error occurred";
  }
}
</script>

<style scoped>
.text-error {
  @apply text-red-600 text-xs;
}
</style>
