<template>
    <AuthenticatedLayout>
        <div class="container mx-auto p-6">
            <!-- Tabs -->
            <div class="flex border-b mb-6">
                <button :class="tab === 'payments' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-600'"
                    class="px-4 py-2 font-medium" @click="tab = 'payments'">
                    Payments
                </button>
                <button :class="tab === 'installments' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-600'"
                    class="px-4 py-2 font-medium" @click="tab = 'installments'">
                    Installments
                </button>
            </div>

            <!-- Add Payment -->
            <div v-if="tab === 'payments'" class="bg-white p-6 rounded shadow mb-6">
                <h3 class="text-lg font-semibold mb-4">Add Payment</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Total Price *</label>
                        <input v-model.number="newPayment.total_price" type="number"
                            class="form-control w-full border p-2 rounded" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Installments Count *</label>
                        <input v-model.number="newPayment.installments_count" type="number"
                            class="form-control w-full border p-2 rounded" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Payment Type *</label>
                        <select v-model="newPayment.payment_type" class="form-control w-full border p-2 rounded">
                            <option disabled value="">Select type</option>
                            <option value="cash">Cash</option>
                            <option value="installments">Installments</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Overall Status *</label>
                        <select v-model="newPayment.overall_status" class="form-control w-full border p-2 rounded">
                            <option value="pending">Pending</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                            <option value="overdue">Overdue</option>
                        </select>
                    </div>
                </div>

                <div class="mt-4 text-right">
                    <button @click="createPayment" :disabled="creating"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded disabled:opacity-50">
                        {{ creating ? 'Saving…' : 'Save' }}
                    </button>
                </div>
            </div>

            <!-- Payments Table -->
            <div v-if="tab === 'payments'" class="bg-white p-6 rounded shadow">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">Existing Payments</h3>
                    <button @click="fetchPayments" class="border px-3 py-1 rounded hover:bg-gray-100">Refresh</button>
                </div>

                <div v-if="loadingPayments" class="text-sm text-gray-500">Loading…</div>
                <div v-else-if="!payments.length" class="text-center text-gray-500 py-6">No payments found.</div>

                <div v-else class="overflow-x-auto">
                    <table class="min-w-full border text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="p-2 text-left"></th>
                                <th class="p-2 text-left">Total Price</th>
                                <th class="p-2 text-left">Installments</th>
                                <th class="p-2 text-left">Type</th>
                                <th class="p-2 text-left">Status</th>
                                <th class="p-2 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(p, i) in payments" :key="p.id" class="border-t">
                                <td class="p-2">{{ i + 1 }}</td>

                                <td class="p-2">
                                    <input v-if="p.isEditing" v-model.number="p.editable.total_price" type="number"
                                        class="w-full border p-1 rounded" />
                                    <span v-else>{{ formatPrice(p.total_price) }}</span>
                                </td>

                                <td class="p-2">
                                    <input v-if="p.isEditing" v-model.number="p.editable.installments_count"
                                        type="number" class="w-full border p-1 rounded" />
                                    <span v-else>{{ p.installments_count }}</span>
                                </td>

                                <td class="p-2">
                                    <select v-if="p.isEditing" v-model="p.editable.payment_type"
                                        class="w-full border p-1 rounded">
                                        <option value="cash">Cash</option>
                                        <option value="installments">Installments</option>
                                    </select>
                                    <span v-else>{{ p.payment_type }}</span>
                                </td>

                                <td class="p-2">
                                    <select v-if="p.isEditing" v-model="p.editable.overall_status"
                                        class="w-full border p-1 rounded">
                                        <option value="pending">Pending</option>
                                        <option value="in_progress">In Progress</option>
                                        <option value="completed">Completed</option>
                                        <option value="overdue">Overdue</option>
                                    </select>
                                    <span v-else>{{ p.overall_status }}</span>
                                </td>

                                <td class="p-2 text-center">
                                    <div class="flex justify-center gap-2">
                                        <template v-if="p.isEditing">
                                            <button
                                                class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-1 rounded"
                                                @click="saveEdit(p)">
                                                Save
                                            </button>
                                            <button class="bg-gray-300 hover:bg-gray-400 text-xs px-3 py-1 rounded"
                                                @click="cancelEdit(p)">
                                                Cancel
                                            </button>
                                        </template>
                                        <template v-else>
                                            <button
                                                class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-1 rounded"
                                                @click="startEdit(p)">
                                                Edit
                                            </button>
                                            <button
                                                class="bg-red-600 hover:bg-red-700 text-white text-xs px-3 py-1 rounded"
                                                @click="deletePayment(p.id)">
                                                Delete
                                            </button>
                                            <!-- <button class="bg-gray-200 hover:bg-gray-300 text-xs px-3 py-1 rounded"
                                                @click="openInstallmentsTab(p.id)">
                                                View Installments
                                            </button> -->
                                        </template>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

      <!-- ===================== Installments Tab ===================== -->
      <div v-if="tab === 'installments'" class="space-y-4">
        <!-- Payment Selector -->
        <div class="flex gap-3 items-end">
          <div class="flex-1">
            <label class="block text-xs text-gray-500 mb-1">Select Payment</label>
            <select v-model="selectedPaymentId" class="form-input" @change="fetchInstallments">
              <option value="">-- Choose Payment --</option>
              <option v-for="p in payments" :key="p.id" :value="p.id">
                Payment  {{ p.total_price }} EGP
              </option>
            </select>
          </div>

          <button
            :disabled="!selectedPaymentId"
            @click="openAddInstallmentModal"
            class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 disabled:opacity-50"
          >
            + Add Installment
          </button>
        </div>

        <!-- Installments Table -->
        <div v-if="selectedPaymentId" class="bg-white rounded-xl shadow-sm p-4">
          <div class="flex justify-between items-center mb-3">
            <h3 class="font-semibold text-lg text-gray-800">Installments</h3>
            <button
              @click="fetchInstallments"
              class="px-3 py-1 text-sm border rounded-lg hover:bg-gray-50"
            >
              Refresh
            </button>
          </div>

          <table class="min-w-full text-sm text-left border">
            <thead class="bg-gray-50 text-gray-700 uppercase text-xs">
              <tr>
                <th class="p-2 border"></th>
                <th class="p-2 border">Title</th>
                <th class="p-2 border">Amount</th>
                <th class="p-2 border">Percentage</th>
                <th class="p-2 border">Status</th>
                <th class="p-2 border">Due Date</th>
                <th class="p-2 border">Invoices</th>
                <th class="p-2 border">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="i in installments"
                :key="i.id"
                class="hover:bg-gray-50 transition"
              >
                <td class="p-2 border">{{ i.id }}</td>
                <td class="p-2 border">{{ i.title_en }}</td>
                <td class="p-2 border">{{ i.amount }}</td>
                <td class="p-2 border">{{ i.percentage }}</td>
                <td class="p-2 border">{{ i.status }}</td>
                <td class="p-2 border">{{ i.due_date || '-' }}</td>
                <td class="p-2 border text-center">
                  <button
                    @click="openInvoicesModal(i)"
                    class="text-blue-600 underline text-xs hover:text-blue-800"
                  >
                    View
                  </button>
                </td>
                <td class="p-2 border flex gap-2">
                  <button
                    @click="openEditInstallmentModal(i)"
                    class="px-2 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700"
                  >
                    Edit
                  </button>
                  <button
                    @click="deleteInstallment(i.id)"
                    class="px-2 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700"
                  >
                    Delete
                  </button>
                  <!-- logs -->
