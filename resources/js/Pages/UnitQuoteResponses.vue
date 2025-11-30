<template>
  <AuthenticatedLayout>
    <div class="p-6 space-y-8">

      <!-- Header -->
      <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800">Unit Quote Responses</h2>
        <button @click="fetchAll" class="px-3 py-1.5 border rounded-lg text-sm hover:bg-gray-50">
          Refresh
        </button>
      </div>

      <!-- Search -->
      <div class="flex justify-between items-center bg-white p-4 rounded-lg shadow-sm">
        <input v-model="search" type="text" placeholder="Search quotes by title or user…" class="form-input w-1/2" />
        <span class="text-sm text-gray-500">
          {{ filteredQuotes.length }} quote(s) found
        </span>
      </div>

      <!-- Table -->
      <div class="bg-white p-6 rounded-2xl shadow">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Quotes & Responses</h3>

        <div v-if="loading" class="text-sm text-gray-500">Loading…</div>
        <div v-else-if="!paginatedQuotes.length" class="text-center text-gray-500 py-6">
          No quotes found.
        </div>

        <div v-else class="overflow-x-auto">
          <table class="min-w-full border border-gray-200 text-sm">
            <thead class="bg-gray-100 text-gray-700">
              <tr>
                <th class="py-2 px-3 text-left"></th>
                <th class="py-2 px-3 text-left">Title</th>
                <th class="py-2 px-3 text-left">User</th>
                <th class="py-2 px-3 text-left">Vendor</th>
                <th class="py-2 px-3 text-left">Price</th>
                <th class="py-2 px-3 text-left">Timeline</th>
                <th class="py-2 px-3 text-left">Status</th>
                <th class="py-2 px-3 text-center">Actions</th>
              </tr>
            </thead>

            <tbody>
              <tr
                v-for="q in paginatedQuotes"
                :key="q.id"
                class="border-t hover:bg-gray-50 transition"
              >
                <!-- ID -->
                <td class="py-2 px-3 font-medium">{{ q.id }}</td>

                <!-- Title -->
                <td class="py-2 px-3">
                  <div class="font-semibold text-gray-900">{{ q.title }}</div>
                  <div class="text-xs text-gray-500">{{ formatDate(q.created_at) }}</div>
                </td>

                <!-- User -->
                <td class="py-2 px-3 text-gray-700">
                  {{ users.find(u => u.id === q.user_id)?.name || `User ${q.user_id}` }}
                </td>

                <!-- Vendor (selectable) -->
                <td class="py-2 px-3">
                  <select v-model="q.response.vendor_id" class="form-input" :disabled="!q.editing && q.has_response">
                    <option value="" disabled>Select Vendor</option>
                    <option v-for="v in vendors" :key="v.id" :value="v.id">
                      {{ v.name }}
                    </option>
                  </select>
                </td>

                <!-- Price (input) -->
                <td class="py-2 px-3">
                  <input v-model.number="q.response.price" type="number" class="form-input"
                         :disabled="!q.editing && q.has_response" />
                </td>

                <!-- Timeline (input) -->
                <td class="py-2 px-3">
                  <input v-model="q.response.time_line" type="text" class="form-input"
                         :disabled="!q.editing && q.has_response" />
                </td>

                <!-- Status -->
                <td class="py-2 px-3">
                  <span
                    :class="[
                      'px-2 py-0.5 rounded-full text-xs border',
                      q.has_response
                        ? 'bg-green-100 text-green-700 border-green-200'
                        : 'bg-yellow-100 text-yellow-700 border-yellow-200'
                    ]"
                  >
                    {{ q.has_response ? 'Responded' : 'Pending' }}
                  </span>
                </td>

                <!-- Actions -->
                <td class="py-2 px-3 text-center space-x-2">

                  <!-- Details button -->
                  <button
                    class="px-2 py-1 text-xs border rounded-lg text-gray-700 hover:bg-gray-100"
                    @click="openDetails(q)"
                  >
                    Details
                  </button>

                  <!-- Save -->
                  <button
                    v-if="!q.has_response"
                    class="px-3 py-1 text-xs border rounded-lg text-blue-700 border-blue-200 hover:bg-blue-50"
                    @click="saveResponse(q)"
                  >
                    Save
                  </button>

                  <!-- Edit / Update / Delete -->
                  <template v-else>
                    <button
                      v-if="!q.editing"
                      class="px-3 py-1 text-xs border rounded-lg text-blue-700 border-blue-200 hover:bg-blue-50"
                      @click="enableEdit(q)"
                    >
                      Edit
                    </button>

                    <button
                      v-if="q.editing"
                      class="px-3 py-1 text-xs border rounded-lg text-green-700 border-green-200 hover:bg-green-50"
                      @click="updateResponse(q)"
                    >
                      Update
                    </button>

                    <button
                      class="px-3 py-1 text-xs border rounded-lg text-red-700 border-red-200 hover:bg-red-50"
                      @click="deleteResponse(q)"
                    >
                      Delete
                    </button>
                  </template>
                </td>
              </tr>
            </tbody>
          </table>

          <!-- Pagination -->
          <div v-if="totalPages > 1" class="flex justify-between items-center mt-6 text-sm">
            <span class="text-gray-600">
              Showing {{ startItem }}–{{ endItem }} of {{ filteredQuotes.length }}
            </span>
            <div class="space-x-2">
              <button @click="prevPage" :disabled="currentPage === 1"
                      class="px-3 py-1 border rounded-lg hover:bg-gray-50 disabled:opacity-50">Prev</button>

              <button v-for="n in totalPages" :key="n" @click="goToPage(n)"
                      class="px-3 py-1 border rounded-lg"
                      :class="n === currentPage ? 'bg-blue-600 text-white' : 'hover:bg-gray-50'">
                {{ n }}
              </button>

              <button @click="nextPage" :disabled="currentPage === totalPages"
                      class="px-3 py-1 border rounded-lg hover:bg-gray-50 disabled:opacity-50">Next</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- DETAILS POPUP -->
    <div
      v-if="detailsModal"
      class="fixed inset-0 bg-black/30 flex items-center justify-center p-4 z-[200]"
    >
      <div class="bg-white w-full max-w-lg rounded-xl shadow-lg p-6 space-y-5">

        <h3 class="text-xl font-bold text-gray-800">Quote Details</h3>

        <div class="space-y-3 text-sm text-gray-700">

          <div><b>Title:</b> {{ selectedQuote.title }}</div>
          <div><b>Other Title:</b> {{ selectedQuote.other_title || '—' }}</div>

          <div>
            <b>Building Type:</b>
            {{
              getEn(buildings.find(b => b.id === selectedQuote.type_of_building_id)?.name)
                || '—'
            }}
          </div>

          <div>
            <b>Price Type:</b>
            {{
              getEn(prices.find(p => p.id === selectedQuote.type_of_price_id)?.name)
                || '—'
            }}
          </div>

          <div><b>User:</b>
            {{
              users.find(u => u.id === selectedQuote.user_id)?.name ||
              `User ${selectedQuote.user_id}`
            }}
          </div>

          <div><b>Vendor:</b>
            {{ vendorName(selectedQuote.response.vendor_id) }}
          </div>

          <div><b>Price:</b>
            {{ selectedQuote.response.price || '—' }}
          </div>

          <div><b>Timeline:</b>
            {{ selectedQuote.response.time_line || '—' }}
          </div>

          <div v-if="selectedQuote.pay_image">
            <b>Payment Image:</b>
            <a
              :href="`/storage/${selectedQuote.pay_image}`"
              target="_blank"
            >
              <img
                :src="`/storage/${selectedQuote.pay_image}`"
                class="h-24 w-24 object-cover rounded shadow mt-1"
              />
            </a>
          </div>

          <div><b>Created At:</b> {{ formatDate(selectedQuote.created_at) }}</div>
        </div>

        <div class="flex justify-end pt-4">
          <button
            class="px-4 py-2 border rounded-lg hover:bg-gray-50"
            @click="detailsModal = false"
          >
            Close
          </button>
        </div>

      </div>
    </div>

  </AuthenticatedLayout>
