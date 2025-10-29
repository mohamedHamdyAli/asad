<template>
  <Head title="Units" />
  <AuthenticatedLayout>
    <div class="p-6 space-y-6">
      <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-dash-title">Units</h2>
        <button @click="openCreate" class="bg-black text-white px-3 py-2 rounded hover:bg-gray-700">
          + Add Unit
        </button>
      </div>

      <!-- Filters -->
      <div class="bg-white rounded-2xl border border-gray-200 p-4">
        <div class="grid gap-3 md:grid-cols-5">
          <div class="md:col-span-2">
            <label class="text-xs text-gray-500 mb-1 block">Search</label>
            <input
              v-model.trim="filters.q"
              type="text"
              placeholder="Search by name or location…"
              class="form-input"
            />
          </div>

          <div>
            <label class="text-xs text-gray-500 mb-1 block">Status</label>
            <select v-model="filters.status" class="form-input">
              <option value="">All</option>
              <option value="under_construction">under_construction</option>
              <option value="completed">completed</option>
            </select>
          </div>

          <div>
            <label class="text-xs text-gray-500 mb-1 block">User</label>
            <select v-model.number="filters.user_id" class="form-input">
              <option :value="null">All</option>
              <option v-for="u in userOptions" :key="u.id" :value="u.id">{{ u.name }} — {{ u.email }}</option>
            </select>
          </div>

          <div>
            <label class="text-xs text-gray-500 mb-1 block">Project Manager</label>
            <select v-model.number="filters.vendor_id" class="form-input">
              <option :value="null">All</option>
              <option v-for="v in vendorOptions" :key="v.id" :value="v.id">{{ v.name }} — {{ v.email }}</option>
            </select>
          </div>

          <div>
            <label class="text-xs text-gray-500 mb-1 block">From</label>
            <input v-model="filters.date_from" type="date" class="form-input" />
          </div>

          <div>
            <label class="text-xs text-gray-500 mb-1 block">To</label>
            <input v-model="filters.date_to" type="date" class="form-input" />
          </div>

          <div>
            <label class="text-xs text-gray-500 mb-1 block">Per Page</label>
            <select v-model.number="pagination.perPage" class="form-input">
              <option :value="10">10</option>
              <option :value="20">20</option>
              <option :value="50">50</option>
            </select>
          </div>

          <div class="md:col-span-2 flex items-end gap-2">
            <button @click="resetFilters" class="px-3 py-2 border rounded-lg">Reset</button>
            <button @click="fetchUnits" class="px-3 py-2 border rounded-lg hover:bg-gray-50">Apply</button>
          </div>
        </div>
      </div>

      <!-- Table -->
      <div class="bg-white rounded-2xl border border-gray-200">
        <div class="p-4 flex items-center justify-between">
          <h3 class="text-lg font-semibold text-gray-800">All Units</h3>
          <button @click="fetchUnits" class="px-3 py-1.5 text-sm border rounded-lg hover:bg-gray-50">Refresh</button>
        </div>

        <div v-if="loading" class="px-4 pb-4 text-sm text-gray-500">Loading…</div>

        <div v-else>
          <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
              <thead class="bg-gray-50 text-gray-600">
                <tr>
                  <th class="text-left font-semibold px-4 py-2">#</th>
                  <th class="text-left font-semibold px-4 py-2">Name</th>
                  <th class="text-left font-semibold px-4 py-2">Location</th>
                  <th class="text-left font-semibold px-4 py-2">Period</th>
                  <th class="text-left font-semibold px-4 py-2">Status</th>
                  <th class="text-left font-semibold px-4 py-2">User</th>
                  <th class="text-left font-semibold px-4 py-2">Project Manager</th>
                  <th class="text-right font-semibold px-4 py-2">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="u in rows" :key="u.id" class="border-t last:border-b">
                  <td class="px-4 py-2">{{ u.id }}</td>
                  <td class="px-4 py-2">
                    <div class="font-medium text-gray-900">
                      {{ u.name_en || '—' }} <span v-if="u.name_ar" class="text-gray-400">/</span>
                      <span dir="rtl">{{ u.name_ar }}</span>
                    </div>
                  </td>
                  <td class="px-4 py-2">{{ u.location || '—' }}</td>
                  <td class="px-4 py-2">
                    <div>{{ u.start_date || '—' }} → {{ u.end_date || '—' }}</div>
                  </td>
                  <td class="px-4 py-2">
                    <span
                      class="inline-flex items-center rounded-full border px-2 py-0.5 text-[11px]"
                      :class="u.status === 'completed' ? 'border-green-200 text-green-700 bg-green-50' : 'border-gray-200 text-gray-700 bg-gray-50'"
                    >
                      {{ u.status || '—' }}
                    </span>
                  </td>
                  <td class="px-4 py-2">
                    <div class="text-gray-900">{{ u.user_name || '—' }}</div>
                    <div class="text-xs text-gray-500">{{ u.user_email || '' }}</div>
                  </td>
                  <td class="px-4 py-2">
                    <div class="text-gray-900">{{ u.vendor_name || '—' }}</div>
                    <div class="text-xs text-gray-500">{{ u.vendor_email || '' }}</div>
                  </td>
                  <td class="px-4 py-2">
                    <div class="flex items-center justify-end gap-2">
                      <button @click="openDetails(u.id)" class="px-2 py-1.5 border rounded hover:bg-gray-50">Details</button>
                      <button @click="openEdit(u)" class="px-2 py-1.5 border rounded hover:bg-gray-50">Edit</button>
                      <button @click="remove(u)" class="px-2 py-1.5 border border-red-200 text-red-600 rounded hover:bg-red-50">Delete</button>
                    </div>
                  </td>
                </tr>

                <tr v-if="!rows.length">
                  <td colspan="8" class="px-4 py-8 text-center text-gray-500">No units found.</td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div class="flex items-center justify-between px-4 py-3 border-t text-sm">
            <div class="text-gray-600">
              <template v-if="meta">
                Showing
                <span class="font-medium">{{ from }}</span>–
                <span class="font-medium">{{ to }}</span>
                of <span class="font-medium">{{ meta.total }}</span>
              </template>
            </div>
            <div class="flex items-center gap-2">
              <button
                class="px-2 py-1.5 border rounded disabled:opacity-50"
                :disabled="!canPrev"
                @click="go(meta.current_page - 1)"
              >
                Prev
              </button>
              <div class="px-2">Page {{ meta?.current_page || 1 }} / {{ meta?.last_page || 1 }}</div>
              <button
                class="px-2 py-1.5 border rounded disabled:opacity-50"
                :disabled="!canNext"
                @click="go(meta.current_page + 1)"
              >
                Next
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Details Modal -->
      <div v-if="details.open" class="fixed inset-0 z-50">
        <div class="absolute inset-0 bg-black/40" @click="closeDetails" />
        <div class="relative mx-auto w-full max-w-5xl p-4 sm:p-6 h-full flex items-center justify-center">
          <div class="bg-white rounded-2xl shadow-xl w-full max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white border-b px-5 py-3 rounded-t-2xl flex items-center justify-between">
              <h3 class="text-lg font-bold">Unit Details</h3>
              <button @click="closeDetails" class="text-gray-500 hover:text-gray-800">✕</button>
            </div>

            <div v-if="details.loading" class="p-5 text-sm text-gray-500">Loading…</div>

            <div v-else class="p-5 space-y-5">
              <div class="grid md:grid-cols-3 gap-4">
                <div class="md:col-span-2 space-y-3">
                  <div class="text-xl font-semibold text-gray-900">
                    {{ d.name_en || '—' }} <span v-if="d.name_ar" class="text-gray-400">/</span>
                    <span dir="rtl">{{ d.name_ar }}</span>
                  </div>

                  <div class="text-sm text-gray-600">
                    <div><span class="font-medium">Location:</span> {{ d.location || '—' }}</div>
                    <div><span class="font-medium">Period:</span> {{ d.start_date || '—' }} → {{ d.end_date || '—' }}</div>
                    <div><span class="font-medium">Status:</span> {{ d.status || '—' }}</div>
                  </div>

                  <div class="space-y-1">
                    <div class="text-sm text-gray-800 font-semibold">Description (EN)</div>
                    <div class="text-sm text-gray-700 whitespace-pre-wrap">{{ d.description_en || '—' }}</div>
                  </div>

                  <div class="space-y-1">
                    <div class="text-sm text-gray-800 font-semibold">Description (AR)</div>
                    <div class="text-sm text-gray-700 whitespace-pre-wrap" dir="rtl">{{ d.description_ar || '—' }}</div>
                  </div>

                  <div class="grid grid-cols-2 gap-2">
                    <Link :href="docsPath(d.id)" class="px-3 py-2 border rounded text-center hover:bg-gray-50">Docs</Link>
                    <Link :href="galleryPath(d.id)" class="px-3 py-2 border rounded text-center hover:bg-gray-50">Gallery</Link>
                    <Link :href="drawingPath(d.id)" class="px-3 py-2 border rounded text-center hover:bg-gray-50">Drawing</Link>
                    <Link :href="reportsPath(d.id)" class="px-3 py-2 border rounded text-center hover:bg-gray-50">Reports</Link>
                    <Link :href="phasesPath(d.id)" class="px-3 py-2 border rounded text-center hover:bg-gray-50">Phases</Link>
                    <Link :href="timelinePath(d.id)" class="px-3 py-2 border rounded text-center hover:bg-gray-50">Timeline</Link>
                    <Link :href="unitContractorsPath(d.id)" class="px-3 py-2 border rounded text-center hover:bg-gray-50">Assignments</Link>
                    <Link :href="unitPaymentsPath(d.id)" class="px-3 py-2 border rounded text-center hover:bg-gray-50">Payments & Installments</Link>
                  </div>
                </div>

                <div class="space-y-3">
                  <div class="aspect-[4/3] bg-gray-50 rounded border relative">
                    <img v-if="d.cover_image_url" :src="d.cover_image_url" class="absolute inset-0 w-full h-full object-cover rounded" />
                    <div v-else class="absolute inset-0 flex items-center justify-center text-gray-400 text-sm">No Cover</div>
                  </div>

                  <div>
                    <div class="text-sm font-semibold text-gray-800 mb-2">Gallery</div>
                    <div class="grid grid-cols-3 gap-2">
                      <div v-for="g in (d.gallery || [])" :key="g.id" class="aspect-[4/3] bg-gray-50 rounded border relative">
                        <img :src="g.image_url" class="absolute inset-0 w-full h-full object-cover rounded" />
                      </div>
                      <div v-if="!(d.gallery && d.gallery.length)" class="text-xs text-gray-500">No images</div>
                    </div>
                  </div>

                  <div class="text-xs text-gray-500">
                    <div><span class="font-medium">Lat:</span> {{ d.lat ?? '—' }}</div>
                    <div><span class="font-medium">Lng:</span> {{ d.long ?? '—' }}</div>
                  </div>
                </div>
              </div>

            </div>

            <div class="sticky bottom-0 bg-white border-t px-5 py-3 rounded-b-2xl text-right">
              <button @click="closeDetails" class="px-3 py-2 border rounded">Close</button>
            </div>
          </div>
        </div>
      </div>

      <!-- Create/Edit modal (your existing modal preserved as-is) -->
      <div v-if="showModal" class="fixed inset-0 z-50" @keydown.esc="closeModal">
        <div class="absolute inset-0 bg-black/40 back-drop" @click="closeModal" aria-hidden="true"></div>
        <div class="relative mx-auto w-full max-w-3xl p-4 sm:p-6 h-full flex items-center justify-center">
          <div class="bg-white rounded-2xl shadow-lg w-full max-h-[min(88vh,900px)] overflow-y-auto focus:outline-none" role="dialog" aria-modal="true">
            <!-- header -->
            <div class="sticky top-0 z-10 bg-white border-b rounded-t-2xl">
              <div class="flex items-center justify-between px-5 py-3">
                <h3 class="text-lg font-bold">{{ editingId ? 'Edit Unit' : 'Add Unit' }}</h3>
                <button @click="closeModal" class="text-gray-400 hover:text-gray-600" aria-label="Close">✕</button>
              </div>
            </div>

            <!-- body -->
            <div class="px-5 py-4 space-y-4">
              <!-- keep your existing form exactly as you sent (omitted here for brevity) -->
              <!-- BEGIN: your whole form -->
              <form @submit.prevent="submit" class="space-y-4">
                <!-- *** paste the same form fields you already have here (unchanged) *** -->
                <!-- names -->
                <!-- ... (ALL YOUR FIELDS UNCHANGED) ... -->
                <div v-if="errorMsg" class="rounded border border-red-200 bg-red-50 text-red-700 px-3 py-2">
                  {{ errorMsg }}
                </div>
              </form>
              <!-- END -->
            </div>

            <!-- footer -->
            <div class="sticky bottom-0 z-10 bg-white border-t rounded-b-2xl">
              <div class="px-5 py-3 flex items-center gap-3">
                <button :disabled="saving" class="px-4 py-2 bg-green-600 text-white rounded disabled:opacity-60" @click="submit">
                  {{ saving ? 'Saving…' : (editingId ? 'Update' : 'Create') }}
                </button>
                <button type="button" class="px-3 py-2 border rounded" @click="closeModal">Cancel</button>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { ref, onMounted, watch, computed } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import { UnitsApi, buildUnitCreateFD, buildUnitUpdateFD } from '@/Services/units'
