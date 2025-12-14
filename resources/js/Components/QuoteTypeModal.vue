<template>
  <Teleport to="body">
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-lg p-6 relative">

        <!-- Close -->
        <button
          @click="$emit('close')"
          class="absolute top-3 right-4 text-gray-500 hover:text-black"
        >
          ✕
        </button>

        <h3 class="text-xl font-semibold mb-4">
          {{ mode === "add" ? "Add Item" : "Edit Item" }}
        </h3>

        <!-- SERVER ERRORS -->
        <div
          v-if="serverError"
          class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm"
        >
          {{ serverError }}
        </div>

        <!-- FORM -->
        <Form @submit="submitForm" :validation-schema="schema" v-slot="{ errors }">

          <!-- Title EN -->
          <div class="mb-3">
            <label class="label">Title (EN) *</label>
            <Field name="title_en" class="form-input" />
            <span class="error">{{ errors.title_en }}</span>
          </div>

          <!-- Title AR -->
          <div class="mb-3">
            <label class="label">Title (AR) *</label>
            <Field name="title_ar" class="form-input" dir="rtl" />
            <span class="error">{{ errors.title_ar }}</span>
          </div>

          <!-- PRICE FIELD (only for building) -->
          <div v-if="type === 'building'" class="mb-3">
            <label class="label">Price</label>
            <Field name="price" type="number" class="form-input" />
            <span class="error">{{ errors.price }}</span>
          </div>

          <!-- Action Buttons -->
          <div class="flex justify-end mt-6 gap-3">
            <button type="button" @click="$emit('close')" class="px-4 py-2 border rounded">
              Cancel
            </button>

            <button
              type="submit"
              class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700"
              :disabled="saving"
            >
              {{ saving ? "Saving…" : "Save" }}
            </button>
          </div>
        </Form>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, watch } from "vue";
import { Form, Field } from "vee-validate";
import * as yup from "yup";

import { TypeOfBuildingApi } from "@/Services/typeOfBuilding";
import { TypeOfPriceApi } from "@/Services/typeOfPrice";

const props = defineProps({
  mode: String, // add | edit
  type: String, // building | price
  item: Object, // null if add mode
});
const emit = defineEmits(["close", "saved"]);

const saving = ref(false);
const serverError = ref("");

// ----------------------------
//  VALIDATION SCHEMA
// ----------------------------
const schema = yup.object({
  title_en: yup.string().required("English title is required"),
  title_ar: yup.string().required("Arabic title is required"),
  price: props.type === "building"
    ? yup.number().nullable().min(0, "Price must be >= 0")
    : yup.mixed().nullable(),
});

// ----------------------------
//  INITIAL VALUES
// ----------------------------
const initialValues = ref({
  title_en: "",
  title_ar: "",
  price: null,
});

// Load edit item values
watch(
  () => props.item,
  (item) => {
    if (!item) return;
    const parsed = safeTitle(item.title);

    initialValues.value = {
      title_en: parsed.en,
      title_ar: parsed.ar,
      price: item.price ?? null,
    };
  },
  { immediate: true }
);

// Safe title parse
function safeTitle(val) {
  if (!val) return { en: "", ar: "" };
  try {
    const obj = JSON.parse(val);
    return { en: obj.en ?? "", ar: obj.ar ?? "" };
  } catch {
    return { en: "", ar: "" };
  }
}

// ----------------------------
//  SUBMIT
// ----------------------------
async function submitForm(values) {
  saving.value = true;
  serverError.value = "";

  const payload =
    props.mode === "add"
      ? {
          data: [
            {
              title: { en: values.title_en, ar: values.title_ar },
              price: props.type === "building" ? values.price : null,
            },
          ],
        }
      : {
          data: {
            title: { en: values.title_en, ar: values.title_ar },
            price: props.type === "building" ? values.price : null,
          },
        };

  try {
    if (props.mode === "add") {
      if (props.type === "building") {
        await TypeOfBuildingApi.create(payload);
      } else {
        await TypeOfPriceApi.create(payload);
      }
    } else {
      if (props.type === "building") {
        await TypeOfBuildingApi.update(props.item.id, payload);
      } else {
        await TypeOfPriceApi.update(props.item.id, payload);
      }
    }

    emit("saved");
    emit("close");
  } catch (e) {
    console.error(e);
    serverError.value =
      e.response?.data?.message || "Server error. Please try again.";
  } finally {
    saving.value = false;
  }
}
</script>

<style scoped>
.label {
  @apply block text-xs text-gray-500 mb-1;
}
.form-input {
  @apply w-full border border-gray-300 rounded px-3 py-2 text-sm;
}
.error {
  @apply text-red-600 text-xs;
}
</style>
