<template>
  <AuthenticatedLayout>
    <div class="p-6 space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div class="space-y-1">
          <h2 class="text-2xl font-bold text-gray-900">Quotation Details</h2>
          <p class="text-sm text-gray-500">Full details submitted by the user.</p>
        </div>

        <div class="flex gap-2">
          <button class="px-3 py-2 border rounded-lg hover:bg-gray-50" @click="goBack">
            Back
          </button>
          <button class="px-3 py-2 border rounded-lg hover:bg-gray-50" @click="load" :disabled="loading">
            <span v-if="loading">Refreshing…</span>
            <span v-else>Refresh</span>
          </button>
        </div>
      </div>

      <!-- Error -->
      <div v-if="errorMsg" class="rounded border border-red-200 bg-red-50 text-red-700 px-3 py-2">
        {{ errorMsg }}
      </div>

      <!-- Loading -->
      <div v-if="loading" class="text-sm text-gray-500">Loading…</div>

      <div v-else-if="!quote" class="text-sm text-gray-500">
        Quote not found.
      </div>

      <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left: Quote info -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Quote card -->
          <div class="bg-white rounded-2xl shadow-sm ring-1 ring-black/[0.05] p-5">
            <div class="flex items-start justify-between gap-4">
              <div>
                <h3 class="text-lg font-semibold text-gray-900">{{ quote.title || '—' }}</h3>
                <p class="text-sm text-gray-500 mt-1">Created: {{ formatDate(quote.created_at) }}</p>
              </div>

              <span :class="[
                'px-2 py-0.5 rounded-full text-xs border',
                hasResponse
                  ? 'bg-green-100 text-green-700 border-green-200'
                  : 'bg-yellow-100 text-yellow-700 border-yellow-200'
              ]">
                {{ hasResponse ? 'Responded' : 'Pending' }}
              </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4 text-sm">
              <div class="rounded-xl border p-3 bg-gray-50">
                <div class="text-xs text-gray-500 mb-1">Other Title</div>
                <div class="text-gray-900 font-medium">{{ quote.other_title || '—' }}</div>
              </div>

              <div class="rounded-xl border p-3 bg-gray-50">
                <div class="text-xs text-gray-500 mb-1">Owner</div>
                <div class="text-gray-900 font-medium">
                  {{ user?.name || `User ${quote.user_id}` }}
                </div>
                <div class="text-xs text-gray-500 mt-1">
                  {{ user?.phone || '—' }}
                </div>
              </div>

              <div class="rounded-xl border p-3">
                <div class="text-xs text-gray-500 mb-1">Building Type</div>
                <div class="text-gray-900 font-medium">{{ buildingTitle || '—' }}</div>
              </div>

              <div class="rounded-xl border p-3">
                <div class="text-xs text-gray-500 mb-1">Price Type</div>
                <div class="text-gray-900 font-medium">{{ priceTitle || '—' }}</div>
              </div>
            </div>
          </div>

          <!-- Payment image -->
          <div class="bg-white rounded-2xl shadow-sm ring-1 ring-black/[0.05] p-5">
            <h4 class="font-semibold text-gray-900 mb-3">Payment Image</h4>

            <div v-if="quote.pay_image" class="rounded-2xl border overflow-hidden">
              <a :href="payImageUrl" target="_blank" class="block">
                <img :src="payImageUrl" class="w-full max-h-[360px] object-cover" />
              </a>
            </div>

            <div v-else class="text-sm text-gray-500">No payment image.</div>
          </div>

          <!-- Gallery -->
          <div class="bg-white rounded-2xl shadow-sm ring-1 ring-black/[0.05] p-5">
            <div class="flex items-center justify-between mb-3">
              <h4 class="font-semibold text-gray-900">Drawings Gallery</h4>
              <span class="text-xs text-gray-500">{{ gallery.length }} image(s)</span>
            </div>

            <div v-if="!gallery.length" class="text-sm text-gray-500">No gallery images.</div>

            <div v-else class="grid grid-cols-2 md:grid-cols-3 gap-3">
              <a v-for="img in gallery" :key="img.id" :href="img.image_url" target="_blank"
                class="rounded-xl overflow-hidden border bg-gray-50">
                <img :src="img.image_url" class="w-full h-32 object-cover hover:scale-[1.02] transition" />
              </a>
            </div>
          </div>
        </div>

        <!-- Right: Response -->
        <div class="space-y-6">
          <div class="bg-white rounded-2xl shadow-sm ring-1 ring-black/[0.05] p-5">
            <h4 class="font-semibold text-gray-900 mb-3">PM Response</h4>

            <div v-if="hasResponse" class="space-y-3 text-sm">
              <div class="rounded-xl border p-3">
                <div class="text-xs text-gray-500 mb-1">Project Manager</div>
                <div class="font-medium text-gray-900">{{ vendor?.name || '—' }}</div>
              </div>

              <div class="rounded-xl border p-3">
                <div class="text-xs text-gray-500 mb-1">Price</div>
                <div class="font-medium text-gray-900">{{ response.price ?? '—' }}</div>
              </div>

              <div class="rounded-xl border p-3">
                <div class="text-xs text-gray-500 mb-1">Timeline</div>
                <div class="font-medium text-gray-900">{{ response.time_line_en || response.time_line || '—' }}</div>
              </div>
            </div>

            <div v-else class="text-sm text-gray-500">
              No response yet.
            </div>
          </div>

        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, router } from '@inertiajs/vue3'