import { UsersApi } from '@/Services/users'
import { VendorsApi } from '@/Services/vendors'

// ===== Filters + pagination =====
const filters = ref({
  q: '',
  status: '',
  user_id: null,
  vendor_id: null,
  date_from: '',
  date_to: ''
})

const pagination = ref({
  page: 1,
  perPage: 10
})

const meta = ref(null)
const canPrev = computed(() => (meta.value ? meta.value.current_page > 1 : false))
const canNext = computed(() => (meta.value ? meta.value.current_page < meta.value.last_page : false))
const from   = computed(() => {
  if (!meta.value) return 0
  return (meta.value.current_page - 1) * meta.value.per_page + 1
})
const to     = computed(() => {
  if (!meta.value) return 0
  return Math.min(meta.value.current_page * meta.value.per_page, meta.value.total)
})
function go(page) {
  if (!meta.value) return
  if (page < 1 || page > meta.value.last_page) return
  pagination.value.page = page
  fetchUnits()
}

// ===== Data =====
const rows = ref([])
const loading = ref(false)
const userOptions = ref([])
const vendorOptions = ref([])

let searchTimer = null
watch(
  () => [filters.value.q, filters.value.status, filters.value.user_id, filters.value.vendor_id, filters.value.date_from, filters.value.date_to, pagination.value.perPage],
  () => {
    // reset to page 1 on filter changes
    pagination.value.page = 1
    // debounce
    clearTimeout(searchTimer)
    searchTimer = setTimeout(fetchUnits, 300)
  }
)

