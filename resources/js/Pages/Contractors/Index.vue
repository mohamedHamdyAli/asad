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
      <input v-model="search" placeholder="Search title or email" class="form-input w-72" />

      <!-- TABLE -->
      <div class="bg-white rounded-xl shadow border overflow-hidden">
        <table class="w-full text-sm">
          <thead class="bg-gray-100">
            <tr>
              <th class="px-4 py-3">Image</th>
              <th class="px-4 py-3">Title (EN)</th>
              <th class="px-4 py-3">Email</th>
              <th class="px-4 py-3 text-right">Actions</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="c in paginatedRows" :key="c.id" class="border-t">
              <td class="px-4 py-3">
                <img v-if="c.image_url" :src="c.image_url" class="w-16 h-12 object-cover rounded" />
              </td>
              <td class="px-4 py-3">{{ c.title_en }}</td>
              <td class="px-4 py-3">{{ c.email }}</td>
              <td class="px-4 py-3 text-right space-x-2">
                <button class="text-blue-600" @click="openEdit(c)">Details</button>
                <button class="text-red-600" @click="remove(c.id)">Delete</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- PAGINATION -->
      <div class="flex justify-between">
        <span>Page {{ currentPage }} of {{ totalPages }}</span>
        <div class="space-x-2">
          <button @click="currentPage--" :disabled="currentPage === 1">Prev</button>
          <button @click="currentPage++" :disabled="currentPage === totalPages">Next</button>
        </div>
      </div>

      <!-- MODAL -->
      <div v-if="modalOpen" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
        <div class="bg-white w-full max-w-2xl rounded-xl p-6">

          <h3 class="text-lg font-semibold mb-4">
            {{ editing ? 'Edit Contractor' : 'Add Contractor' }}
          </h3>

          <Form :key="formKey" :validation-schema="schema" :initial-values="form" @submit="submit">
            <div class="grid grid-cols-2 gap-4">

<Field name="title.en" class="form-input" placeholder="Title (EN)" />
<ErrorMessage name="title.en" class="text-red-500 text-xs" />

<Field name="title.ar" class="form-input" placeholder="Title (AR)" />
<ErrorMessage name="title.ar" class="text-red-500 text-xs" />

<Field name="email" type="email" class="form-input col-span-2" placeholder="Email" />
<ErrorMessage name="email" class="text-red-500 text-xs col-span-2" />

<Field name="description.en" as="textarea" class="form-input col-span-2" placeholder="Description (EN)" />
<ErrorMessage name="description.en" class="text-red-500 text-xs col-span-2" />

<Field name="description.ar" as="textarea" class="form-input col-span-2" placeholder="Description (AR)" />
<ErrorMessage name="description.ar" class="text-red-500 text-xs col-span-2" />

              <Field name="company_phone" class="form-input" placeholder="Company Phone" />
              <ErrorMessage name="company_phone" class="text-red-500 text-xs" />

              <Field name="representative_phone" class="form-input" placeholder="Representative Phone" />
              <ErrorMessage name="representative_phone" class="text-red-500 text-xs" />

              <Field name="company_address.en" class="form-input" placeholder="Company Address (EN)" />
              <ErrorMessage name="company_address.en" class="text-red-500 text-xs" />

              <Field name="company_address.ar" class="form-input" placeholder="Company Address (AR)" />
              <ErrorMessage name="company_address.ar" class="text-red-500 text-xs" />

              <Field name="representative_name.en" class="form-input" placeholder="Representative Name (EN)" />
              <ErrorMessage name="representative_name.en" class="text-red-500 text-xs" />

              <Field name="representative_name.ar" class="form-input" placeholder="Representative Name (AR)" />
              <ErrorMessage name="representative_name.ar" class="text-red-500 text-xs" />

              <input type="file" accept="image/*" @change="onFile" class="col-span-2" />
            </div>

            <div class="flex justify-end gap-2 mt-4">
              <button type="button" class="border px-4 py-2" @click="closeModal">Cancel</button>
              <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">
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
import { ContractorsApi, buildContractorsCreateFD, buildContractorsUpdateFD } from '@/Services/contractors'

const rows = ref([])
const search = ref('')
const currentPage = ref(1)
const perPage = 10

const modalOpen = ref(false)
const editing = ref(false)
const currentId = ref(null)
const imageFile = ref(null)
const formKey = ref(0)


const form = ref({})

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

  company_phone: yup.string().nullable(),
  representative_phone: yup.string().nullable(),

  company_address: yup.object({
    en: yup.string().nullable(),
    ar: yup.string().nullable(),
  }),

  representative_name: yup.object({
    en: yup.string().nullable(),
    ar: yup.string().nullable(),
  }),
})


async function load() {
  rows.value = await ContractorsApi.list()
}

const filteredRows = computed(() =>
  rows.value.filter(r =>
    r.email?.toLowerCase().includes(search.value.toLowerCase()) ||
    r.title_en?.toLowerCase().includes(search.value.toLowerCase())
  )
)

const totalPages = computed(() =>
  Math.ceil(filteredRows.value.length / perPage)
)

const paginatedRows = computed(() =>
  filteredRows.value.slice((currentPage.value - 1) * perPage, currentPage.value * perPage)
)

function openCreate() {
  editing.value = false
  currentId.value = null

  form.value = {
    title: { en: '', ar: '' },
    description: { en: '', ar: '' },
    email: '',
    company_phone: '',
    representative_phone: '',
    company_address: { en: '', ar: '' },
    representative_name: { en: '', ar: '' },
  }

  imageFile.value = null
  formKey.value++      // 🔴 IMPORTANT
  modalOpen.value = true
}


function openEdit(c) {
  editing.value = true
  currentId.value = c.id

  form.value = {
    title: { en: c.title_en, ar: c.title_ar },
    description: { en: c.description_en, ar: c.description_ar },
    email: c.email,
    company_phone: c.company_phone || '',
    representative_phone: c.representative_phone || '',
    company_address: c.company_address || { en: '', ar: '' },
    representative_name: c.representative_name || { en: '', ar: '' },
  }

  imageFile.value = null
  formKey.value++      // 🔴 IMPORTANT
  modalOpen.value = true
}


function closeModal() {
  modalOpen.value = false
}

function onFile(e) {
  imageFile.value = e.target.files?.[0] || null
}

async function submit(values) {
  const payload = {
    email: values.email,
    title: {
      en: values.title.en || '',
      ar: values.title.ar || '',
    },
    description: {
      en: values.description.en || '',
      ar: values.description.ar || '',
    },
    company_phone: values.company_phone || '',
    representative_phone: values.representative_phone || '',
    company_address: values.company_address || { en: '', ar: '' },
    representative_name: values.representative_name || { en: '', ar: '' },
    image: imageFile.value,
  }

  try {
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
  } catch (e) {
    console.error('SAVE ERROR', e.response?.data || e)
    alert(e.response?.data?.message || 'Save failed')
  }
}


async function remove(id) {
  if (!confirm('Delete contractor?')) return
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