<button
  @click="openLogs(i.id)"
  class="px-3 py-1 text-xs border rounded text-gray-700 border-gray-200 hover:bg-gray-50 flex items-center gap-1"
>
  <i class="fa fa-clock text-blue-500"></i>
  Logs
</button>

                </td>
              </tr>
            </tbody>
          </table>

          <div v-if="!installments.length" class="text-center py-4 text-gray-500">
            No installments found.
          </div>
        </div>
      </div>

        </div>
            <!-- ========== Add/Edit Installment Modal ========== -->
    <InstallmentModal
      v-if="showInstallmentModal"
      :mode="modalMode"
      :data="selectedInstallment"
      :paymentId="selectedPaymentId"
      @close="closeInstallmentModal"
      @saved="fetchInstallments"
    />

    <!-- ========== Invoice Modal ========== -->
    <InvoiceModal
      v-if="showInvoiceModal"
      :installment="selectedInvoiceInstallment"
      @close="closeInvoiceModal"
    />

    <PaymentLogsModal
  v-if="showLogs"
  :logs="logs"
  @close="showLogs = false"
/>


    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { ref, onMounted } from 'vue'
import {
    UnitPaymentsApi,
    buildUnitPaymentCreateFD,
    buildUnitPaymentUpdateFD,
} from '@/Services/unitPayments'
import { UnitInstallmentsApi } from '@/Services/unitInstallments'
import { router } from '@inertiajs/vue3'
import InstallmentModal from '@/Components/InstallmentModal.vue'
import InvoiceModal from '@/Components/InvoiceModal.vue'
import PaymentLogsModal from '@/Components/PaymentLogsModal.vue'
import { UnitPaymentLogsApi } from '@/Services/unitPaymentLogs'

