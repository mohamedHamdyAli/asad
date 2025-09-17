<template>
      <Head title="Intro Management" />

  <AuthenticatedLayout>
    <div class="p-6 space-y-6">
      <h2 class="text-2xl font-semibold text-gray-800">Intro Management</h2>

      <!-- Create / Update -->
      <div class="bg-white p-4 rounded shadow">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-bold">
            {{ editingId ? "Edit Intro" : "Add New Intro" }}
          </h3>
          <button v-if="editingId" @click="resetForm" class="px-3 py-1 border rounded">Cancel</button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <input v-model="form.name.en" class="form-input" type="text" placeholder="Name (EN)" />
          <input v-model="form.name.ar" class="form-input" type="text" placeholder="الاسم (AR)" />

          <textarea v-model="form.description.en" class="form-input" type="text" placeholder="Description (EN)"></textarea>
          <textarea v-model="form.description.ar" class="form-input" type="text" placeholder="الوصف (AR)"></textarea>

          <input v-model.number="form.order" class="form-input" type="number" placeholder="Order" />

          <label class="flex items-center gap-2">
            <input type="checkbox" v-model="form.is_enabled" />
            <span>Enabled</span>
          </label>

          <div class="space-y-2">
            <input type="file" @change="onFile" class="form-input" />
            <div v-if="imagePreview" class="text-sm text-gray-600">
              <span class="block mb-2">Image preview:</span>
              <img :src="imagePreview" class="w-40 h-24 object-cover rounded border" />
            </div>
          </div>
        </div>

        <div class="mt-4 flex items-center gap-3">
          <button :disabled="loading" @click="submit" class="px-4 py-2 bg-green-600 text-white rounded disabled:opacity-60">
            {{ editingId ? "Update Intro" : "Add Intro" }}
          </button>
          <span v-if="loading" class="text-gray-500 text-sm">Saving…</span>
        </div>

        <div v-if="errorMsg" class="mt-3 text-red-600 text-sm">{{ errorMsg }}</div>
        <ul v-if="Object.keys(fieldErrors).length" class="mt-2 text-sm text-red-600 list-disc pl-6">
          <li v-for="(errs, key) in fieldErrors" :key="key">
            <strong>{{ key }}:</strong> {{ errs.join(", ") }}
          </li>
        </ul>
      </div>

      <!-- List -->
      <div class="bg-white p-4 rounded shadow">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-bold">Intro Screens</h3>
          <button @click="fetchIntros" class="px-3 py-1 border rounded">Refresh</button>
        </div>

        <div v-if="listLoading" class="text-sm text-gray-500">Loading…</div>

        <div v-else>
          <div v-for="item in intros" :key="item.id" class="flex justify-between items-center border-b py-3">
            <div class="min-w-0">
              <div class="font-semibold truncate">
                {{ item.name_en }} <span class="text-gray-400">/</span> {{ item.name_ar }}
              </div>
              <div class="text-sm text-gray-600 truncate">
                {{ item.description_en }} <span class="text-gray-400">/</span> {{ item.description_ar }}
              </div>
              <div class="text-xs text-gray-500">
                Order: {{ item.order ?? "-" }} |
                Enabled: {{ item.is_enabled ? "Yes" : "No" }}
              </div>
            </div>
            <div class="flex gap-3 items-center">
              <img v-if="item.image_url" :src="item.image_url" class="w-16 h-10 object-cover rounded border" />
              <button @click="editRow(item.id)" class="text-blue-600 hover:underline">Edit</button>
              <button @click="remove(item.id)" class="text-red-600 hover:underline">Delete</button>
            </div>
          </div>

          <div v-if="!intros.length" class="text-center text-gray-500 py-8">No intros found.</div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { onMounted, ref } from 'vue'
import { IntroApi, buildIntroFormData } from '@/Services/intro'
import { Head } from '@inertiajs/vue3'

const intros = ref([])
const listLoading = ref(false)

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
    resetForm()
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

async function editRow(id) {
  const row = await IntroApi.edit(id)
  editingId.value = id
  form.value = {
    name: { en: row.name_en || '', ar: row.name_ar || '' },
    description: { en: row.description_en || '', ar: row.description_ar || '' },
    order: row.order ?? '',
    is_enabled: !!row.is_enabled,
    image: null,
  }
  imagePreview.value = row.image_url || null
}

function onFile(e) {
  const file = e.target.files?.[0]
  form.value.image = file || null
  imagePreview.value = file ? URL.createObjectURL(file) : null
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
  @apply w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500;
}
</style>
