<template>
  <AuthenticatedLayout>
    <div class="container mx-auto p-6">
      <!-- 🔗 Unit navigation -->
      <UnitNav :unit-id="Number(unitId)" :cols="2" />
      <!-- Tabs -->
      <div class="flex border-b mb-6 mt-6 items-center">
        <button :class="tab === 'payments' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-600'"
          class="px-4 py-2 font-medium" @click="tab = 'payments'">
          Payments
        </button>
        <button :class="tab === 'installments' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-600'"
          class="px-4 py-2 font-medium" @click="tab = 'installments'">
          Installments
        </button>
      </div>

      <!-- Single Payment Display -->

      <div v-if="tab === 'payments'" class="p-6 rounded shadow mt-6">

                <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-semibold text-gray-800">Payment</h3>
        </div>
        <h3 class="text-lg font-semibold mb-3">Current Payment</h3>
        <div v-if="loadingPayment" class="text-sm text-gray-500">Loading…</div>

        <div v-else-if="!payment" class="text-center text-gray-500 py-6">
          No payment created yet.
        </div>

        <div v-else class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
          <div><strong>Total Price:</strong> {{ formatPrice(payment.total_price) }}</div>
          <div><strong>Installments:</strong> {{ payment.installments_count }}</div>
          <div><strong>Type:</strong> {{ payment.payment_type }}</div>
          <div><strong>Status:</strong> {{ payment.overall_status }}</div>
        </div>
      </div>


      <!-- ===================== SINGLE PAYMENT ===================== -->
      <div v-if="tab === 'payments'" class="bg-white p-6 rounded shadow mt-6">
        <h3 class="text-lg font-semibold mb-4">Unit Payment</h3>

        <!-- 🟢 CASE 1: No payment yet -->
        <div v-if="!payment">
          <p class="text-gray-600 mb-4">No payment created yet for this unit.</p>

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
                <option value="installments">Installments</option>
                <option value="cash">Cash</option>
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
              {{ creating ? 'Saving…' : 'Create Payment' }}
            </button>
          </div>
        </div>

        <!-- 🟣 CASE 2: Payment exists -->
        <div v-else class="space-y-6">
          <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
            <div>
              <label class="block text-gray-600 text-xs mb-1">Total Price</label>
              <input v-model.number="payment.editable.total_price" type="number" class="w-full border p-2 rounded" />
            </div>
            <div>
              <label class="block text-gray-600 text-xs mb-1">Installments Count</label>
              <input v-model.number="payment.editable.installments_count" type="number"
                class="w-full border p-2 rounded" />
            </div>
            <div>
              <label class="block text-gray-600 text-xs mb-1">Type</label>
              <select v-model="payment.editable.payment_type" class="w-full border p-2 rounded">
                <option value="installments">Installments</option>
                <option value="cash">Cash</option>
              </select>
            </div>
            <div>
              <label class="block text-gray-600 text-xs mb-1">Status</label>
              <select v-model="payment.editable.overall_status" class="w-full border p-2 rounded">
                <option value="pending">Pending</option>
                <option value="in_progress">In Progress</option>
                <option value="completed">Completed</option>
                <option value="overdue">Overdue</option>
              </select>
            </div>
          </div>

          <div class="flex justify-end gap-2">
            <button @click="savePayment" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
              Save Changes
            </button>
            <button @click="deletePayment(payment.id)" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">
              Delete
            </button>
          </div>
        </div>
      </div>


      <!-- ===================== Installments Tab ===================== -->
      <div v-if="tab === 'installments'" class="space-y-4">
        <!-- Single Payment Installments -->
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-semibold text-gray-800">Installments</h3>
          <button :disabled="!payment" @click="openAddInstallmentModal"
            class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 disabled:opacity-50">
            + Add Installment
          </button>
        </div>


        <!-- Installments Table -->
        <div v-if="selectedPaymentId" class="bg-white rounded-xl shadow-sm p-4">
          <div class="flex justify-between items-center mb-3">
            <h3 class="font-semibold text-lg text-gray-800">Installments</h3>
            <button @click="fetchInstallments" class="px-3 py-1 text-sm border rounded-lg hover:bg-gray-50">
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
              <tr v-for="i in installments" :key="i.id" class="hover:bg-gray-50 transition">
                <td class="p-2 border">{{ i.id }}</td>
                <td class="p-2 border">{{ i.title.en }}</td>
                <td class="p-2 border">{{ i.amount }}</td>
                <td class="p-2 border">{{ i.percentage }}</td>
                <td class="p-2 border">{{ i.status }}</td>
                <td class="p-2 border">{{ i.due_date || '-' }}</td>
                <td class="p-2 border text-center">
                  <button @click="openInvoicesModal(i)" class="text-blue-600 underline text-xs hover:text-blue-800">
                    View
                  </button>
                </td>
                <td class="p-2 border flex gap-2">
                  <button @click="openEditInstallmentModal(i)"
                    class="px-2 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700">
                    Edit
                  </button>
                  <button @click="deleteInstallment(i.id)"
                    class="px-2 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700">
                    Delete
                  </button>
                  <!-- logs -->
                  <button @click="openLogs(i.id)"
                    class="px-3 py-1 text-xs border rounded text-gray-700 border-gray-200 hover:bg-gray-50 flex items-center gap-1">
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
    <InstallmentModal v-if="showInstallmentModal" :mode="modalMode" :data="selectedInstallment"
      :paymentId="selectedPaymentId" @close="closeInstallmentModal" @saved="fetchInstallments" />

    <!-- ========== Invoice Modal ========== -->
    <InvoiceModal v-if="showInvoiceModal" :installment="selectedInvoiceInstallment" @close="closeInvoiceModal" />

    <PaymentLogsModal v-if="showLogs" :logs="logs" @close="showLogs = false" />


  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import UnitNav from '@/Components/UnitNav.vue'