function resetFilters() {
  filters.value = { q: '', status: '', user_id: null, vendor_id: null, date_from: '', date_to: '' }
  pagination.value.page = 1
  fetchUnits()
}

// ===== Fetch =====
async function fetchOptions() {
  const users = await UsersApi.list()
  userOptions.value = users.map(u => ({ id: u.id, name: u.name, email: u.email }))
  const vendors = await VendorsApi.list()
  vendorOptions.value = vendors.map(v => ({ id: v.id, name: v.name, email: v.email }))
}

async function fetchUnits() {
  loading.value = true
  try {
    const params = {
      page: pagination.value.page,
      per_page: pagination.value.perPage,
      q: filters.value.q || undefined,
      status: filters.value.status || undefined,
      user_id: filters.value.user_id || undefined,
      vendor_id: filters.value.vendor_id || undefined,
      date_from: filters.value.date_from || undefined,
      date_to: filters.value.date_to || undefined
    }

    const resp = await UnitsApi.list(params)

    // If API returns {data, meta}
    if (resp && resp.data && resp.meta) {
      rows.value = resp.data
      meta.value = resp.meta
    } else if (Array.isArray(resp)) {
      // Fallback: array only, no pagination info
      rows.value = resp
      meta.value = {
        current_page: 1,
        last_page: 1,
        per_page: resp.length,
        total: resp.length
      }
    } else {
      rows.value = []
      meta.value = { current_page: 1, last_page: 1, per_page: 10, total: 0 }
    }
  } finally {
    loading.value = false
  }
}

