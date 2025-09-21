<template>
  <Head title="Intro Management" />

  <AuthenticatedLayout>
    <div class="p-6 space-y-6">
      <div class="flex items-center justify-between">
        <h2 class="text-2xl font-semibold text-dash-title">Intro Management</h2>
        <button
          @click="openCreate"
          class="bg-black text-white px-3 py-2 rounded hover:bg-gray-700 disabled:opacity-50"
        >
          + Add Intro
        </button>
      </div>

      <!-- List -->
      <div class="bg-white p-5 rounded-2xl shadow-sm ring-1 ring-black/[0.05]">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-gray-800">Intro Screens</h3>
          <button
            @click="fetchIntros"
            class="px-3 py-1.5 text-sm border rounded-lg hover:bg-gray-50"
          >
            Refresh
          </button>
        </div>

        <div v-if="listLoading" class="text-sm text-gray-500">Loading…</div>

        <div
          v-else
          class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-5"
        >
          <div
            v-for="item in intros"
            :key="item.id"
            class="group rounded-2xl overflow-hidden bg-white border border-gray-200/70 shadow hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200"
          >
            <!-- Image -->
            <div class="relative aspect-[4/3] bg-gray-50">
              <img
                v-if="item.image_url"
                :src="item.image_url"
                class="absolute inset-0 w-full h-full object-cover"
              />
              <div
                v-else
                class="absolute inset-0 flex items-center justify-center text-gray-400 text-sm"
              >
                No Image
              </div>

              <!-- top-left badges -->
              <div class="absolute top-2 left-2 flex flex-wrap gap-2">
                <span
                  class="inline-flex items-center rounded-full bg-white/90 backdrop-blur px-2 py-0.5 text-[11px] font-medium text-gray-700 border border-gray-200"
                >
                  Order: {{ item.order ?? '—' }}
                </span>
                <span
                  :class="[
                    'inline-flex items-center rounded-full backdrop-blur px-2 py-0.5 text-[11px] font-medium border',
                    item.is_enabled
                      ? 'bg-green-100 text-green-700 border-green-200'
                      : 'bg-red-100 text-red-700 border-red-200'
                  ]"
                >
                  {{ item.is_enabled ? 'Enabled' : 'Disabled' }}
                </span>
              </div>
            </div>

            <!-- Body -->
            <div class="p-3.5 space-y-3">
              <!-- Names -->
              <div class="text-sm font-semibold text-gray-900 whitespace-pre-wrap break-words">
                {{ item.name_en || '—' }}
                <span v-if="item.name_ar" class="text-gray-400"> / </span>
                <span dir="rtl">{{ item.name_ar }}</span>
              </div>

              <!-- Description -->
              <div class="text-xs text-gray-600">
                <p>{{ item.description_en || '—' }}</p>
                <p dir="rtl" class="text-gray-500">{{ item.description_ar || '—' }}</p>
              </div>

              <!-- Actions -->
              <div class="flex gap-2 pt-3">
                <button
                  class="px-3 py-1.5 text-sm rounded-lg border bg-white hover:bg-gray-50"
                  @click="openEdit(item)"
                >
                  Edit
                </button>
                <button
                  class="px-3 py-1.5 text-sm rounded-lg border border-red-200 text-red-700 bg-red-50 hover:bg-red-100"
                  @click="remove(item.id)"
                >
                  Delete
                </button>
              </div>
            </div>
          </div>

          <div
            v-if="!intros.length"
            class="col-span-full text-center text-gray-500 py-8"
          >
            No intros found.
          </div>
        </div>
      </div>

      <!-- Modal -->
      <div v-if="showModal" class="fixed inset-0 z-50" @keydown.esc="closeModal">
        <div class="absolute inset-0 bg-black/40 back-drop" @click="closeModal"></div>
        <div
          class="relative mx-auto w-full max-w-2xl p-4 sm:p-6 h-full flex items-center justify-center"
        >
          <div
            class="bg-white rounded-2xl shadow-lg w-full max-h-[90vh] overflow-y-auto focus:outline-none"
          >
            <!-- Header -->
            <div class="sticky top-0 z-10 bg-white border-b rounded-t-2xl">
              <div class="flex items-center justify-between px-5 py-3">
                <h3 class="text-lg font-bold">
                  {{ editingId ? "Edit Intro" : "Add Intro" }}
                </h3>
                <button
                  @click="closeModal"
                  class="text-gray-400 hover:text-gray-600"
                >
                  ✕
                </button>
              </div>
            </div>

            <!-- Body -->
            <div class="px-5 py-4 space-y-4">
              <form @submit.prevent="submit" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <input v-model="form.name.en" class="form-input" type="text" placeholder="Name (EN)" />
                  <input v-model="form.name.ar" class="form-input" type="text" placeholder="الاسم (AR)" />

                  <textarea v-model="form.description.en" class="form-input" placeholder="Description (EN)"></textarea>
                  <textarea v-model="form.description.ar" class="form-input" placeholder="الوصف (AR)"></textarea>

                  <input v-model.number="form.order" class="form-input" type="number" placeholder="Order" />

                  <label class="flex items-center gap-2">
                    <input type="checkbox" v-model="form.is_enabled" />
                    <span>Enabled</span>
                  </label>

                  <div class="space-y-2 md:col-span-2">
                    <input type="file" @change="onFile" class="form-input" />
                    <div v-if="imagePreview" class="mt-2">
                      <img :src="imagePreview" class="w-40 h-24 object-cover rounded border" />
                    </div>
                  </div>
                </div>

                <div v-if="errorMsg" class="rounded border border-red-200 bg-red-50 text-red-700 px-3 py-2">
                  {{ errorMsg }}
                </div>
              </form>
            </div>

            <!-- Footer -->
            <div class="sticky bottom-0 z-10 bg-white border-t rounded-b-2xl">
              <div class="px-5 py-3 flex items-center gap-3">
                <button
                  :disabled="loading"
                  class="px-4 py-2 bg-green-600 text-white rounded disabled:opacity-60"
                  @click="submit"
                >
                  {{ loading ? "Saving…" : (editingId ? "Update" : "Create") }}
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
import { ref, onMounted } from 'vue'
import { IntroApi, buildIntroFormData } from '@/Services/intro'
import { Head } from '@inertiajs/vue3'

