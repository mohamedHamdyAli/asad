<template>
  <Head title="Units" />
  <AuthenticatedLayout>
    <div class="p-6 space-y-6">
      <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800">Units</h2>
        <button @click="openCreate"
          class="inline-flex items-center rounded-md bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
          + Add Unit
        </button>
      </div>

      <!-- List -->
      <div class="bg-white shadow rounded-lg p-4">
        <div class="flex items-center justify-between mb-3">
          <h3 class="text-lg font-bold">All Units</h3>
          <button @click="fetchUnits" class="px-3 py-1 border rounded">Refresh</button>
        </div>

        <div v-if="loading" class="text-sm text-gray-500">Loading…</div>

        <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
          <div v-for="u in rows" :key="u.id" class="rounded border overflow-hidden bg-gray-50">
            <div class="aspect-[4/3] bg-white flex items-center justify-center">
              <img v-if="u.cover_image_url" :src="u.cover_image_url" class="w-full h-full object-cover" />
              <div v-else class="text-gray-400 text-sm">No Cover</div>
            </div>

            <div class="p-3 space-y-2">
              <div class="font-semibold truncate">
                {{ u.name_en || '—' }} <span class="text-gray-400" v-if="u.name_ar">/</span> {{ u.name_ar }}
              </div>
              <div class="text-xs text-gray-500">
                <span class="font-medium">Location:</span> {{ u.location || '—' }}
              </div>
              <div class="text-xs text-gray-500">
                <span class="font-medium">Period:</span> {{ u.start_date || '—' }} → {{ u.end_date || '—' }}
              </div>
              <div class="text-xs text-gray-500">
                <span class="font-medium">Status:</span> {{ u.status || '—' }}
              </div>

              <!-- Section links (wire later) -->
              <div class="grid grid-cols-3 gap-2 pt-2 text-xs">
                <!-- <a :href="unitSection(u.id, 'folders')"
                  class="px-2 py-1 border rounded text-center hover:bg-gray-100">Folders</a> -->
                <Link :href="docsPath(u.id)" class="px-2 py-1 border rounded text-center hover:bg-gray-100">
                Docs
                </Link>
                <Link :href="galleryPath(u.id)" class="px-2 py-1 border rounded text-center hover:bg-gray-100">
                Gallery
                </Link>

                <Link :href="drawingPath(u.id)" class="px-2 py-1 border rounded text-center hover:bg-gray-100">
                Drawing
                </Link>
                <Link :href="reportsPath(u.id)" class="px-2 py-1 border rounded text-center hover:bg-gray-100">
                Reports
                </Link>
                <Link :href="phasesPath(u.id)" class="px-2 py-1 border rounded text-center hover:bg-gray-100">
                Phases
                </Link>
                <Link :href="timelinePath(u.id)" class="px-2 py-1 border rounded text-center hover:bg-gray-100">
                TimeLine
                </Link>
              </div>

              <div class="flex gap-2 pt-2">
                <button class="px-3 py-1 border rounded text-sm" @click="openEdit(u)">Edit</button>
                <button class="px-3 py-1 border rounded text-sm text-red-600" @click="remove(u)">Delete</button>
              </div>
            </div>
          </div>

          <div v-if="!rows.length" class="col-span-full text-center text-gray-500 py-8">
            No units found.
          </div>
        </div>
      </div>

<!-- Modal -->
<div
  v-if="showModal"
  class="fixed inset-0 z-50"
  @keydown.esc="closeModal"
