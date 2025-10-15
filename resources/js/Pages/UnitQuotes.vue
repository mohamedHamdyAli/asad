<template>
  <AuthenticatedLayout>
    <div class="p-6 space-y-8">
      <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800">Unit Quotations</h2>
      </div>

      <!-- Create -->
      <div class="bg-white p-6 rounded-2xl shadow space-y-4">
        <h3 class="text-lg font-semibold text-gray-800">Add New Quotation</h3>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="label">Title *</label>
            <input v-model="form.title" class="form-input" />
          </div>
          <div>
            <label class="label">Other Title</label>
            <input v-model="form.other_title" class="form-input" />
          </div>
          <div>
            <label class="label">User ID *</label>
            <input v-model="form.user_id" type="number" class="form-input" />
          </div>
          <div>
            <label class="label">Building Type *</label>
            <input v-model="form.type_of_building_id" type="number" class="form-input" />
          </div>
          <div>
            <label class="label">Price Type *</label>
            <input v-model="form.type_of_price_id" type="number" class="form-input" />
          </div>
          <div>
            <label class="label">Pay Image *</label>
            <input type="file" accept="image/*,.pdf" @change="onPayImage" class="form-input" />
            <img v-if="preview" :src="preview" class="w-32 h-24 mt-2 object-cover rounded border" />
          </div>
          <div class="md:col-span-3">
            <label class="label">Gallery *</label>
            <input type="file" multiple accept="image/*,.pdf" @change="onGallery" class="form-input" />
            <div v-if="galleryPreviews.length" class="flex flex-wrap gap-2 mt-2">
              <img
                v-for="(g, i) in galleryPreviews"
                :key="i"
                :src="g"
                class="w-24 h-16 object-cover rounded border"
              />
            </div>
          </div>
        </div>

        <div class="flex justify-end">
          <button
            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-60"
            :disabled="creating"
            @click="createQuote"
          >
            {{ creating ? "Saving…" : "Create Quotation" }}
          </button>
        </div>
      </div>

      <!-- List -->
      <div class="bg-white p-6 rounded-2xl shadow">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-gray-800">All Quotations</h3>
          <button @click="fetchList" class="px-3 py-1.5 text-sm border rounded-lg hover:bg-gray-50">
            Refresh
          </button>
        </div>

        <div v-if="loading" class="text-sm text-gray-500">Loading…</div>
        <div v-else-if="!rows.length" class="text-center text-gray-500 py-8">No quotations found.</div>

        <div v-else class="overflow-x-auto">
          <table class="min-w-full border border-gray-200 text-sm">
            <thead class="bg-gray-100 text-gray-700">
              <tr>
                <th class="py-2 px-3 text-left">#</th>
                <th class="py-2 px-3 text-left">Title</th>
                <th class="py-2 px-3 text-left">User</th>
                <th class="py-2 px-3 text-left">Building Type</th>
                <th class="py-2 px-3 text-left">Price Type</th>
                <th class="py-2 px-3 text-center">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="q in rows" :key="q.id" class="border-t hover:bg-gray-50 transition">
                <td class="py-2 px-3">#{{ q.id }}</td>
                <td class="py-2 px-3">{{ q.title }}</td>
                <td class="py-2 px-3">{{ q.user_id }}</td>
                <td class="py-2 px-3">{{ q.type_of_building_id }}</td>
                <td class="py-2 px-3">{{ q.type_of_price_id }}</td>
                <td class="py-2 px-3 text-center">
                  <button
                    class="px-3 py-1 text-xs border rounded text-blue-600 border-blue-200 hover:bg-blue-50 mr-2"
                    @click="openEdit(q)"
                  >
                    Edit
                  </button>
                  <button
                    class="px-3 py-1 text-xs border rounded text-red-600 border-red-200 hover:bg-red-50"
                    @click="remove(q.id)"
                  >
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
        <div v-if="showModal" class="fixed inset-0 z-[1000] flex items-center justify-center">
          <div class="absolute inset-0 bg-black/40" @click="closeModal"></div>
          <div
            class="relative z-10 bg-white rounded-2xl shadow-2xl w-full max-w-3xl p-6 overflow-y-auto max-h-[90vh]"
          >
            <div class="flex justify-between mb-4">
              <h3 class="text-lg font-semibold text-gray-800">Edit Quotation #{{ selected?.id }}</h3>
              <button @click="closeModal" class="text-gray-500 hover:text-gray-700 text-xl">✕</button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="label">Title</label>
                <input v-model="editForm.title" class="form-input" />
              </div>
              <div>
                <label class="label">Other Title</label>
                <input v-model="editForm.other_title" class="form-input" />
              </div>
              <div>
                <label class="label">User ID</label>
                <input v-model="editForm.user_id" class="form-input" />
              </div>
              <div>
                <label class="label">Building Type</label>
                <input v-model="editForm.type_of_building_id" class="form-input" />
              </div>
              <div>
                <label class="label">Price Type</label>
                <input v-model="editForm.type_of_price_id" class="form-input" />
              </div>
              <div>
                <label class="label">Replace Pay Image</label>
                <input type="file" accept="image/*,.pdf" @change="onPayEdit" class="form-input" />
                <img
                  v-if="editPreview"
                  :src="editPreview"
                  class="w-32 h-24 mt-2 object-cover rounded border"
                />
              </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
              <button class="px-4 py-2 border rounded-lg" @click="closeModal">Cancel</button>
              <button
                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-60"
                :disabled="saving"
                @click="saveEdit"
              >
                {{ saving ? "Saving…" : "Save Changes" }}
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
  UnitQuotesApi,
  buildQuoteCreateFD,
  buildQuoteUpdateFD,
} from '@/Services/unitQuotes.js'

