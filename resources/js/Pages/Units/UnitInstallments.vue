<template>
    <AuthenticatedLayout>
        <div class="p-6 space-y-8">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold text-gray-800">Unit Installments</h2>
                <a :href="route('units.payments', { unitId })"
                    class="px-3 py-1 border rounded text-gray-700 hover:bg-gray-100">
                    ← Back to Payments
                </a>
            </div>

            <!-- Add Installment -->
            <div class="bg-white p-6 rounded-2xl shadow space-y-4">
                <h3 class="text-lg font-semibold text-gray-800"> Add Installment</h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Title EN -->
                    <div>
                        <label class="label">Title (EN) *</label>
                        <input v-model="newInstallment.title.en" class="form-input" />
                    </div>

                    <!-- Title AR -->
                    <div>
                        <label class="label">Title (AR) *</label>
                        <input v-model="newInstallment.title.ar" class="form-input" />
                    </div>

                    <!-- Amount -->
                    <div>
                        <label class="label">Amount *</label>
                        <input type="number" v-model.number="newInstallment.amount" class="form-input" />
                    </div>

                    <!-- Percentage -->
                    <div>
                        <label class="label">Percentage</label>
                        <input type="number" v-model.number="newInstallment.percentage" class="form-input" />
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="label">Status</label>
                        <select v-model="newInstallment.status" class="form-input">
                            <option value="pending">Pending</option>
                            <option value="paid">Paid</option>
                            <option value="overdue">Overdue</option>
                        </select>
                    </div>

                    <!-- Milestone Date -->
                    <div>
                        <label class="label">Milestone Date</label>
                        <input type="date" v-model="newInstallment.milestone_date" class="form-input" />
                    </div>

                    <!-- Submission Date -->
                    <div>
                        <label class="label">Submission Date</label>
                        <input type="date" v-model="newInstallment.submission_date" class="form-input" />
                    </div>

                    <!-- Consultant Approval Date -->
                    <div>
                        <label class="label">Consultant Approval Date</label>
                        <input type="date" v-model="newInstallment.consultant_approval_date" class="form-input" />
                    </div>

                    <!-- Due Date -->
                    <div>
                        <label class="label">Due Date</label>
                        <input type="date" v-model="newInstallment.due_date" class="form-input" />
                    </div>

                    <!-- Payment Date -->
                    <div>
                        <label class="label">Payment Date</label>
                        <input type="date" v-model="newInstallment.payment_date" class="form-input" />
                    </div>

                    <!-- Description EN -->
                    <div class="md:col-span-3">
                        <label class="label">Description (EN)</label>
                        <textarea v-model="newInstallment.description.en" rows="2" class="form-input"></textarea>
                    </div>

                    <!-- Description AR -->
                    <div class="md:col-span-3">
                        <label class="label">Description (AR)</label>
                        <textarea v-model="newInstallment.description.ar" rows="2" class="form-input"></textarea>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button class="bg-black text-white px-3 py-2 rounded hover:bg-gray-700 disabled:opacity-50"
                        :disabled="creating" @click="createInstallment">
                        {{ creating ? 'Saving…' : 'Add Installment' }}
                    </button>
                </div>
            </div>


            <!-- Table View -->
            <div class="bg-white p-6 rounded-2xl shadow">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Existing Installments</h3>
                    <button @click="fetchInstallments" class="px-3 py-1.5 text-sm border rounded-lg hover:bg-gray-50">
                        Refresh
                    </button>
                </div>

                <div v-if="loading" class="text-sm text-gray-500">Loading…</div>
                <div v-else-if="!rows.length" class="text-center text-gray-500 py-8">
                    No installments found.
                </div>

                <div v-else class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200 text-sm">
                        <thead class="bg-gray-100 text-gray-700">
                            <tr>
                                <th class="py-2 px-3 text-left"></th>
                                <th class="py-2 px-3 text-left">Title (EN)</th>
                                <th class="py-2 px-3 text-left">Amount</th>
                                <th class="py-2 px-3 text-left">%</th>
                                <th class="py-2 px-3 text-left">Status</th>
                                <th class="py-2 px-3 text-left">Due Date</th>
                                <th class="py-2 px-3 text-center w-[180px]">Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="i in rows" :key="i.id" class="border-t hover:bg-gray-50 transition">
                                <td class="py-2 px-3 text-gray-600"></td>
                                <td class="py-2 px-3">{{ i.title_en || '-' }}</td>
                                <td class="py-2 px-3">{{ i.amount ?? '-' }}</td>
                                <td class="py-2 px-3">{{ i.percentage ?? '-' }}%</td>
                                <td class="py-2 px-3">
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium"
                                        :class="statusColor(i.status)">
                                        {{ i.status }}
                                    </span>
                                </td>
                                <td class="py-2 px-3">{{ i.due_date || '-' }}</td>
                                <td class="py-2 px-3 text-center">
                                    <button
                                        class="px-3 py-1 text-xs border rounded text-blue-600 border-blue-200 hover:bg-blue-50 mr-2"
                                        @click="openEditModal(i)">
                                        Edit
                                    </button>
                                    <button
                                        class="px-3 py-1 text-xs border rounded text-red-600 border-red-200 hover:bg-red-50"
                                        @click="removeInstallment(i.id)">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Edit Modal -->
            <Teleport to="body">
                <div v-if="showModal" class="fixed inset-0 z-[2000] flex items-center justify-center">
                    <!-- Backdrop -->
                    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="closeModal"></div>

                    <!-- Panel -->
                    <div
                        class="relative z-10 bg-white rounded-2xl shadow-2xl w-full max-w-3xl p-6 animate-fadeIn overflow-y-auto max-h-[90vh]">
                        <!-- Header -->
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xl font-semibold text-gray-800">
                                Edit Installment
                            </h3>
                            <button class="text-gray-400 hover:text-gray-600 text-xl" @click="closeModal">
                                ✕
                            </button>
                        </div>

                        <!-- Form -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="label">Title (EN)</label>
                                <input v-model="editForm.title.en" class="form-input" />
                            </div>
                            <div>
                                <label class="label">Title (AR)</label>
                                <input v-model="editForm.title.ar" class="form-input" />
                            </div>
                            <div>
                                <label class="label">Amount</label>
                                <input type="number" v-model.number="editForm.amount" class="form-input" />
                            </div>
                            <div>
                                <label class="label">Percentage</label>
                                <input type="number" v-model.number="editForm.percentage" class="form-input" />
                            </div>
                            <div>
                                <label class="label">Status</label>
                                <select v-model="editForm.status" class="form-input">
                                    <option value="pending">Pending</option>
                                    <option value="paid">Paid</option>
                                    <option value="overdue">Overdue</option>
                                </select>
                            </div>
                            <div>
                                <label class="label">Milestone Date</label>
                                <input type="date" v-model="editForm.milestone_date" class="form-input" />
                            </div>
                            <div>
                                <label class="label">Submission Date</label>
                                <input type="date" v-model="editForm.submission_date" class="form-input" />
                            </div>
                            <div>
                                <label class="label">Consultant Approval Date</label>
                                <input type="date" v-model="editForm.consultant_approval_date" class="form-input" />
                            </div>
                            <div>
                                <label class="label">Due Date</label>
                                <input type="date" v-model="editForm.due_date" class="form-input" />
                            </div>
                            <div>
                                <label class="label">Payment Date</label>
                                <input type="date" v-model="editForm.payment_date" class="form-input" />
                            </div>
                            <div class="md:col-span-2">
                                <label class="label">Description (EN)</label>
                                <textarea v-model="editForm.description.en" rows="2" class="form-input"></textarea>
                            </div>
                            <div class="md:col-span-2">
                                <label class="label">Description (AR)</label>
                                <textarea v-model="editForm.description.ar" rows="2" class="form-input"></textarea>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="mt-6 flex justify-end gap-3">
                            <button class="px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-100"
                                @click="closeModal">
                                Cancel
                            </button>
                            <button
                                class="px-4 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 disabled:opacity-60"
                                :disabled="saving" @click="saveEdit">
                                {{ saving ? 'Saving…' : 'Save Changes' }}
                            </button>
                        </div>
                    </div>
                </div>
            </Teleport>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { ref, onMounted } from 'vue'