const intros = ref([])
const listLoading = ref(false)

const showModal = ref(false)
const editingId = ref(null)
const loading = ref(false)
const errorMsg = ref('')
const fieldErrors = ref({})
const imagePreview = ref(null)

const form = ref({
  name: { en: '', ar: '' },
  description: { en: '', ar: '' },
  image: null,
  order: '',
  is_enabled: true,
})

function resetForm() {
  editingId.value = null
  form.value = { name: { en: '', ar: '' }, description: { en: '', ar: '' }, image: null, order: '', is_enabled: true }
  imagePreview.value = null
  errorMsg.value = ''
  fieldErrors.value = {}
}

function openCreate() {
  resetForm()
  showModal.value = true
}

async function openEdit(item) {
  resetForm()
  editingId.value = item.id
  form.value = {
    name: { en: item.name_en || '', ar: item.name_ar || '' },
    description: { en: item.description_en || '', ar: item.description_ar || '' },
    order: item.order ?? '',
    is_enabled: !!item.is_enabled,
    image: null,
  }
  imagePreview.value = item.image_url || null
  showModal.value = true
}

function closeModal() {
  showModal.value = false
}

function onFile(e) {
  const file = e.target.files?.[0]
  form.value.image = file || null
  imagePreview.value = file ? URL.createObjectURL(file) : null
}

async function fetchIntros() {
  try {
    listLoading.value = true
    intros.value = await IntroApi.index()
  } finally {
    listLoading.value = false
  }
}

async function submit() {
  loading.value = true
  errorMsg.value = ''
  fieldErrors.value = {}
  try {
    const fd = buildIntroFormData(form.value)
    if (editingId.value) await IntroApi.update(editingId.value, fd)
    else await IntroApi.create(fd)
    await fetchIntros()
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
    loading.value = false
  }
}

async function remove(id) {
  if (!confirm('Are you sure you want to delete this intro?')) return
  await IntroApi.destroy(id)
  await fetchIntros()
}

onMounted(fetchIntros)
</script>

<style scoped>
.form-input {
  @apply w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500;
}
.aspect-\[4\/3\] {
  aspect-ratio: 4 / 3;
}

.back-drop {
margin-top: -25px !important;
}
</style>
