<template>
  <AuthenticatedLayout>
    <div class="p-6 space-y-6">

      <!-- HEADER -->
      <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold">Consultants</h2>
        <button class="px-4 py-2 bg-blue-600 text-white rounded" @click="openCreate">
          + Add Consultant
        </button>
      </div>

      <!-- SEARCH -->
      <div class="flex gap-4">
        <input v-model="search" placeholder="Search by title or email" class="form-input w-72" />
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
                <img v-if="c.image_url" :src="c.image_url" class="w-16 h-12 object-cover rounded border" />
                <span v-else class="text-gray-400">—</span>
              </td>

              <td class="px-4 py-3">{{ c.title_en }}</td>
              <td class="px-4 py-3">{{ c.email }}</td>

              <td class="px-4 py-3 text-right space-x-2">
                <button class="text-blue-600" @click="openEdit(c)">Edit</button>
                <button class="text-red-600" @click="remove(c.id)">Delete</button>
              </td>
            </tr>

            <tr v-if="filteredRows.length === 0">
              <td colspan="4" class="text-center py-6 text-gray-500">
                No consultants found
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
          <button class="px-3 py-1 border rounded" :disabled="currentPage === 1" @click="currentPage--">
            Prev
          </button>
          <button class="px-3 py-1 border rounded" :disabled="currentPage === totalPages" @click="currentPage++">
            Next
          </button>
        </div>
      </div>

      <!-- MODAL -->
      <div v-if="modalOpen" class="fixed back-drop inset-0 bg-black/40 flex items-center justify-center z-50">
        <div class="bg-white w-full max-w-xl rounded-xl p-6 max-h-[85vh] overflow-y-auto">

          <h3 class="text-lg font-semibold mb-4">
            {{ editing ? 'Edit Consultant' : 'Add Consultant' }}
          </h3>

          <Form :key="formKey" :validate-on-mount="false" :validation-schema="schema" :initial-values="form"
           @invalid-submit="onInvalidSubmit" @submit="submit" v-slot="{ setFieldValue, submitCount }">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

              <!-- Title EN -->
              <div>
                <Field name="title.en" class="form-input" placeholder="Title (EN)" />
                <ErrorMessage name="title.en" class="error" />
              </div>

              <!-- Title AR -->
              <div>
                <Field name="title.ar" class="form-input" placeholder="Title (AR)" />
                <ErrorMessage name="title.ar" class="error" />
              </div>

              <!-- Email -->
              <div class="md:col-span-2">
                <Field name="email" type="email" class="form-input" placeholder="Email" />
                <ErrorMessage name="email" class="error" />
              </div>

              <!-- Description EN -->
              <div class="md:col-span-2">
                <Field name="description.en" as="textarea" class="form-input" placeholder="Description (EN)" />
                <ErrorMessage name="description.en" class="error" />
              </div>

              <!-- Description AR -->
              <div class="md:col-span-2">
                <Field name="description.ar" as="textarea" class="form-input" placeholder="Description (AR)"
                  dir="rtl" />
                <ErrorMessage name="description.ar" class="error" />
              </div>

              <!-- Company Phone -->
              <div>
                <Field name="company_phone" class="form-input" placeholder="Company Phone" />
                <ErrorMessage name="company_phone" class="error" />
              </div>

              <!-- Representative Phone -->
              <div>
                <Field name="representative_phone" class="form-input" placeholder="Representative Phone" />
                <ErrorMessage name="representative_phone" class="error" />
              </div>

              <!-- Company Address EN -->
              <div>
                <Field name="company_address.en" class="form-input" placeholder="Company Address (EN)" />
                <ErrorMessage name="company_address.en" class="error" />
              </div>

              <!-- Company Address AR -->
              <div>
                <Field name="company_address.ar" class="form-input" placeholder="Company Address (AR)" />
                <ErrorMessage name="company_address.ar" class="error" />
              </div>

              <!-- Representative Name EN -->
              <div>
                <Field name="representative_name.en" class="form-input" placeholder="Representative Name (EN)" />
                <ErrorMessage name="representative_name.en" class="error" />
              </div>

              <!-- Representative Name AR -->
              <div>
                <Field name="representative_name.ar" class="form-input" placeholder="Representative Name (AR)" />
                <ErrorMessage name="representative_name.ar" class="error" />
              </div>


              <!-- Image -->
              <div class="md:col-span-2 space-y-2">

                <!-- Existing / New Preview -->
                <img v-if="imagePreview" :src="imagePreview" class="w-32 h-24 object-cover rounded border" />

                <input type="file" accept="image/*" @change="(e) => onFileChange(e, setFieldValue)" />

                <ErrorMessage v-if="submitCount > 0" name="image" class="error" />
              </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-2 mt-6">
              <button type="button" class="px-4 py-2 border rounded" @click="closeModal">
                Cancel
              </button>
              <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">
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
import { useServerError } from '@/composables/useServerError'

