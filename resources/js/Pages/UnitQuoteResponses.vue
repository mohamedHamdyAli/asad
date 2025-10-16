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
      <div
        class="flex justify-between items-center bg-white p-4 rounded-lg shadow-sm"
      >
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

      <!-- Table -->
      <div class="bg-white p-6 rounded-2xl shadow">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
          Quotes & Responses
        </h3>

        <div v-if="loading" class="text-sm text-gray-500">Loading…</div>
        <div
          v-else-if="!paginatedQuotes.length"
          class="text-center text-gray-500 py-6"
        >
          No quotes found.
        </div>

        <div v-else class="overflow-x-auto">
          <table class="min-w-full border border-gray-200 text-sm">
            <thead class="bg-gray-100 text-gray-700">
              <tr>
                <th class="py-2 px-3 text-left"></th>
                <th class="py-2 px-3 text-left">Title</th>
                <th class="py-2 px-3 text-left">Other Title</th>
                <th class="py-2 px-3 text-left">Building Type</th>
                <th class="py-2 px-3 text-left">Price Type</th>
                <th class="py-2 px-3 text-left">User</th>
                <th class="py-2 px-3 text-left">Vendor</th>
                <th class="py-2 px-3 text-left">Price</th>
                <th class="py-2 px-3 text-left">Timeline</th>
                <th class="py-2 px-3 text-left">Payment</th>
                <th class="py-2 px-3 text-left">Status</th>
                <th class="py-2 px-3 text-center">Actions</th>
              </tr>
            </thead>

       <tbody>
  <tr
    v-for="q in paginatedQuotes"
    :key="q.id"
    class="border-t hover:bg-gray-50 transition"
    :class="{ 'bg-blue-50/40': q.editing }"
  >
    <!-- ID -->
    <td class="py-2 px-3 font-medium">{{ q.id }}</td>

    <!-- Title / Other title -->
    <!-- <td class="py-2 px-3">{{ q.title }}</td> -->
     <!-- Title + created_at -->
<td class="py-2 px-3">
  <div class="font-medium text-gray-900">{{ q.title }}</div>
  <div class="text-xs text-gray-500 mt-0.5">
    {{ formatDate(q.created_at) }}
  </div>
</td>

    <td class="py-2 px-3">{{ q.other_title || '—' }}</td>

    <!-- Building Type (read-only) -->
    <td class="py-2 px-3 text-gray-700">
      {{
        buildings.find((b) => b.id === q.type_of_building_id)?.name ||
        buildings.find((b) => b.id === q.type_of_building_id)?.title ||
        '—'
      }}
    </td>

    <!-- Price Type (read-only) -->
    <td class="py-2 px-3 text-gray-700">
      {{
        prices.find((p) => p.id === q.type_of_price_id)?.name ||
        prices.find((p) => p.id === q.type_of_price_id)?.title ||
        '—'
      }}
    </td>

    <!-- User (read-only) -->
    <td class="py-2 px-3 text-gray-700">
      {{
        users.find((u) => u.id === q.user_id)?.name ||
        `User ${q.user_id}`
      }}
    </td>

    <!-- Vendor (editable dropdown) -->
    <td class="py-2 px-3">
      <select
        v-model="q.response.vendor_id"
        class="form-input"
        :disabled="!q.editing && q.has_response"
      >
        <option value="" disabled>Select Vendor</option>
        <option v-for="v in vendors" :key="v.id" :value="v.id">
          {{ v.name }}
        </option>
      </select>
    </td>

    <!-- Price (editable) -->
    <td class="py-2 px-3">
      <input
        v-model.number="q.response.price"
        type="number"
        class="form-input"
        :disabled="!q.editing && q.has_response"
      />
    </td>

    <!-- Timeline (editable) -->
    <td class="py-2 px-3">
      <input
        v-model="q.response.time_line"
        type="text"
        class="form-input"
        :disabled="!q.editing && q.has_response"
      />
    </td>

    <!-- Pay image (if available) -->
    <td class="py-2 px-3">
      <a
        v-if="q.pay_image"
        :href="`/storage/${q.pay_image}`"
        target="_blank"
        class="text-blue-600 hover:underline"
      >
        <img
          :src="`/storage/${q.pay_image}`"
          alt="Preview"
          class="h-10 w-10 object-cover rounded shadow-sm"
        />
      </a>
      <span v-else class="text-gray-400">—</span>
    </td>

    <!-- Status -->
    <td class="py-2 px-3">
      <span
        :class="[
          'px-2 py-0.5 rounded-full text-xs border',
          q.has_response
            ? 'bg-green-100 text-green-700 border-green-200'
            : 'bg-yellow-100 text-yellow-700 border-yellow-200',
        ]"
      >
        {{ q.has_response ? 'Responded' : 'Pending' }}
      </span>
    </td>

    <!-- Actions -->
    <td class="py-2 px-3 text-center space-x-1">
      <button
        v-if="!q.has_response"
        class="px-3 py-1 text-xs border rounded-lg text-blue-700 border-blue-200 hover:bg-blue-50"
        @click="saveResponse(q)"
      >
        Save
      </button>

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
          <div
            v-if="totalPages > 1"
            class="flex justify-between items-center mt-6 text-sm"
          >
            <span class="text-gray-600">
              Showing {{ startItem }}–{{ endItem }} of {{ filteredQuotes.length }}
            </span>
            <div class="space-x-2">
              <button
                @click="prevPage"
                :disabled="currentPage === 1"
                class="px-3 py-1 border rounded-lg hover:bg-gray-50 disabled:opacity-50"
              >
                Prev
              </button>
              <button
                v-for="n in totalPages"
                :key="n"
                @click="goToPage(n)"
                class="px-3 py-1 border rounded-lg"
                :class="n === currentPage ? 'bg-blue-600 text-white' : 'hover:bg-gray-50'"
              >
                {{ n }}
              </button>
              <button
                @click="nextPage"
                :disabled="currentPage === totalPages"
                class="px-3 py-1 border rounded-lg hover:bg-gray-50 disabled:opacity-50"
              >
                Next
              </button>
            </div>
          </div>
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

/* Pagination */
const currentPage = ref(1)
const perPage = ref(7)

/* Computed */
const filteredQuotes = computed(() => {
  const term = search.value.toLowerCase()
  return quotes.value
    .filter(
      (q) =>
        q.title.toLowerCase().includes(term) ||
        (q.user_name && q.user_name.toLowerCase().includes(term))
    )
    .sort((a, b) => b.id - a.id) // newest first
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

function formatDate(dateString) {
  if (!dateString) return '—'
  const date = new Date(dateString)
  return date.toLocaleString('en-GB', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

function goToPage(page) {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page
  }
}

function nextPage() {
  if (currentPage.value < totalPages.value) {
    currentPage.value++
  }
}

function prevPage() {
  if (currentPage.value > 1) {
    currentPage.value--
  }
}



onMounted(fetchAll)
</script>

<style scoped>
.form-input {
  @apply w-full border border-gray-300 rounded-md px-3 py-2 text-sm
         focus:outline-none focus:ring-2 focus:ring-blue-500;
}
</style>