const props = defineProps({ unitId: Number })
const tab = ref('payments')

const payments = ref([])
const newPayment = ref({
    total_price: '',
    installments_count: '',
    payment_type: '',
    overall_status: 'pending',
})
const creating = ref(false)
const loadingPayments = ref(false)
const showLogs = ref(false)
const logs = ref([])
// const selectedPaymentId = ref(null)

async function openLogs(id) {
  selectedPaymentId.value = id
  showLogs.value = true
  try {
    const res = await UnitPaymentLogsApi.installmentLogs(id)

    logs.value = res
  } catch {
    logs.value = []
    alert('Failed to fetch logs')
  }
}

async function fetchPayments() {
    loadingPayments.value = true
    try {
        const res = await UnitPaymentsApi.list(props.unitId)
        payments.value = res.map(p => ({
            ...p,
            total_price: Number(p.total_price) || 0, // ✅ force numeric
            installments_count: Number(p.installments_count) || 0,
            isEditing: false,
            editable: { ...p },
        }))
    } finally {
        loadingPayments.value = false
    }
}


async function createPayment() {
    creating.value = true
    const fd = buildUnitPaymentCreateFD(props.unitId, [newPayment.value])
    await UnitPaymentsApi.create(fd)
    await fetchPayments()
    newPayment.value = { total_price: '', installments_count: '', payment_type: '', overall_status: 'pending' }
    creating.value = false
}

function startEdit(p) {
    p.isEditing = true
    p.editable = { ...p }
}
function cancelEdit(p) {
    p.isEditing = false
}
async function saveEdit(p) {
    const fd = buildUnitPaymentUpdateFD(p.editable)
    await UnitPaymentsApi.update(p.id, fd)
    p.isEditing = false
    await fetchPayments()
}
async function deletePayment(id) {
    if (confirm('Delete this payment?')) {
        await UnitPaymentsApi.remove(id)
        await fetchPayments()
    }
}

function formatPrice(val) {
  const num = Number(val)
  if (isNaN(num)) return '0.00'
  return num.toFixed(2)
}


function openInstallmentsTab(paymentId) {
    router.visit(route('units.installments', { unitId: props.unitId, unitPaymentId: paymentId }))
}


/* --------------------- Installments --------------------- */
const selectedPaymentId = ref('')
const installments = ref([])
const loadingInstallments = ref(false)

async function fetchInstallments() {
  if (!selectedPaymentId.value) return
  loadingInstallments.value = true
  try {
    installments.value = await UnitInstallmentsApi.list(selectedPaymentId.value)
  } finally {
    loadingInstallments.value = false
  }
}

async function deleteInstallment(id) {
  if (!confirm('Delete this installment?')) return
  await UnitInstallmentsApi.remove(id)
  await fetchInstallments()
}

/* --------------------- Installment Modal --------------------- */
const showInstallmentModal = ref(false)
const modalMode = ref('add') // add | edit
const selectedInstallment = ref(null)

function openAddInstallmentModal() {
  modalMode.value = 'add'
  selectedInstallment.value = null
  showInstallmentModal.value = true
}

function openEditInstallmentModal(i) {
  modalMode.value = 'edit'
  selectedInstallment.value = i
  showInstallmentModal.value = true
}

function closeInstallmentModal() {
  showInstallmentModal.value = false
}

/* --------------------- Invoices Modal --------------------- */
const showInvoiceModal = ref(false)
const selectedInvoiceInstallment = ref(null)

function openInvoicesModal(i) {
  selectedInvoiceInstallment.value = i
  showInvoiceModal.value = true
}

function closeInvoiceModal() {
  showInvoiceModal.value = false
}

/* Init */
onMounted(fetchPayments)

</script>