import { ref, computed, onMounted } from 'vue'
import { useServerError } from '@/composables/useServerError'

import { UnitQuotesApi } from '@/Services/unitQuotes.js'
import { UnitQuoteResponsesApi } from '@/Services/unitQuoteResponses.js'
import { UsersApi } from '@/Services/users.js'
import { VendorsApi } from '@/Services/vendors.js'
import { TypeOfBuildingApi } from '@/Services/typeOfBuilding.js'
import { TypeOfPriceApi } from '@/Services/typeOfPrice.js'

const props = defineProps({
  id: { type: Number, required: true }
})

const { show } = useServerError()

const loading = ref(false)
const errorMsg = ref('')

const quote = ref(null)
const response = ref(null)
const user = ref(null)
const vendor = ref(null)

const buildingTitle = ref('—')
const priceTitle = ref('—')

const gallery = computed(() => quote.value?.gallery || [])
const hasResponse = computed(() => !!response.value)

const payImageUrl = computed(() => quote.value?.pay_image ? storageUrl(quote.value.pay_image) : '')

function goBack() {
  router.visit(route('unit-quotes-responses'))
}

function storageUrl(path) {
  return path ? `/storage/${path}` : ''
}

function formatDate(dateString) {
  if (!dateString) return '—'
  const d = new Date(dateString)
  return d.toLocaleString('en-GB', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })
}

function parseTitle(v) {
  try { return typeof v === 'string' ? JSON.parse(v) : v }
  catch { return { en: '', ar: '' } }
}

async function load() {
  loading.value = true
  errorMsg.value = ''

  try {
    const res = await UnitQuotesApi.show(props.id)

    const q = res?.data ?? res
    if (!q || !q.id) {
      throw new Error('Quote not found')
    }

    quote.value = q

    console.log('GALLERY:', q.unit_quote_gallery) 

    const [
      responses,
      users,
      vendors,
      buildings,
      prices,
    ] = await Promise.all([
      UnitQuoteResponsesApi.list(),
      UsersApi.list(),
      VendorsApi.list(),
      TypeOfBuildingApi.list(),
      TypeOfPriceApi.list(),
    ])

    response.value =
      (responses || []).find(r => r.unit_quote_id === q.id) || null

    user.value =
      (users || []).find(u => u.id === q.user_id) || null

    vendor.value =
      response.value?.vendor_id
        ? (vendors || []).find(v => v.id === response.value.vendor_id) || null
        : null

    const b =
      (buildings?.data ?? buildings ?? [])
        .find(x => x.id === q.type_of_building_id)

    buildingTitle.value =
      b ? (parseTitle(b.title).en || '—') : '—'

    const p =
      (prices?.data ?? prices ?? [])
        .find(x => x.id === q.type_of_price_id)

    priceTitle.value =
      p ? (parseTitle(p.title).en || '—') : '—'

  } catch (e) {
    console.error(e)
    errorMsg.value = show(e) || 'Failed to load quote details.'
    quote.value = null
  } finally {
    loading.value = false
  }
}




onMounted(load)
</script>
