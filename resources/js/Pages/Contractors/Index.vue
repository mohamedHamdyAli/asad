<template>
  <AuthenticatedLayout>
    <div class="p-6 space-y-6">

      <!-- HEADER -->
      <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold">Contractors</h2>
        <button class="px-4 py-2 bg-blue-600 text-white rounded" @click="openCreate">
          + Add Contractor
        </button>
      </div>

      <!-- SEARCH -->
      <div class="flex gap-4">
        <input
          v-model="search"
          placeholder="Search by title or email"
          class="form-input w-72"
        />
      </div>

      <!-- TABLE -->
      <div class="bg-white rounded-xl shadow border overflow-hidden">
        <table class="w-full text-sm">
          <thead class="bg-gray-100">
            <tr>
              <th class="px-4 py-3 text-left">Image</th>
              <th class="px-4 py-3 text-left">Title (EN)</th>
              <th class="px-4 py-3 text-left">Email</th>
              <th class="px-4 py-3 text-right">Actions</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="c in paginatedRows" :key="c.id" class="border-t">
              <td class="px-4 py-3">
                <img
                  v-if="c.image_url"
                  :src="c.image_url"
                  class="w-16 h-12 object-cover rounded border"
                />
                <span v-else class="text-gray-400">—</span>
              </td>

              <td class="px-4 py-3">{{ c.title_en }}</td>
              <td class="px-4 py-3">{{ c.email }}</td>

              <td class="px-4 py-3 text-right space-x-2">
                <button class="text-blue-600" @click="openEdit(c)">Details</button>
                <button class="text-red-600" @click="remove(c.id)">Delete</button>
              </td>
            </tr>

            <tr v-if="filteredRows.length === 0">
              <td colspan="4" class="text-center py-6 text-gray-500">
                No contractors found
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- PAGINATION -->
      <div class="flex justify-between items-center">
        <span class="text-sm text-gray-600">
          Page {{ currentPage }} of {{ totalPages }}
        </span>

        <div class="flex gap-2">
          <button
            class="px-3 py-1 border rounded"
            :disabled="currentPage === 1"
            @click="currentPage--"
          >
            Prev
          </button>
          <button
            class="px-3 py-1 border rounded"
            :disabled="currentPage === totalPages"
            @click="currentPage++"
          >
            Next
          </button>
        </div>
      </div>

      <!-- MODAL -->
      <div
        v-if="modalOpen"
        class="fixed inset-0 bg-black/40 flex items-center justify-center z-50"
      >
        <div class="bg-white w-full max-w-xl rounded-xl p-6">

          <h3 class="text-lg font-semibold mb-4">
            {{ editing ? 'Edit Contractor' : 'Add Contractor' }}
          </h3>

          <Form
            :validation-schema="schema"
            :initial-values="form"
            @submit="submit"
          >
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

              <Field name="title.en" class="form-input" placeholder="Title (EN)" />
              <ErrorMessage name="title.en" class="error" />

              <Field name="title.ar" class="form-input" placeholder="Title (AR)" />
              <ErrorMessage name="title.ar" class="error" />

              <Field name="email" type="email" class="form-input" placeholder="Email" />
              <ErrorMessage name="email" class="error" />

              <Field
                name="description.en"
                as="textarea"
                class="form-input md:col-span-2"
                placeholder="Description (EN)"
              />
              <ErrorMessage name="description.en" class="error md:col-span-2" />

              <Field
                name="description.ar"
                as="textarea"
                class="form-input md:col-span-2"
                placeholder="Description (AR)"
              />
              <ErrorMessage name="description.ar" class="error md:col-span-2" />

              <input
                type="file"
                accept="image/*"
                @change="onFile"
                class="md:col-span-2"
              />

            </div>

            <div class="flex justify-end gap-2 mt-4">
              <button
                type="button"
                class="px-4 py-2 border rounded"
                @click="closeModal"
              >
                Cancel
              </button>
              <button
                type="submit"
                class="px-4 py-2 bg-green-600 text-white rounded"
              >
                Save
              </button>
            </div>
          </Form>

        </div>
      </div>

    </div>
  </AuthenticatedLayout>
</template>
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { ref, onMounted, computed } from 'vue'
import { Form, Field, ErrorMessage } from 'vee-validate'
import * as yup from 'yup'

import {
  ContractorsApi,
  buildContractorsCreateFD,
  buildContractorsUpdateFD
} from '@/Services/contractors'

/* STATE */
const rows = ref([])
const search = ref('')
const currentPage = ref(1)
const perPage = 10

const modalOpen = ref(false)
const editing = ref(false)
const currentId = ref(null)
const imageFile = ref(null)

const form = ref({})

/* VALIDATION */
const schema = yup.object({
  title: yup.object({
    en: yup.string().required(),
    ar: yup.string().required(),
  }),
  description: yup.object({
    en: yup.string().required(),
    ar: yup.string().required(),
  }),
  email: yup.string().email().required(),
})

/* LOAD */
async function load() {
  rows.value = await ContractorsApi.list()
}

/* SEARCH + PAGINATION */
const filteredRows = computed(() => {
  const q = search.value.toLowerCase()
  return rows.value.filter(r =>
    r.title_en?.toLowerCase().includes(q) ||
    r.title_ar?.toLowerCase().includes(q) ||
    r.email?.toLowerCase().includes(q)
  )
})

const totalPages = computed(() =>
  Math.ceil(filteredRows.value.length / perPage)
)

const paginatedRows = computed(() => {
  const start = (currentPage.value - 1) * perPage
  return filteredRows.value.slice(start, start + perPage)
})

/* MODAL */
function openCreate() {
  editing.value = false
  form.value = {
    title: { en: '', ar: '' },
    description: { en: '', ar: '' },
    email: '',
  }
  imageFile.value = null
  modalOpen.value = true
}

function openEdit(c) {
  editing.value = true
  currentId.value = c.id
  form.value = {
    title: { en: c.title_en, ar: c.title_ar },
    description: { en: c.description_en, ar: c.description_ar },
    email: c.email,
  }
  modalOpen.value = true
}

function closeModal() {
  modalOpen.value = false
}

function onFile(e) {
  imageFile.value = e.target.files?.[0] || null
}

/* SAVE */
async function submit(values) {
  const payload = { ...values, image: imageFile.value }

  if (editing.value) {
    await ContractorsApi.update(
      currentId.value,
      buildContractorsUpdateFD(payload)
    )
  } else {
    await ContractorsApi.create(
      buildContractorsCreateFD([payload])
    )
  }

  closeModal()
  load()
}

/* DELETE */
async function remove(id) {
  if (!confirm('Delete this contractor?')) return
  await ContractorsApi.remove(id)
  load()
}

onMounted(load)
</script>
<style scoped>
.form-input {
  @apply w-full border border-gray-300 rounded px-3 py-2;
}
.error {
  @apply text-red-600 text-xs;
}
</style>