onMounted(async () => {
  await Promise.all([fetchOptions(), fetchUnits()])
})

// ===== Quick links =====
function docsPath(id) { return `/units-management/${id}/docs` }
function drawingPath(id) { return `/units-management/${id}/drawing` }
function galleryPath(id) { return `/units-management/${id}/gallery` }
function timelinePath(id) { return `/units-management/${id}/timeline` }
function reportsPath(id) { return `/units-management/${id}/reports` }
function unitContractorsPath(id) { return `/units-management/${id}/contractors` }
function unitPaymentsPath(id) { return `/units-management/${id}/payments` }
function phasesPath(id) { return `/units-management/${id}/phases` }

// ===== Details modal =====
const details = ref({ open: false, loading: false, id: null, data: null })
const d = computed(() => details.value.data || {})

async function openDetails(id) {
  details.value = { open: true, loading: true, id, data: null }
  try {
    const data = await UnitsApi.show(id)
    details.value.data = data
  } catch (e) {
    console.error(e)
  } finally {
    details.value.loading = false
  }
}
function closeDetails() {
  details.value = { open: false, loading: false, id: null, data: null }
}

// ===== Create/Edit/Delete =====
// (Keep your existing create/edit modal + logic)
// Only the hooks are referenced here.
const showModal = ref(false)
const editingId = ref(null)
const saving = ref(false)
const errorMsg = ref('')
const errors = ref({})

