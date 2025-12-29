<template>
  <Head title="Vendors Management" />

  <AuthenticatedLayout>
    <div class="p-6 space-y-6">

      <!-- HEADER -->
      <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold">Project Managers (PM)</h2>
        <button
          class="px-4 py-2 bg-black text-white rounded"
          @click="openCreate"
        >
          + Add PM
        </button>
      </div>

      <!-- SEARCH -->
      <div class="flex gap-4">
        <input
          v-model="search"
          placeholder="Search name, email, or phone"
          class="form-input w-80"
        />
      </div>

      <!-- TABLE -->
      <div class="bg-white rounded-xl shadow border overflow-hidden">
        <table class="w-full text-sm">
          <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
            <tr>
              <th class="px-4 py-3">#</th>
              <th class="px-4 py-3">Avatar</th>
              <th class="px-4 py-3">Name</th>
              <th class="px-4 py-3">Email</th>
              <th class="px-4 py-3">Phone</th>
              <th class="px-4 py-3">Enabled</th>
              <th class="px-4 py-3 text-right">Actions</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="v in paginatedRows" :key="v.id" class="border-t">
              <td class="px-4 py-3">{{ v.id }}</td>

              <td class="px-4 py-3">
                <img
                  v-if="v.profile_image_url"
                  :src="v.profile_image_url"
                  class="w-10 h-10 rounded-full object-cover"
                />
                <span v-else class="text-gray-400">—</span>
              </td>

              <td class="px-4 py-3 font-medium">{{ v.name }}</td>
              <td class="px-4 py-3">{{ v.email }}</td>
              <td class="px-4 py-3">
                {{ v.country_code }} {{ v.phone }}
              </td>

              <td class="px-4 py-3">
                <span
                  :class="v.is_enabled ? 'text-green-600' : 'text-gray-400'"
                >
                  {{ v.is_enabled ? 'Yes' : 'No' }}
                </span>
              </td>

              <td class="px-4 py-3 text-right space-x-2">
                <button class="text-blue-600" @click="openEdit(v)">
                  Edit
                </button>
                <button class="text-red-600" @click="remove(v)">
                  Delete
                </button>
              </td>
            </tr>

            <tr v-if="filteredRows.length === 0">
              <td colspan="7" class="text-center py-6 text-gray-500">
                No PMs found
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
        v-if="showModal"
        class="fixed inset-0 bg-black/40 flex items-center justify-center z-50"
      >
        <div class="bg-white rounded-xl w-full max-w-2xl p-6 relative">

          <button
            class="absolute top-3 right-3 text-gray-400"
            @click="closeModal"
          >
            ✕
          </button>

          <h3 class="text-lg font-bold mb-4">
            {{ editingId ? 'Edit PM' : 'Add PM' }}
          </h3>

          <Form
            :validation-schema="schema"
            :initial-values="form"
            @submit="submit"
          >
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

              <Field name="name" class="form-input md:col-span-2" placeholder="Name" />
              <ErrorMessage name="name" class="error md:col-span-2" />

              <Field name="email" type="email" class="form-input" placeholder="Email" />
              <ErrorMessage name="email" class="error" />

              <div class="flex gap-2">
                <Field name="country_code" class="form-input w-1/3" placeholder="+20" />
                <Field name="phone" class="form-input flex-1" placeholder="Phone" />
              </div>
              <ErrorMessage name="country_code" class="error" />
              <ErrorMessage name="phone" class="error" />

              <Field
                name="password"
                type="password"
                class="form-input"
                :placeholder="editingId ? 'Password (optional)' : 'Password'"
              />
              <ErrorMessage name="password" class="error" />

              <Field as="select" name="gender" class="form-input">
                <option value="">Select gender</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
              </Field>
              <ErrorMessage name="gender" class="error" />

              <Field as="select" name="country_name" class="form-input">
                <option value="">Select country</option>
                <option
                  v-for="c in countryList"
                  :key="c.name"
                  :value="c.name"
                >
                  {{ c.name }}
                </option>
              </Field>
              <ErrorMessage name="country_name" class="error" />

              <div class="flex items-center gap-2">
                <Field type="checkbox" name="is_enabled" />
                <span class="text-sm">Enabled</span>
              </div>

              <input type="file" accept="image/*" @change="onFile" class="md:col-span-2" />
              <img v-if="imagePreview" :src="imagePreview" class="w-24 h-24 rounded border" />

            </div>

            <div class="flex justify-end gap-2 mt-6">
              <button type="button" class="px-4 py-2 border rounded" @click="closeModal">
                Cancel
              </button>
              <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">
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
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import { Head } from "@inertiajs/vue3"
import { ref, onMounted, computed } from "vue"
import { Form, Field, ErrorMessage } from "vee-validate"
import * as yup from "yup"
import { VendorsApi, buildVendorCreateFD, buildVendorUpdateFD } from "@/Services/vendors"
import { countries as countriesData } from "countries-list"

/* STATE */
const rows = ref([])
const search = ref("")
const currentPage = ref(1)
const perPage = 10

const showModal = ref(false)
const editingId = ref(null)
const imageFile = ref(null)
const imagePreview = ref(null)

const form = ref({})

/* COUNTRIES */
const countryList = Object.values(countriesData).map(c => ({ name: c.name }))

/* VALIDATION */
const schema = yup.object({
  name: yup.string().required(),
  email: yup.string().email().required(),
  country_code: yup.string().required(),
  phone: yup.string().required(),
  gender: yup.string().required(),
  country_name: yup.string().required(),
  password: yup.string().min(8).nullable(),
  is_enabled: yup.boolean(),
})

/* LOAD */
async function fetchVendors() {
  rows.value = await VendorsApi.list()
}

/* SEARCH + PAGINATION */
const filteredRows = computed(() => {
  const q = search.value.toLowerCase()
  return rows.value.filter(v =>
    v.name?.toLowerCase().includes(q) ||
    v.email?.toLowerCase().includes(q) ||
    v.phone?.includes(q)
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
  editingId.value = null
  form.value = {
    name: "",
    email: "",
    phone: "",
    country_code: "",
    gender: "",
    country_name: "",
    is_enabled: true,
    password: "",
  }
  imageFile.value = null
  imagePreview.value = null
  showModal.value = true
}

function openEdit(v) {
  editingId.value = v.id
  form.value = {
    name: v.name,
    email: v.email,
    phone: v.phone,
    country_code: v.country_code,
    gender: v.gender,
    country_name: v.country_name,
    is_enabled: !!v.is_enabled,
    password: "",
  }
  imagePreview.value = v.profile_image_url || null
  showModal.value = true
}

function closeModal() {
  showModal.value = false
}

function onFile(e) {
  const f = e.target.files?.[0] || null
  imageFile.value = f
  imagePreview.value = f ? URL.createObjectURL(f) : imagePreview.value
}

/* SAVE */
async function submit(values) {
  const payload = { ...values, profile_image: imageFile.value }

  if (editingId.value) {
    await VendorsApi.update(
      editingId.value,
      buildVendorUpdateFD(payload)
    )
  } else {
    await VendorsApi.create(
      buildVendorCreateFD(payload)
    )
  }

  closeModal()
  fetchVendors()
}

/* DELETE */
async function remove(v) {
  if (!confirm(`Delete "${v.name}"?`)) return
  await VendorsApi.remove(v.id)
  fetchVendors()
}

onMounted(fetchVendors)
</script>
<style scoped>
.form-input {
  @apply w-full border border-gray-300 rounded px-3 py-2;
}
.error {
  @apply text-xs text-red-600;
}
</style>
