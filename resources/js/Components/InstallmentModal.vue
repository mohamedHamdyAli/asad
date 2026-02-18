<template>
  <Teleport to="body">
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-xl relative max-h-[85vh] flex flex-col">
        <!-- Close -->
        <button @click="$emit('close')" class="absolute top-3 right-4 text-gray-500 hover:text-black z-20">
          ✕
        </button>

        <!-- Header (sticky) -->
        <div class="px-6 pt-5 pb-3 border-b">
          <h3 class="text-lg font-semibold">
            {{ mode === 'add' ? 'Add Invoice' : 'Edit Invoice' }}
          </h3>
        </div>

        <!-- Body (scrollable) -->
<div class="px-6 py-4 overflow-y-auto flex-1">
            <!-- FORM (Vee Validate Form) -->
          <Form @submit="handleSubmit" :initial-values="initialValues" :validation-schema="schema">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <!-- Title EN -->
              <div>
                <label class="block text-xs text-gray-500 mb-1">Title (EN) *</label>
                <Field name="title.en" as="input" class="form-input" />
                <ErrorMessage name="title.en" class="text-red-600 text-xs mt-1" />
              </div>

              <!-- Title AR -->
              <div>
                <label class="block text-xs text-gray-500 mb-1">Title (AR) *</label>
                <Field name="title.ar" as="input" class="form-input" dir="rtl" />
                <ErrorMessage name="title.ar" class="text-red-600 text-xs mt-1" />
              </div>

              <!-- Description EN -->
              <div class="md:col-span-2">
                <label class="block text-xs text-gray-500 mb-1">Description (EN)</label>
                <Field name="description.en" as="textarea" rows="2" class="form-input" />
                <ErrorMessage name="description.en" class="text-red-600 text-xs mt-1" />
              </div>

              <!-- Description AR -->
              <div class="md:col-span-2">
                <label class="block text-xs text-gray-500 mb-1">Description (AR)</label>
                <Field name="description.ar" as="textarea" rows="2" class="form-input" dir="rtl" />
                <ErrorMessage name="description.ar" class="text-red-600 text-xs mt-1" />
              </div>

              <!-- Paid Amount -->
              <div>
                <label class="block text-xs text-gray-500 mb-1">Paid Amount *</label>
                <Field name="paid_amount" as="input" type="number" class="form-input" />
                <ErrorMessage name="paid_amount" class="text-red-600 text-xs mt-1" />
              </div>

              <!-- Percentage -->
              <div>
                <label class="block text-xs text-gray-500 mb-1">Percentage</label>
                <Field name="percentage" as="input" type="number" class="form-input" />
                <ErrorMessage name="percentage" class="text-red-600 text-xs mt-1" />
              </div>

              <!-- Payment Date -->
              <div>
                <label class="block text-xs text-gray-500 mb-1">Payment Date *</label>
                <Field name="payment_date" as="input" type="date" class="form-input" />
                <ErrorMessage name="payment_date" class="text-red-600 text-xs mt-1" />
              </div>

              <!-- Invoice File -->
              <div class="md:col-span-2">
                <label class="block text-xs text-gray-500 mb-1">Invoice File *</label>
                <input type="file" accept=".jpg,.jpeg,.png,.pdf" @change="onFileChange" class="form-input" />
                <p v-if="fileError" class="text-red-600 text-xs mt-1">{{ fileError }}</p>
                <p v-if="props.data?.invoice_file && props.mode === 'edit'" class="text-xs text-green-600 mt-1">
                  Current file: {{ props.data.invoice_file.split('/').pop() }}
                </p>
              </div>

            </div>

            <!-- Footer (sticky) -->
 <div class="px-6 py-4 bg-white flex justify-end gap-3">              <button class="px-4 py-2 border rounded hover:bg-gray-100" @click="$emit('close')" type="button">
                Cancel
              </button>

              <button class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700" type="submit"
                :disabled="saving">
                {{ saving ? 'Saving…' : 'Save' }}
              </button>
            </div>
          </Form>
        </div>
      </div>
    </div>
  </Teleport>
</template>


<script setup>
import { ref, watch } from 'vue'
import * as yup from 'yup'
import { Form, Field, ErrorMessage } from 'vee-validate'
import { useServerError } from '@/composables/useServerError'

const { show } = useServerError()

import {
  UnitInstallmentsApi,
  buildInstallmentCreateFD,
  buildInstallmentUpdateFD,
} from '@/Services/unitInstallments'

const props = defineProps({
  mode: String,
  data: Object,
  paymentId: [String, Number],
})
const emit = defineEmits(['close', 'saved'])

const saving = ref(false)
const invoiceFile = ref(null)
const fileError = ref('')

function onFileChange(e) {
  invoiceFile.value = e.target.files[0] || null
  if (invoiceFile.value) fileError.value = ''
}

/* ----------------------- SCHEMA ----------------------- */
const schema = yup.object({
  title: yup.object({
    en: yup.string().required('Title (EN) is required'),
    ar: yup.string().required('Title (AR) is required'),
  }),
  description: yup.object({
    en: yup.string().nullable(),
    ar: yup.string().nullable(),
  }),
  paid_amount: yup
    .number()
    .typeError('Paid amount must be a number')
    .required('Paid amount is required')
    .min(0, 'Paid amount must be >= 0'),
  percentage: yup
    .number()
    .nullable()
    .min(0, 'Min 0')
    .max(100, 'Max 100')
    .typeError('Percentage must be a number'),
  payment_date: yup.string().required('Payment date is required'),
})

/* ----------------------- INITIAL VALUES ----------------------- */
function normalize(obj, fallback) {
  if (!obj) return fallback
  if (typeof obj === 'string') {
    try {
      return JSON.parse(obj)
    } catch {
      return fallback
    }
  }
  return obj
}

const initialValues = ref({
  title: normalize(props.data?.title, { en: '', ar: '' }),
  description: normalize(props.data?.description, { en: '', ar: '' }),
  paid_amount: props.data?.paid_amount ?? '',
  percentage: props.data?.percentage ?? '',
  payment_date: props.data?.payment_date ?? '',
})

watch(
  () => props.data,
  (v) => {
    if (!v) return;

    initialValues.value = {
      title: normalize(v.title, { en: '', ar: '' }),
      description: normalize(v.description, { en: '', ar: '' }),
      paid_amount: v.paid_amount ?? '',
      percentage: v.percentage ?? '',
      payment_date: v.payment_date ?? '',
    };
  },
  { immediate: true }
);


/* ----------------------- SUBMIT HANDLER ----------------------- */
async function handleSubmit(values) {
  if (props.mode === 'add' && !invoiceFile.value) {
    fileError.value = 'Invoice file is required'
    return
  }
  saving.value = true
  try {
    const payload = { ...values }
    if (invoiceFile.value) payload.invoice_file = invoiceFile.value

    if (props.mode === 'add') {
      const fd = buildInstallmentCreateFD(props.paymentId, payload)
      await UnitInstallmentsApi.create(fd)
    } else {
      payload.unit_payment_id = props.paymentId
      const fd = buildInstallmentUpdateFD(payload)
      await UnitInstallmentsApi.update(props.data.id, fd)
    }

    emit('saved')
    emit('close')
  } catch (err) {
    console.error(err)
    show(err)
    alert('Failed to save installment')
  } finally {
    saving.value = false
  }
}

</script>

<style scoped>
.form-input {
  @apply w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500;
}
.modal-body {
  overscroll-behavior: contain;
}

</style>