</template>
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { ref, computed, watch, onMounted } from 'vue'

import {
  UnitQuoteResponsesApi,
  buildQuoteResponseCreateFD,
  buildQuoteResponseUpdateFD,
} from '@/Services/unitQuoteResponses.js'
import { UnitQuotesApi } from '@/Services/unitQuotes.js'
import { VendorsApi } from '@/Services/vendors.js'
import { UsersApi } from '@/Services/users.js'
import { TypeOfBuildingsApi } from '@/Services/typeOfBuildings.js'
import { TypeOfPricesApi } from '@/Services/typeOfPrices.js'

const quotes = ref([])
const users = ref([])
const vendors = ref([])
const buildings = ref([])
const prices = ref([])
const search = ref('')
const loading = ref(false)

/* MODAL */
const detailsModal = ref(false)
const selectedQuote = ref(null)

function openDetails(q) {
  selectedQuote.value = q
  detailsModal.value = true
}

/* Pagination */
const currentPage = ref(1)
const perPage = ref(7)

const filteredQuotes = computed(() => {
  const term = search.value.toLowerCase()
  return quotes.value
    .filter(
      (q) =>
        q.title.toLowerCase().includes(term) ||
        (q.user_name && q.user_name.toLowerCase().includes(term))
    )
    .sort((a, b) => b.id - a.id)
})