>
  <!-- Backdrop -->
  <div
    class="absolute inset-0 bg-black/40"
    @click="closeModal"
    aria-hidden="true"
  ></div>

  <!-- Panel -->
  <div
    class="relative mx-auto w-full max-w-3xl p-4 sm:p-6 h-full flex items-center justify-center"
  >
    <div
      class="bg-white rounded-2xl shadow-lg w-full
             max-h-[min(88vh,900px)] overflow-y-auto
             focus:outline-none"
      role="dialog"
      aria-modal="true"
    >
      <!-- Sticky Header -->
      <div class="sticky top-0 z-10 bg-white border-b rounded-t-2xl">
        <div class="flex items-center justify-between px-5 py-3">
          <h3 class="text-lg font-bold">
            {{ editingId ? 'Edit Unit' : 'Add Unit' }}
          </h3>
          <button
            @click="closeModal"
            class="text-gray-400 hover:text-gray-600"
            aria-label="Close"
          >
            ✕
          </button>
        </div>
      </div>

      <!-- Scrollable Body -->
      <div class="px-5 py-4 space-y-4">
        <form @submit.prevent="submit" class="space-y-4">
          <!-- names -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs text-gray-500 mb-1">Name (EN)*</label>
              <input v-model="form.name.en" class="form-input" type="text" :required="!editingId" />
            </div>
            <div>
              <label class="block text-xs text-gray-500 mb-1">Name (AR)*</label>
              <input v-model="form.name.ar" class="form-input" type="text" :required="!editingId" />
            </div>
            <div class="md:col-span-2">
              <label class="block text-xs text-gray-500 mb-1">Description (EN)*</label>
              <input v-model="form.description.en" class="form-input" type="text" :required="!editingId" />
            </div>
            <div class="md:col-span-2">
              <label class="block text-xs text-gray-500 mb-1">Description (AR)*</label>
              <input v-model="form.description.ar" class="form-input" type="text" :required="!editingId" />
            </div>
          </div>

          <!-- required meta -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs text-gray-500 mb-1">Location*</label>
              <input v-model="form.location" class="form-input" type="text" :required="!editingId" />
            </div>
            <div>
              <label class="block text-xs text-gray-500 mb-1">Latitude (-90..90)*</label>
              <input v-model.number="form.lat" class="form-input" type="number" step="any" min="-90" max="90" :required="!editingId" />
            </div>
            <div>
              <label class="block text-xs text-gray-500 mb-1">Longitude (-180..180)*</label>
              <input v-model.number="form.long" class="form-input" type="number" step="any" min="-180" max="180" :required="!editingId" />
            </div>

            <!-- Map (full width for comfort) -->
            <div class="md:col-span-2">
              <MapPicker
                label="Pick on map"
                v-model:lat="form.lat"
                v-model:lng="form.long"
                v-model:address="form.location"
              />
            </div>

            <div>
              <label class="block text-xs text-gray-500 mb-1">Start Date*</label>
              <input v-model="form.start_date" class="form-input" type="date" :required="!editingId" />
            </div>
            <div>
              <label class="block text-xs text-gray-500 mb-1">End Date* (>= start)</label>
              <input v-model="form.end_date" class="form-input" type="date" :required="!editingId" />
            </div>
          </div>

         <!-- associations + status -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
  <div>
    <label class="block text-xs text-gray-500 mb-1">User *</label>
    <select v-model.number="form.user_id" class="form-input" :required="!editingId">
      <option value="" disabled>Select a user</option>
      <option v-for="u in userOptions" :key="u.id" :value="u.id">
        {{ u.name }}
      </option>
    </select>
  </div>

  <div>
    <label class="block text-xs text-gray-500 mb-1">Vendor *</label>
    <select v-model.number="form.vendor_id" class="form-input" :required="!editingId">
      <option value="" disabled>Select a vendor</option>
      <option v-for="v in vendorOptions" :key="v.id" :value="v.id">
        {{ v.name }}
      </option>
    </select>
  </div>

  <div>
    <label class="block text-xs text-gray-500 mb-1">Status</label>
    <select v-model="form.status" class="form-input">
      <option value="under_construction">under_construction</option>
      <option value="completed">completed</option>
    </select>
  </div>
</div>


          <!-- files -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs text-gray-500 mb-1">
                Cover Image* {{ editingId ? '(optional to replace)' : '' }}
              </label>
              <input type="file" accept="image/*" @change="onCover" :required="!editingId" />
              <div v-if="coverPreview" class="mt-2">
                <img :src="coverPreview" class="w-40 h-28 object-cover rounded border" />
              </div>
            </div>
            <div>
              <label class="block text-xs text-gray-500 mb-1">Gallery* (multiple on create)</label>
              <input type="file" accept="image/*" multiple @change="onGallery" :required="!editingId" />
              <div v-if="galleryPreviews.length" class="mt-2 flex flex-wrap gap-2">
                <img v-for="(src, i) in galleryPreviews" :key="i" :src="src" class="w-24 h-16 object-cover rounded border" />
              </div>
            </div>
          </div>

          <!-- Errors -->
          <div v-if="errorMsg" class="text-sm text-red-600">{{ errorMsg }}</div>
          <ul v-if="Object.keys(fieldErrors).length" class="text-sm text-red-600 list-disc pl-6">
            <li v-for="(errs, key) in fieldErrors" :key="key">
              <strong>{{ key }}:</strong> {{ errs.join(', ') }}
            </li>
          </ul>
        </form>
      </div>

      <!-- Sticky Footer -->
      <div class="sticky bottom-0 z-10 bg-white border-t rounded-b-2xl">
        <div class="px-5 py-3 flex items-center gap-3">
          <button :disabled="saving" class="px-4 py-2 bg-indigo-600 text-white rounded disabled:opacity-60"
          @click="submit"
          >
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
import { ref, onMounted , watch , nextTick} from 'vue'
import MapPicker from '@/Components/MapPicker.vue'
import { UnitsApi, buildUnitCreateFD, buildUnitUpdateFD } from '@/Services/units'
import { Link } from '@inertiajs/vue3'
import { Head } from '@inertiajs/vue3'
import { UsersApi } from '@/Services/users'
import { VendorsApi } from '@/Services/vendors'

