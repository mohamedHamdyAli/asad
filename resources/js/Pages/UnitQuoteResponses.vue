<template>
  <AuthenticatedLayout>
    <div class="p-6 space-y-8">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800">Unit Quote Responses</h2>
        <button
          @click="fetchAll"
          class="px-3 py-1.5 border rounded-lg text-sm hover:bg-gray-50"
        >
          Refresh
        </button>
      </div>

      <!-- Search -->
      <div class="flex justify-between items-center bg-white p-4 rounded-lg shadow-sm">
        <input
          v-model="search"
          type="text"
          placeholder="Search quotes by title or user…"
          class="form-input w-1/2"
        />
        <span class="text-sm text-gray-500">
          {{ filteredQuotes.length }} quote(s) found
        </span>
      </div>

      <!-- Quotes Table -->
      <div class="bg-white p-6 rounded-2xl shadow">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
          Quotes Pending / Responded
        </h3>

        <div v-if="loading" class="text-sm text-gray-500">Loading…</div>
        <div v-else-if="!filteredQuotes.length" class="text-center text-gray-500 py-6">
          No quotes found.
        </div>

        <div v-else class="overflow-x-auto">
          <table class="min-w-full border border-gray-200 text-sm">
            <thead class="bg-gray-100 text-gray-700">
              <tr>
                <th class="py-2 px-3 text-left"></th>
                <th class="py-2 px-3 text-left">Title</th>
                <th class="py-2 px-3 text-left">User</th>
                <th class="py-2 px-3 text-left">Created</th>
                <th class="py-2 px-3 text-left">Status</th>
                <th class="py-2 px-3 text-center">Action</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="q in filteredQuotes"
                :key="q.id"
                class="border-t hover:bg-gray-50 transition"
              >
                <td class="py-2 px-3 font-medium">{{ q.id }}</td>
                <td class="py-2 px-3">{{ q.title }}</td>
                <td class="py-2 px-3">{{ q.user_name || '—' }}</td>
                <td class="py-2 px-3">{{ formatDate(q.created_at) }}</td>
                <td class="py-2 px-3">
                  <span
                    :class="[
                      'px-2 py-0.5 rounded-full text-xs',
                      q.has_response
                        ? 'bg-green-100 text-green-700 border border-green-200'
                        : 'bg-yellow-100 text-yellow-700 border border-yellow-200'
                    ]"
                  >
                    {{ q.has_response ? 'Responded' : 'Pending' }}
                  </span>
                </td>
                <td class="py-2 px-3 text-center">
                  <button
                    class="px-3 py-1 text-xs border rounded-lg"
                    :class="q.has_response
                      ? 'text-gray-400 border-gray-200 cursor-not-allowed'
                      : 'text-blue-700 border-blue-200 hover:bg-blue-50'"
                    :disabled="q.has_response"
                    @click="openNewResponse(q)"
                  >
                    Respond
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Responses Table -->
      <div class="bg-white p-6 rounded-2xl shadow">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-gray-800">Existing Responses</h3>
        </div>

        <div v-if="responsesLoading" class="text-sm text-gray-500">Loading…</div>
        <div v-else-if="!responses.length" class="text-center text-gray-500 py-6">
          No responses yet.
        </div>
        <div v-else class="overflow-x-auto">
          <table class="min-w-full border border-gray-200 text-sm">
            <thead class="bg-gray-100 text-gray-700">
              <tr>
                <th class="py-2 px-3 text-left"></th>
                <th class="py-2 px-3 text-left">Quote</th>
                <th class="py-2 px-3 text-left">Vendor</th>
                <th class="py-2 px-3 text-left">User</th>
                <th class="py-2 px-3 text-left">Price</th>
                <th class="py-2 px-3 text-center">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="r in responses"
                :key="r.id"
                class="border-t hover:bg-gray-50 transition"
              >
                <td class="py-2 px-3 font-medium">{{ r.id }}</td>
                <td class="py-2 px-3">{{ r.unit_quote_id }}</td>
                <td class="py-2 px-3">{{ r.vendor_name || r.vendor_id }}</td>
                <td class="py-2 px-3">{{ r.user_name || r.user_id }}</td>
                <td class="py-2 px-3">{{ r.price ?? '-' }}</td>
                <td class="py-2 px-3 text-center">
                  <button
                    class="px-3 py-1 text-xs border rounded-lg text-blue-700 border-blue-200 hover:bg-blue-50 mr-2"
                    @click="openEdit(r)"
                  >
                    Edit
                  </button>
                  <button
                    class="px-3 py-1 text-xs border rounded-lg text-red-700 border-red-200 hover:bg-red-50"
                    @click="remove(r.id)"
                  >
                    Delete
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Response Modal -->
      <Teleport to="body">
        <div v-if="showModal" class="fixed inset-0 z-[1000] flex items-center justify-center">
          <div class="absolute inset-0 bg-black/40" @click="closeModal"></div>
          <div
            class="relative z-10 bg-white rounded-2xl shadow-2xl w-full max-w-2xl p-6 overflow-y-auto max-h-[90vh]"
          >
            <div class="flex justify-between mb-4">
              <h3 class="text-lg font-semibold text-gray-800">
                {{ editing ? 'Edit Response' : 'Respond to Quote ' + selectedQuote?.id }}
              </h3>
              <button @click="closeModal" class="text-gray-500 hover:text-gray-700 text-xl">✕</button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="label">Vendor *</label>
                <select v-model="form.vendor_id" class="form-input">
                  <option value="" disabled>Select Vendor</option>
                  <option v-for="v in vendors" :key="v.id" :value="v.id">
                    {{ v.id }} — {{ v.name }}
                  </option>
                </select>
              </div>

              <div>
                <label class="label">User *</label>
                <select v-model="form.user_id" class="form-input">
                  <option value="" disabled>Select User</option>
                  <option v-for="u in users" :key="u.id" :value="u.id">
                    {{ u.id }} — {{ u.name }}
                  </option>
                </select>
              </div>

              <div>
                <label class="label">Title (EN) *</label>
                <input v-model="form.title.en" class="form-input" />
              </div>

              <div>
                <label class="label">Title (AR) *</label>
                <input v-model="form.title.ar" class="form-input" />
              </div>

              <div>
                <label class="label">Price</label>
                <input v-model.number="form.price" type="number" class="form-input" />
              </div>

              <div>
                <label class="label">Timeline (EN)</label>
                <input v-model="form.time_line.en" class="form-input" />
              </div>

              <div>
                <label class="label">Timeline (AR)</label>
                <input v-model="form.time_line.ar" class="form-input" />
              </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
              <button class="px-4 py-2 border rounded-lg" @click="closeModal">Cancel</button>
              <button
                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-60"
                :disabled="saving"
                @click="submitResponse"
              >
                {{ saving ? 'Saving…' : 'Save' }}
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
import { ref, computed, onMounted } from 'vue'
import {
  UnitQuoteResponsesApi,
  buildQuoteResponseCreateFD,
  buildQuoteResponseUpdateFD
} from '@/Services/unitQuoteResponses.js'
import { UnitQuotesApi } from '@/Services/unitQuotes.js'