function openCreate() {
  // you already have reset + open in your original file
  // call your existing function here if you separated it
  // reset + show:
  resetForm()
  showModal.value = true
}
async function openEdit(u) {
  await originalOpenEdit(u) // see mapping below
  showModal.value = true
}
async function remove(u) {
  if (!confirm(`Delete unit #${u.id}?`)) return
  await UnitsApi.remove(u.id)
  await fetchUnits()
}

// ===== Your original form state + helpers (unchanged) =====
const form = ref({
  name: { en: '', ar: '' },
  description: { en: '', ar: '' },
  location: '',
  lat: null,
  long: null,
  start_date: '',
  end_date: '',
  user_id: null,
  vendor_id: null,
  status: 'under_construction',
  cover_image: null,
  gallery: [],
})
const coverPreview = ref(null)
const galleryPreviews = ref([])
const existingGallery = ref([])

function resetForm() {
  form.value = {
    name: { en: '', ar: '' },
    description: { en: '', ar: '' },
    location: '',
    lat: null,
    long: null,
    start_date: '',
    end_date: '',
    user_id: null,
    vendor_id: null,
    status: 'under_construction',
    cover_image: null,
    gallery: [],
  }
  coverPreview.value = null
  galleryPreviews.value = []
  errorMsg.value = ''
  errors.value = {}
  editingId.value = null
}

async function originalOpenEdit(u) {
  resetForm()
  editingId.value = u.id
  const data = await UnitsApi.show(u.id)
  form.value.name.en = data.name_en || ''
  form.value.name.ar = data.name_ar || ''
  form.value.description.en = data.description_en || ''
  form.value.description.ar = data.description_ar || ''
  form.value.location = data.location || ''
  form.value.lat = data.lat
  form.value.long = data.long
  form.value.start_date = data.start_date || ''
  form.value.end_date = data.end_date || ''
  form.value.user_id = data.user_id ?? null
  form.value.vendor_id = data.vendor_id ?? null
  form.value.status = data.status || ''

  coverPreview.value = data.cover_image_url || null
  form.value.cover_image = null
  existingGallery.value = Array.isArray(data.gallery) ? data.gallery : []
  galleryPreviews.value = []
  form.value.gallery = []
}

function onCover(e) {
  const f = e.target.files?.[0] || null
  form.value.cover_image = f
  coverPreview.value = f ? URL.createObjectURL(f) : coverPreview.value
}
function onGallery(e) {
  const files = Array.from(e.target.files || [])
  form.value.gallery = files
  galleryPreviews.value = files.map(f => URL.createObjectURL(f))
}

// Your validate()/submit() implementations can stay as-is
// Ensure submit() calls fetchUnits() after success and closes modal
async function submit() {
  saving.value = true
  try {
    // call your existing validate() here if you extracted it
    // if (!validate()) { saving.value = false; return }
    if (editingId.value) {
      const fd = buildUnitUpdateFD(form.value)
      await UnitsApi.update(editingId.value, fd)
    } else {
      const fd = buildUnitCreateFD(form.value)
      await UnitsApi.create(fd)
    }
    await fetchUnits()
    showModal.value = false
  } catch (e) {
    errorMsg.value = e?.response?.data?.message || e?.message || 'Request failed'
  } finally {
    saving.value = false
  }
}
</script>

<style scoped>
.form-input {
  @apply w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500;
}
.aspect-\[4\/3\] { aspect-ratio: 4 / 3; }
.back-drop { margin-top: -25px; }
</style>