const userOptions = ref([])
const vendorOptions = ref([])

async function fetchOptions() {
  const users = await UsersApi.list()
  userOptions.value = users.map(u => ({
    id: u.id, name: u.name, email: u.email
  }))

  const vendors = await VendorsApi.list()
  vendorOptions.value = vendors.map(v => ({
    id: v.id, name: v.name, email: v.email
  }))
}

onMounted(async () => {
  await Promise.all([fetchUnits(), fetchOptions()])
})

const rows = ref([])
const loading = ref(false)

const showModal = ref(false)
const editingId = ref(null)

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
  status: '',
  cover_image: null,
  gallery: [],
})

const coverPreview = ref(null)
const galleryPreviews = ref([])

const saving = ref(false)
const errorMsg = ref('')
const fieldErrors = ref({})

function unitSection(id, section) { return `/units/${id}/${section}` }
function docsPath(id) { return `/units-management/${id}/docs` }
function drawingPath(id) { return `/units-management/${id}/drawing` }
function galleryPath(id) { return `/units-management/${id}/gallery` }
function timelinePath(id) { return `/units-management/${id}/timeline` }
function reportsPath(id) { return `/units-management/${id}/reports` }
// const phasesPath = id => `/units-management/${id}/phases`
function phasesPath(id) { return `/units-management/${id}/phases` }

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
    status: '',
    cover_image: null,
    gallery: [],
  }
  coverPreview.value = null
  galleryPreviews.value = []
  errorMsg.value = ''
  fieldErrors.value = {}
  editingId.value = null
}

function openCreate() { resetForm(); showModal.value = true }

async function openEdit(u) {
  resetForm()
  editingId.value = u.id
  const data = await UnitsApi.show(u.id)
  // preload
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
  showModal.value = true
}

function onMapAddress(addr) {
  form.value.location = addr || form.value.location
}

watch(() => showModal.value, async (open) => {
  if (open) await nextTick()
})

watch(() => showModal.value, (open) => {
  document.body.style.overflow = open ? 'hidden' : ''
})


function closeModal() { showModal.value = false }

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

async function fetchUnits() {
  loading.value = true
  try {
    rows.value = await UnitsApi.list()
  } finally {
    loading.value = false
  }
}

async function submit() {
  saving.value = true
  errorMsg.value = ''
  fieldErrors.value = {}
  try {
    if (editingId.value) {
      const fd = buildUnitUpdateFD(form.value)
      await UnitsApi.update(editingId.value, fd)
    } else {
      const fd = buildUnitCreateFD(form.value)
      await UnitsApi.create(fd)
    }
    await fetchUnits()
    closeModal()
  } catch (e) {
    const status = e?.response?.status
    if (status === 422 && e?.response?.data?.errors) {
      fieldErrors.value = e.response.data.errors
      errorMsg.value = e.response.data.message || 'Validation error'
    } else {
      errorMsg.value = e?.response?.data?.message || e?.message || 'Request failed'
    }
  } finally {
    saving.value = false
  }
}

async function remove(u) {
  if (!confirm(`Delete unit #${u.id}?`)) return
  await UnitsApi.remove(u.id)
  await fetchUnits()
}

onMounted(fetchUnits)
</script>

<style scoped>
.form-input {
  @apply w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500;
}

.aspect-\[4\/3\] {
  aspect-ratio: 4 / 3;
}
</style>