/* Dummy User/Vendor APIs */
const UsersApi = {
  list: async () => [
    { id: 1, name: 'Client 1', email: 'client@example.com' },
    { id: 2, name: 'Client 2', email: 'user@example.com' }
  ]
}
const VendorsApi = {
  list: async () => [
    { id: 10, name: 'Vendor A' },
    { id: 11, name: 'Vendor B' }
  ]
}

/* Refs */
const quotes = ref([])
const responses = ref([])
const users = ref([])
const vendors = ref([])
const search = ref('')
const loading = ref(false)
const responsesLoading = ref(false)
const showModal = ref(false)
const selectedQuote = ref(null)
const editing = ref(false)
const saving = ref(false)

const form = ref({
  unit_quote_id: '',
  vendor_id: '',
  user_id: '',
  title: { en: '', ar: '' },
  price: '',
  time_line: { en: '', ar: '' }
})

/* Computed */
const filteredQuotes = computed(() => {
  const term = search.value.toLowerCase()
  return quotes.value.filter(
    (q) =>
      q.title.toLowerCase().includes(term) ||
      (q.user_name && q.user_name.toLowerCase().includes(term))
  )
})

/* Fetch */
async function fetchAll() {
  loading.value = true
  responsesLoading.value = true
  try {
    const [allQuotes, allResponses, allUsers, allVendors] = await Promise.all([
      UnitQuotesApi.list(),
      UnitQuoteResponsesApi.list(),
      UsersApi.list(),
      VendorsApi.list()
    ])
    quotes.value = allQuotes.map((q) => ({
      ...q,
      has_response: allResponses.some((r) => r.unit_quote_id === q.id)
    }))
    responses.value = allResponses
    users.value = allUsers
    vendors.value = allVendors
  } finally {
    loading.value = false
    responsesLoading.value = false
  }
}

/* Helpers */
function formatDate(d) {
  return new Date(d).toLocaleDateString()
}

/* Modal logic */
function openNewResponse(q) {
  selectedQuote.value = q
  form.value = {
    unit_quote_id: q.id,
    vendor_id: '',
    user_id: '',
    title: { en: '', ar: '' },
    price: '',
    time_line: { en: '', ar: '' }
  }
  editing.value = false
  showModal.value = true
}
function openEdit(r) {
  selectedQuote.value = { id: r.unit_quote_id }
  form.value = {
    unit_quote_id: r.unit_quote_id,
    vendor_id: r.vendor_id,
    user_id: r.user_id,
    title: { en: r.title_en, ar: r.title_ar },
    price: r.price,
    time_line: { en: r.time_line_en, ar: r.time_line_ar }
  }
  editing.value = true
  showModal.value = true
}
function closeModal() {
  showModal.value = false
  selectedQuote.value = null
}

/* Submit */
async function submitResponse() {
  saving.value = true
  try {
    const fd = editing.value
      ? buildQuoteResponseUpdateFD(form.value)
      : buildQuoteResponseCreateFD(form.value)
    if (editing.value) {
      const existing = responses.value.find(
        (r) => r.unit_quote_id === form.value.unit_quote_id
      )
      await UnitQuoteResponsesApi.update(existing.id, fd)
    } else {
      await UnitQuoteResponsesApi.create(fd)
    }
    await fetchAll()
    closeModal()
  } finally {
    saving.value = false
  }
}
async function remove(id) {
  if (!confirm('Delete this response?')) return
  await UnitQuoteResponsesApi.remove(id)
  await fetchAll()
}

onMounted(fetchAll)
</script>

<style scoped>
.form-input {
  @apply w-full border border-gray-300 rounded-md px-3 py-2 text-sm
         focus:outline-none focus:ring-2 focus:ring-blue-500;
}
.label {
  @apply block text-xs text-gray-500 mb-1;
}
</style>
