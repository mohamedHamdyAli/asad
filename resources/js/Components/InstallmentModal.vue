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

        <!-- Form -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <!-- Title EN -->
          <div>
            <label class="block text-xs text-gray-500 mb-1">Title (EN) *</label>
            <input v-model="form.title.en" class="form-input" />
          </div>

          <!-- Title AR -->
          <div>
            <label class="block text-xs text-gray-500 mb-1">Title (AR) *</label>
            <input v-model="form.title.ar" class="form-input" dir="rtl" />
          </div>

          <!-- Description EN -->
          <div class="md:col-span-2">
            <label class="block text-xs text-gray-500 mb-1">Description (EN)</label>
            <textarea v-model="form.description.en" class="form-input" rows="2"></textarea>
          </div>

          <!-- Description AR -->
          <div class="md:col-span-2">
            <label class="block text-xs text-gray-500 mb-1">Description (AR)</label>
            <textarea
              v-model="form.description.ar"
              class="form-input"
              rows="2"
              dir="rtl"
            ></textarea>
          </div>

          <!-- Amount -->
          <div>
            <label class="block text-xs text-gray-500 mb-1">Amount *</label>
            <input
              v-model.number="form.amount"
              type="number"
              class="form-input"
            />
          </div>

          <!-- Percentage -->
          <div>
            <label class="block text-xs text-gray-500 mb-1">Percentage</label>
            <input
              v-model.number="form.percentage"
              type="number"
              step="0.01"
              class="form-input"
            />
          </div>

          <!-- Milestone Date -->
          <div>
            <label class="block text-xs text-gray-500 mb-1">Milestone Date</label>
            <input v-model="form.milestone_date" type="date" class="form-input" />
          </div>

          <!-- Submission Date -->
          <div>
            <label class="block text-xs text-gray-500 mb-1">Submission Date</label>
            <input v-model="form.submission_date" type="date" class="form-input" />
          </div>

          <!-- Consultant Approval Date -->
          <div>
            <label class="block text-xs text-gray-500 mb-1">Consultant Approval Date</label>
            <input v-model="form.consultant_approval_date" type="date" class="form-input" />
          </div>

          <!-- Due Date -->
          <div>
            <label class="block text-xs text-gray-500 mb-1">Due Date</label>
            <input v-model="form.due_date" type="date" class="form-input" />
          </div>

          <!-- Status -->
          <div>
            <label class="block text-xs text-gray-500 mb-1">Status</label>
            <select v-model="form.status" class="form-input">
              <option value="pending">Pending</option>
              <option value="paid">Paid</option>
              <option value="overdue">Overdue</option>
            </select>
          </div>
        </div>

        <!-- Divider -->
        <hr class="my-6 border-gray-200" />

        <!-- Admin Approval Step -->
        <div class="space-y-4">
          <h4 class="font-semibold text-gray-800 text-base">Admin Invoice Approval</h4>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="flex items-center gap-2">
              <input
                type="checkbox"
                v-model="form.admin_approved"
                id="admin_approved"
                class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded"
              />
              <label for="admin_approved" class="text-sm text-gray-700">Mark as Approved</label>
            </div>

            <div>
              <label class="block text-xs text-gray-500 mb-1">Approval Date</label>
              <input v-model="form.approval_date" type="date" class="form-input" />
            </div>

            <div class="md:col-span-2">
              <label class="block text-xs text-gray-500 mb-1">Admin Comment</label>
              <textarea
                v-model="form.admin_comment"
                class="form-input"
                rows="2"
                placeholder="Add approval notes..."
              ></textarea>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="flex justify-end mt-6 gap-3">
          <button
            class="px-4 py-2 border rounded hover:bg-gray-100"
            @click="$emit('close')"
          >
            Cancel
          </button>
          <button
            class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700"
            @click="save"
            :disabled="saving"
          >
            {{ saving ? 'Saving…' : 'Save' }}
          </button>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref } from 'vue'
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

/* ✅ Fix: safely normalize all nested objects */
function normalize(obj, defaultVal = {}) {
  if (!obj) return structuredClone(defaultVal)
  if (typeof obj === 'string') {
    try {
      return JSON.parse(obj)
    } catch {
      return structuredClone(defaultVal)
    }
  }
  return obj
}

/* ✅ Initialize form with safe defaults */
const form = ref({
  title: normalize(props.data?.title, { en: '', ar: '' }),
  description: normalize(props.data?.description, { en: '', ar: '' }),
  amount: props.data?.amount ?? '',
  percentage: props.data?.percentage ?? '',
  milestone_date: props.data?.milestone_date ?? '',
  submission_date: props.data?.submission_date ?? '',
  consultant_approval_date: props.data?.consultant_approval_date ?? '',
  due_date: props.data?.due_date ?? '',
  status: props.data?.status ?? 'pending',
  admin_approved: props.data?.admin_approved ?? false,
  approval_date: props.data?.approval_date ?? '',
  admin_comment: props.data?.admin_comment ?? '',
})

async function save() {
  saving.value = true
  try {
    if (props.mode === 'add') {
      const fd = buildInstallmentCreateFD(props.paymentId, form.value)
      await UnitInstallmentsApi.create(fd)
    } else {
      const fd = buildInstallmentUpdateFD(form.value)
      await UnitInstallmentsApi.update(props.data.id, fd)
    }
    emit('saved')
    emit('close')
  } catch (e) {
    console.error(e)
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
