<template>
  <AuthenticatedLayout>
    <div class="p-6 space-y-8">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800">Unit Payments</h2>
        <a
          :href="route('units.timeline', { unitId })"
          class="px-3 py-1 border rounded text-gray-700 hover:bg-gray-100"
        >
          ← Back to Unit Timeline
        </a>
      </div>

      <!-- Add Payment Form -->
      <div class="bg-white p-6 rounded-2xl shadow space-y-4">
        <h3 class="text-lg font-semibold text-gray-800"> Add Unit Payment</h3>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div>
            <label class="block text-xs text-gray-500 mb-1">Total Price *</label>
            <input type="number" v-model.number="newPayment.total_price" class="form-input" />
          </div>

          <div>
            <label class="block text-xs text-gray-500 mb-1">Installments Count *</label>
            <input type="number" v-model.number="newPayment.installments_count" class="form-input" />
          </div>

          <div>
            <label class="block text-xs text-gray-500 mb-1">Payment Type *</label>
            <select v-model="newPayment.payment_type" class="form-input">
              <option value="" disabled>Select type</option>
              <option value="cash">Cash</option>
              <option value="installments">Installments</option>
            </select>
          </div>

          <div>
            <label class="block text-xs text-gray-500 mb-1">Overall Status</label>
            <select v-model="newPayment.overall_status" class="form-input">
              <option value="pending">Pending</option>
              <option value="in_progress">In Progress</option>
              <option value="completed">Completed</option>
              <option value="overdue">Overdue</option>
            </select>
          </div>
        </div>

        <div class="flex justify-end">
          <button
            class="bg-black text-white px-3 py-2 rounded hover:bg-gray-700 disabled:opacity-50"
            :disabled="creating"
            @click="createPayment"
          >
            {{ creating ? 'Saving…' : 'Add Payment' }}
          </button>
        </div>
      </div>

      <!-- Payments Table -->
      <div class="bg-white p-6 rounded-2xl shadow">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-gray-800">Existing Payments</h3>
          <button
            @click="fetchPayments"
            class="px-3 py-1.5 text-sm border rounded-lg hover:bg-gray-50"
          >
            Refresh
          </button>
        </div>

        <div v-if="loading" class="text-sm text-gray-500">Loading…</div>

        <div v-else-if="!rows.length" class="text-center text-gray-500 py-8">
          No payments found.
        </div>

        <div v-else class="overflow-x-auto">
          <table class="min-w-full border border-gray-200 text-sm">
            <thead class="bg-gray-100 text-gray-700">
              <tr>
                <th class="py-2 px-3 text-left"></th>
                <th class="py-2 px-3 text-left">Total Price</th>
                <th class="py-2 px-3 text-left">Installments</th>
                <th class="py-2 px-3 text-left">Type</th>
                <th class="py-2 px-3 text-left">Status</th>
                <th class="py-2 px-3 text-center w-[220px]">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="p in rows"
                :key="p.id"
                class="border-t hover:bg-gray-50 transition"
              >
                <!-- ID -->
                <td class="py-2 px-3 text-gray-600"></td>

                <!-- Editable Total Price -->
                <td class="py-2 px-3">
                  <input
                    v-if="p.isEditing"
                    type="number"
                    v-model.number="p.editable.total_price"
                    class="form-input text-sm"
                  />
                  <span v-else>{{ p.total_price.toLocaleString() }}</span>
                </td>

                <!-- Editable Installments -->
                <td class="py-2 px-3">
                  <input
                    v-if="p.isEditing"
                    type="number"
                    v-model.number="p.editable.installments_count"
                    class="form-input text-sm"
                  />
                  <span v-else>{{ p.installments_count }}</span>
                </td>

                <!-- Editable Payment Type -->
                <td class="py-2 px-3">
                  <select
                    v-if="p.isEditing"
                    v-model="p.editable.payment_type"
                    class="form-input text-sm"
                  >
                    <option value="cash">Cash</option>
                    <option value="installments">Installments</option>
                  </select>
                  <span v-else class="capitalize">{{ p.payment_type }}</span>
                </td>

                <!-- Editable Status -->
                <td class="py-2 px-3">
                  <select
                    v-if="p.isEditing"
                    v-model="p.editable.overall_status"
                    class="form-input text-sm"
                  >
                    <option value="pending">Pending</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                    <option value="overdue">Overdue</option>
                  </select>
                  <span
                    v-else
                    class="px-2 py-0.5 rounded-full text-xs font-medium"
                    :class="statusColor(p.overall_status)"
                  >
                    {{ p.overall_status.replace('_', ' ') }}
                  </span>
                </td>

                <!-- Actions -->
                <td class="py-2 px-3 text-center">
                  <div class="flex justify-center gap-2">
                    <template v-if="p.isEditing">
                      <button
                        class="px-3 py-1 text-xs rounded bg-green-600 text-white hover:bg-green-700"
                        @click="saveEdit(p)"
                      >
                        Save
                      </button>
                      <button
                        class="px-3 py-1 text-xs rounded bg-gray-300 hover:bg-gray-400"
                        @click="cancelEdit(p)"
                      >
                        Cancel
                      </button>
                    </template>
                    <template v-else>
                      <button
                        class="px-3 py-1 text-xs border rounded text-blue-600 border-blue-200 hover:bg-blue-50"
                        @click="startEdit(p)"
                      >
                        Edit
                      </button>
                      <a
                        :href="route('units.installments', { unitId, unitPaymentId: p.id })"
                        class="px-3 py-1 text-xs border rounded text-green-600 border-green-200 hover:bg-green-50"
                      >
                        Installments
                      </a>
                      <button
                        class="px-3 py-1 text-xs border rounded text-red-600 border-red-200 hover:bg-red-50"
                        @click="removePayment(p.id)"
                      >
                        Delete
                      </button>
                    </template>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { ref, onMounted } from 'vue'