const { show } = useServerError()

import {
  ConsultantsApi,
  buildConsultantsCreateFD,
  buildConsultantsUpdateFD
} from '@/Services/consultants'

/* STATE */
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

/* VALIDATION */
const schema = computed(() =>
  yup.object({
    title: yup.object({
      en: yup.string().required('EN title is required'),
      ar: yup.string().required('AR title is required'),
    }),

    description: yup.object({
      en: yup.string().required('English description is required'),
      ar: yup.string().required('Arabic description is required'),
    }),

    email: yup.string().email('Invalid email').required('Email is required'),

    company_phone: yup.string().required('Company phone is required'),
    representative_phone: yup.string().required('Representative phone is required'),

    company_address: yup.object({
      en: yup.string().required('EN address is required'),
      ar: yup.string().required('AR address is required'),
    }),

    representative_name: yup.object({
      en: yup.string().required('EN representative name is required'),
      ar: yup.string().required('AR representative name is required'),
    }),

    image: editing.value
      ? yup.mixed().nullable()
      : yup
          .mixed()
          .required('Image is required')
          .test('file', 'Invalid image', (v) => v instanceof File),
  })
)


const imagePreview = ref(null)

function onInvalidSubmit(ctx) {
  console.log('INVALID SUBMIT', ctx.errors)
}

/* LOAD */
async function load() {
  rows.value = await ConsultantsApi.list()
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
  currentId.value = null

  form.value = {
    title: { en: '', ar: '' },
    description: { en: '', ar: '' },
    email: '',
    company_phone: '',
    representative_phone: '',
    company_address: { en: '', ar: '' },
    representative_name: { en: '', ar: '' },
    image: null,
  }

  imageFile.value = null
  imagePreview.value = null
  formKey.value++
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
    image: null,
  }

  imageFile.value = null
  imagePreview.value = c.image_url || null
  formKey.value++
  modalOpen.value = true
}


function closeModal() {
  modalOpen.value = false
}

function onFile(e) {
  imageFile.value = e.target.files?.[0] || null
}

function onFileChange(e, setFieldValue) {
  const file = e.target.files?.[0] || null
  imageFile.value = file
  setFieldValue('image', file)

  if (file) {
    imagePreview.value = URL.createObjectURL(file)
  }
}


/* SAVE */
async function submit(values) {
  const payload = {
    email: values.email,
    title: values.title,
    description: values.description,
    company_phone: values.company_phone || '',
    representative_phone: values.representative_phone || '',
    company_address: values.company_address || { en: '', ar: '' },
    representative_name: values.representative_name || { en: '', ar: '' },
    image: imageFile.value,
  }
  try {
    if (editing.value) {
      await ConsultantsApi.update(currentId.value, buildConsultantsUpdateFD(payload))
    } else {
      await ConsultantsApi.create(buildConsultantsCreateFD([payload]))
    }
    closeModal()
    load()
  } catch (e) {
    show(e)
  }

}

/* DELETE */
async function remove(id) {
  if (!confirm('Delete this consultant?')) return
  await ConsultantsApi.remove(id)
  load()
}

onMounted(load)
</script>
<style scoped>
.form-input {
  @apply w-full border border-gray-300 rounded px-3 py-2;
}

.error {
  @apply text-red-600 text-xs mt-1;
}

.back-drop {
  margin-top: -25px !important;
}
</style>