const rows = ref([])
const loading = ref(false)
const creating = ref(false)
const saving = ref(false)
const showModal = ref(false)
const selected = ref(null)
const preview = ref(null)
const editPreview = ref(null)
const galleryPreviews = ref([])

const form = ref({
  title: '',
  other_title: '',
  user_id: '',
  type_of_building_id: '',
  type_of_price_id: '',
  pay_image: null,
  gallery: [],
})
const editForm = ref({})

function onPayImage(e) {
  const f = e.target.files?.[0]
  if (f) {
    form.value.pay_image = f
    preview.value = URL.createObjectURL(f)
  }
}
function onGallery(e) {
  const files = Array.from(e.target.files || [])
  form.value.gallery = files
  galleryPreviews.value = files.map((f) => URL.createObjectURL(f))
}

async function fetchList() {
  loading.value = true
  try {
    rows.value = await UnitQuotesApi.list()
  } finally {
    loading.value = false
  }
}

async function createQuote() {
  creating.value = true
  try {
    const fd = buildQuoteCreateFD(form.value)
    await UnitQuotesApi.create(fd)
    await fetchList()
    form.value = {
      title: '',
      other_title: '',
      user_id: '',
      type_of_building_id: '',
      type_of_price_id: '',
      pay_image: null,
      gallery: [],
    }
    preview.value = null
    galleryPreviews.value = []
  } finally {
    creating.value = false
  }
}

function openEdit(q) {
  selected.value = q
  editForm.value = { ...q }
  showModal.value = true
}
function closeModal() {
  showModal.value = false
  selected.value = null
  editPreview.value = null
}
function onPayEdit(e) {
  const f = e.target.files?.[0]
  if (f) {
    editForm.value.pay_image = f
    editPreview.value = URL.createObjectURL(f)
  }
}
async function saveEdit() {
  saving.value = true
  try {
    const fd = buildQuoteUpdateFD(editForm.value)
    await UnitQuotesApi.update(selected.value.id, fd)
    await fetchList()
    closeModal()
  } finally {
    saving.value = false
  }
}
async function remove(id) {
  if (!confirm('Delete this quotation?')) return
  await UnitQuotesApi.remove(id)
  await fetchList()
}
onMounted(fetchList)
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