import {
    UnitInstallmentsApi,
    buildInstallmentCreateFD,
    buildInstallmentUpdateFD
} from '@/Services/unitInstallments.js'

const props = defineProps({
    unitId: { type: [Number, String], required: true },
    unitPaymentId: { type: [Number, String], required: true },
})

const rows = ref([])
const loading = ref(false)
const creating = ref(false)
const saving = ref(false)

const showModal = ref(false)
const selected = ref(null)
const editForm = ref({
    title: { en: '', ar: '' },
    description: { en: '', ar: '' },
    percentage: null,
    amount: null,
    status: 'pending',
    milestone_date: '',
    submission_date: '',
    consultant_approval_date: '',
    due_date: '',
    payment_date: '',
})

const newInstallment = ref({
    title: { en: '', ar: '' },
    description: { en: '', ar: '' },
    percentage: null,
    amount: null,
    status: 'pending',
    due_date: '',
})

async function fetchInstallments() {
    loading.value = true
    try {
        const data = await UnitInstallmentsApi.list(props.unitPaymentId)
        rows.value = data
    } finally {
        loading.value = false
    }
}

async function createInstallment() {
    creating.value = true
    try {
        const fd = buildInstallmentCreateFD(props.unitPaymentId, newInstallment.value)
        await UnitInstallmentsApi.create(fd)
        await fetchInstallments()
        newInstallment.value = {
            title: { en: '', ar: '' },
            description: { en: '', ar: '' },
            percentage: null,
            amount: null,
            status: 'pending',
            due_date: '',
        }
    } finally {
        creating.value = false
    }
}

function openEditModal(i) {
    selected.value = i
    editForm.value = {
        title: { en: i.title_en, ar: i.title_ar },
        description: { en: i.description_en, ar: i.description_ar },
        percentage: i.percentage,
        amount: i.amount,
        status: i.status,
        milestone_date: i.milestone_date,
        submission_date: i.submission_date,
        consultant_approval_date: i.consultant_approval_date,
        due_date: i.due_date,
        payment_date: i.payment_date,
    }
    showModal.value = true
}

function closeModal() {
    showModal.value = false
    selected.value = null
}

async function saveEdit() {
    if (!selected.value) return
    saving.value = true
    try {
        const fd = buildInstallmentUpdateFD(editForm.value)
        await UnitInstallmentsApi.update(selected.value.id, fd)
        await fetchInstallments()
        closeModal()
    } finally {
        saving.value = false
    }
}

async function removeInstallment(id) {
    if (!confirm('Delete this installment?')) return
    await UnitInstallmentsApi.remove(id)
    await fetchInstallments()
}

function statusColor(status) {
    switch (status) {
        case 'paid':
            return 'bg-green-100 text-green-700'
        case 'overdue':
            return 'bg-red-100 text-red-700'
        default:
            return 'bg-gray-100 text-gray-700'
    }
}

onMounted(fetchInstallments)
</script>

<style scoped>
.form-input {
    @apply w-full border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500;
}

.label {
    @apply block text-xs text-gray-500 mb-1;
}

.animate-fadeIn {
    animation: fadeIn 0.25s ease-in-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: scale(0.98);
    }

    to {
        opacity: 1;
        transform: scale(1);
    }
}
</style>
