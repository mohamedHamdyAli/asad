<template>
  <Teleport to="body">
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
      <div
        class="bg-white rounded-xl shadow-xl w-full max-w-3xl p-6 relative animate-fadeIn"
      >
        <!-- Close -->
        <button
          @click="$emit('close')"
          class="absolute top-3 right-4 text-gray-500 hover:text-black"
        >
          ✕
        </button>

        <!-- Header -->
        <h3 class="text-xl font-semibold mb-4">
          {{ mode === 'add' ? 'Add Installment' : 'Edit Installment' }}
        </h3>

        <!-- FORM (Vee Validate Form) -->
        <Form @submit="handleSubmit" :validation-schema="schema">

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

            <!-- Amount -->
            <div>
              <label class="block text-xs text-gray-500 mb-1">Amount *</label>
              <Field name="amount" as="input" type="number" class="form-input" />
              <ErrorMessage name="amount" class="text-red-600 text-xs mt-1" />
            </div>

            <!-- Percentage -->
            <div>
              <label class="block text-xs text-gray-500 mb-1">Percentage</label>
              <Field name="percentage" as="input" type="number" class="form-input" />
              <ErrorMessage name="percentage" class="text-red-600 text-xs mt-1" />
            </div>

            <!-- Milestone Date -->
            <div>
              <label class="block text-xs text-gray-500 mb-1">Milestone Date</label>
              <Field name="milestone_date" as="input" type="date" class="form-input" />
            </div>

            <!-- Submission Date -->
            <div>
              <label class="block text-xs text-gray-500 mb-1">Submission Date</label>
              <Field name="submission_date" as="input" type="date" class="form-input" />
            </div>

            <!-- Consultant Approval -->
            <div>
              <label class="block text-xs text-gray-500 mb-1">Consultant Approval Date</label>
              <Field name="consultant_approval_date" as="input" type="date" class="form-input" />
            </div>

            <!-- Due Date -->
            <div>
              <label class="block text-xs text-gray-500 mb-1">Due Date</label>
              <Field name="due_date" as="input" type="date" class="form-input" />
            </div>

            <!-- Status -->
            <!-- <div>
              <label class="block text-xs text-gray-500 mb-1">Status</label>
              <Field name="status" as="select" class="form-input">
                <option value="pending">Pending</option>
                <option value="paid">Paid</option>
                <option value="overdue">Overdue</option>
              </Field>
            </div> -->
          </div>

          <!-- Footer -->
          <div class="flex justify-end mt-6 gap-3">
            <button
              class="px-4 py-2 border rounded hover:bg-gray-100"
              @click="$emit('close')"
              type="button"
            >
              Cancel
            </button>

            <button
              class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700"
              type="submit"
              :disabled="saving"
            >
              {{ saving ? 'Saving…' : 'Save' }}
            </button>
          </div>

        </Form>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref } from 'vue'
import * as yup from 'yup'
import { Form, Field, ErrorMessage } from 'vee-validate'

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
  amount: yup
    .number()
    .typeError('Amount must be a number')
    .required('Amount is required')
    .min(0, 'Amount must be >= 0'),
  percentage: yup
    .number()
    .nullable()
    .min(0, 'Min 0')
    .max(100, 'Max 100')
    .typeError('Percentage must be a number'),
  milestone_date: yup.string().nullable(),
  submission_date: yup.string().nullable(),
  consultant_approval_date: yup.string().nullable(),
  due_date: yup.string().nullable(),
  // status: yup.string().required(),
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

const initialValues = {
  title: normalize(props.data?.title, { en: '', ar: '' }),
  description: normalize(props.data?.description, { en: '', ar: '' }),
  amount: props.data?.amount ?? '',
  percentage: props.data?.percentage ?? '',
  milestone_date: props.data?.milestone_date ?? '',
  submission_date: props.data?.submission_date ?? '',
  consultant_approval_date: props.data?.consultant_approval_date ?? '',
  due_date: props.data?.due_date ?? '',
  // status: props.data?.status ?? 'pending',
}

/* ----------------------- SUBMIT HANDLER ----------------------- */
async function handleSubmit(values) {
  saving.value = true

  try {
    if (props.mode === 'add') {
      const fd = buildInstallmentCreateFD(props.paymentId, values)
      await UnitInstallmentsApi.create(fd)
    } else {
      const fd = buildInstallmentUpdateFD(values)
      await UnitInstallmentsApi.update(props.data.id, fd)
    }

    emit('saved')
    emit('close')

  } catch (err) {
    console.error(err)
    alert('Failed to save installment')

  } finally {
    saving.value = false
  }
}
</script>

<style scoped>
.form-input {
  @apply w-full border border-gray-300 rounded-md px-3 py-2
         focus:outline-none focus:ring-2 focus:ring-blue-500;
}
</style>
