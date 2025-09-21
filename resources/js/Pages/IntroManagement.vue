<template>
      <Head title="Intro Management" />

  <AuthenticatedLayout>
    <div class="p-6 space-y-6">
      <h2 class="text-2xl font-semibold text-white">Intro Management</h2>

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

  <div v-else>
    <div v-if="intros.length">
      <table class="min-w-full border-collapse">
        <thead>
          <tr class="bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
            <th class="px-4 py-3 border-b">Image</th>
            <th class="px-4 py-3 border-b">Name</th>
            <th class="px-4 py-3 border-b">Description</th>
            <th class="px-4 py-3 border-b">Order</th>
            <th class="px-4 py-3 border-b">Enabled</th>
            <th class="px-4 py-3 border-b text-center">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="item in intros"
            :key="item.id"
            class="hover:bg-gray-50 transition-colors odd:bg-gray-50/50"
          >
            <!-- Image -->
            <td class="px-4 py-3 align-middle">
              <img
                v-if="item.image_url"
                :src="item.image_url"
                class="w-16 h-10 object-cover rounded border"
              />
              <span v-else class="text-xs text-gray-400 italic">No image</span>
            </td>

            <!-- Name -->
            <td class="px-4 py-3 align-middle">
              <div class="font-medium text-gray-900 truncate">
                {{ item.name_en || '—' }}
              </div>
              <div class="text-gray-500 text-sm truncate" dir="rtl">
                {{ item.name_ar || '—' }}
              </div>
            </td>

            <!-- Description -->
            <td class="px-4 py-3 align-middle">
              <div class="truncate text-sm text-gray-700">
                {{ item.description_en || '—' }}
              </div>
              <div class="truncate text-sm text-gray-500" dir="rtl">
                {{ item.description_ar || '—' }}
              </div>
            </td>

            <!-- Order -->
            <td class="px-4 py-3 align-middle text-sm text-gray-600">
              {{ item.order ?? '—' }}
            </td>

            <!-- Enabled -->
            <td class="px-4 py-3 align-middle">
              <span
                :class="[
                  'px-2 py-0.5 rounded-full text-xs font-medium',
                  item.is_enabled
                    ? 'bg-green-100 text-green-700'
                    : 'bg-red-100 text-red-700'
                ]"
              >
                {{ item.is_enabled ? 'Yes' : 'No' }}
              </span>
            </td>

            <!-- Actions -->
            <td class="px-4 py-3 align-middle text-center space-x-3">
              <button
                @click="editRow(item.id)"
                class="text-blue-600 hover:underline text-sm"
              >
                Edit
              </button>
              <button
                @click="remove(item.id)"
                class="text-red-600 hover:underline text-sm"
              >
                Delete
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-else class="text-center text-gray-500 py-8">
      No intros found.
    </div>
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