import { ref, onMounted, computed, watch } from 'vue'
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
import { useServerError } from '@/composables/useServerError'

const { show } = useServerError()

const props = defineProps({ unitId: Number })
const tab = ref('payments')

const payment = ref(null)
const newPayment = ref({
  total_price: '',
  installments_count: '',
  payment_type: 'installments',
  overall_status: 'pending',
})
const creating = ref(false)
const loadingPayment = ref(false)

const showLogs = ref(false)
const logs = ref([])
// const selectedPaymentId = ref(null)



function setEditablePayment(p) {
  payment.value = {
    ...p,
    editable: { ...p },
  }
}

async function openLogs(id) {
  // selectedPaymentId.value = id
  showLogs.value = true
  try {
    const res = await UnitPaymentLogsApi.installmentLogs(id)

    logs.value = res
  } catch {

    logs.value = []
    show(e)
    // alert('Failed to fetch logs')
    console.log('No logs found')
  }
}


async function fetchPayment() {
  loadingPayment.value = true
  try {
    const res = await UnitPaymentsApi.list(props.unitId)

    if (res?.data?.length > 0 || res?.length > 0) {
      const p = res.data ? res.data[0] : res[0]
      setEditablePayment(p)
      selectedPaymentId.value = p.id 
    } else {
      payment.value = null
      selectedPaymentId.value = null
    }
    if (tab.value === 'installments') {
  await fetchInstallments()
}

  } catch (e) {
    show(e)
  }
  
  finally {
    loadingPayment.value = false
  }
}





async function createPayment() {
  try{
  creating.value = true
  const fd = buildUnitPaymentCreateFD(props.unitId, [newPayment.value])
  await UnitPaymentsApi.create(fd)
  await fetchPayment()
  creating.value = false
  } catch (e) {
    show(e)
  }
}

async function savePayment() {

  try {
  if (!payment.value) return
  const fd = buildUnitPaymentUpdateFD(payment.value.editable)
  await UnitPaymentsApi.update(payment.value.id, fd)
  await fetchPayment()
  }
  catch (e) {
    show(e)
  }
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
  await fetchPayment()
}
async function deletePayment(id) {
  if (confirm('Delete this payment?')) {
    await UnitPaymentsApi.remove(id)
    await fetchPayment()
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
// const selectedPaymentId = computed(() => payment.value?.id || null)
const selectedPaymentId = ref(null)
const installments = ref([])
const loadingInstallments = ref(false)

async function fetchInstallments() {
  if (!selectedPaymentId.value) return
  loadingInstallments.value = true
  try {
    installments.value = await UnitInstallmentsApi.list(selectedPaymentId.value)
  } 
  
  finally {
    loadingInstallments.value = false
  }
}


async function deleteInstallment(id) {
  if (!confirm('Delete this installment?')) return
  await UnitInstallmentsApi.remove(id)
  await fetchPayment()
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
onMounted(fetchPayment)

watch(tab, async (newVal) => {
  if (newVal === 'installments' && selectedPaymentId.value) {
    await fetchInstallments()
  }
})


</script>