import {
  UnitPaymentsApi,
  buildUnitPaymentCreateFD,
  buildUnitPaymentUpdateFD
} from '@/Services/unitPayments.js'

const props = defineProps({
  unitId: { type: [Number, String], required: true },
})

/* State */
const rows = ref([])
const loading = ref(false)
const creating = ref(false)

/* New Payment Form */
const newPayment = ref({
  total_price: '',
  installments_count: '',
  payment_type: '',
  overall_status: 'pending',
})

/* Methods */
async function fetchPayments() {
  loading.value = true
  try {
    const data = await UnitPaymentsApi.list(props.unitId)
    rows.value = data.map(p => ({
      ...p,
      isEditing: false,
      editable: { ...p },
    }))
  } finally {
    loading.value = false
  }
}

async function createPayment() {
  creating.value = true
  try {
    const fd = buildUnitPaymentCreateFD(props.unitId, [newPayment.value])
    await UnitPaymentsApi.create(fd)
    await fetchPayments()
    newPayment.value = {
      total_price: '',
      installments_count: '',
      payment_type: '',
      overall_status: 'pending',
    }
  } finally {
    creating.value = false
  }
}

function startEdit(p) {
  p.isEditing = true
  p.editable = { ...p }
}
function cancelEdit(p) {
  p.isEditing = false
  p.editable = { ...p }
}
async function saveEdit(p) {
  const fd = buildUnitPaymentUpdateFD({ data: p.editable })
  await UnitPaymentsApi.update(p.id, fd)
  p.isEditing = false
  await fetchPayments()
}

async function removePayment(id) {
  if (!confirm('Delete this payment?')) return
  await UnitPaymentsApi.remove(id)
  await fetchPayments()
}

/* Utilities */
function statusColor(status) {
  switch (status) {
    case 'completed':
      return 'bg-green-100 text-green-700'
    case 'in_progress':
      return 'bg-blue-100 text-blue-700'
    case 'overdue':
      return 'bg-red-100 text-red-700'
    default:
      return 'bg-gray-100 text-gray-700'
  }
}

onMounted(fetchPayments)
</script>

<style scoped>
.form-input {
  @apply w-full border border-gray-300 rounded-md px-2 py-1 text-sm
         focus:outline-none focus:ring-2 focus:ring-blue-500;
}
table th, table td {
  @apply text-sm;
}
</style>