const totalPages = computed(() => Math.ceil(filteredQuotes.value.length / perPage.value))
const startItem = computed(() => (currentPage.value - 1) * perPage.value + 1)
const endItem = computed(() =>
  Math.min(currentPage.value * perPage.value, filteredQuotes.value.length)
)

const paginatedQuotes = computed(() => {
  const start = (currentPage.value - 1) * perPage.value
  return filteredQuotes.value.slice(start, start + perPage.value)
})

watch(search, () => (currentPage.value = 1))

/* Fetch */
async function fetchAll() {
  loading.value = true
  try {
    const [allQuotes, allResponses, allUsers, allVendors, allBuildings, allPrices] =
      await Promise.all([
        UnitQuotesApi.list(),
        UnitQuoteResponsesApi.list(),
        UsersApi.list(),
        VendorsApi.list(),
        TypeOfBuildingsApi.list(),
        TypeOfPricesApi.list(),
      ])

    quotes.value = allQuotes.map((q) => {
      const r = allResponses.find((res) => res.unit_quote_id === q.id)
      return {
        ...q,
        has_response: !!r,
        editing: false,
        response: r
          ? {
              id: r.id,
              vendor_id: r.vendor_id,
              user_id: r.user_id,
              price: r.price,
              time_line: r.time_line_en || '',
            }
          : {
              vendor_id: '',
              user_id: '',
              price: '',
              time_line: '',
            },
      }
    })

    users.value = allUsers
    vendors.value = allVendors
    buildings.value = allBuildings
    prices.value = allPrices

  } finally {
    loading.value = false
  }
}

/* CRUD */
function enableEdit(q) {
  q.editing = true
}

async function saveResponse(q) {
  const fd = buildQuoteResponseCreateFD({
    unit_quote_id: q.id,
    vendor_id: q.response.vendor_id,
    user_id: q.response.user_id,
    title: { en: q.title, ar: q.title },
    price: q.response.price,
    time_line: { en: q.response.time_line, ar: q.response.time_line },
  })
  await UnitQuoteResponsesApi.create(fd)
  await fetchAll()
}

async function updateResponse(q) {
  const fd = buildQuoteResponseUpdateFD({
    unit_quote_id: q.id,
    vendor_id: q.response.vendor_id,
    user_id: q.response.user_id,
    title: { en: q.title, ar: q.title },
    price: q.response.price,
    time_line: { en: q.response.time_line, ar: q.response.time_line },
  })
  await UnitQuoteResponsesApi.update(q.response.id, fd)
  q.editing = false
  await fetchAll()
}

async function deleteResponse(q) {
  if (!confirm('Delete this response?')) return
  await UnitQuoteResponsesApi.remove(q.response.id)
  await fetchAll()
}

function vendorName(id) {
  return vendors.value.find(v => v.id === id)?.name || '—'
}

function goToPage(page) {
  if (page >= 1 && page <= totalPages.value) currentPage.value = page
}

function nextPage() {
  if (currentPage.value < totalPages.value) currentPage.value++
}

function prevPage() {
  if (currentPage.value > 1) currentPage.value--
}

function formatDate(dateString) {
  if (!dateString) return '—'
  const date = new Date(dateString)
  return date.toLocaleString('en-GB', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

function getEn(value) {
  if (!value) return '—'
  if (typeof value === 'object' && value.en) return value.en
  return value
}

onMounted(fetchAll)
</script>
<style scoped>
.form-input {
  @apply w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500;
}
</style>