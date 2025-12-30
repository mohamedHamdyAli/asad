<template>

  <Head title="PMs Management" />

  <AuthenticatedLayout>
    <div class="p-6 space-y-6">

      <!-- HEADER -->
      <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold">Project Managers (PM)</h2>
        <button class="px-4 py-2 bg-black text-white rounded" @click="openCreate">
          + Add PM
        </button>
      </div>

      <!-- SEARCH -->
      <input v-model="search" placeholder="Search name, email, or phone" class="form-input w-80" />

      <!-- TABLE -->
      <div class="bg-white rounded-xl shadow border overflow-hidden">
        <table class="w-full text-sm">
          <thead class="bg-gray-100 text-xs uppercase">
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
            <tr v-for="(v, index) in paginatedRows" :key="v.id" class="border-t">
              <td class="px-4 py-3">
                {{ (currentPage - 1) * perPage + index + 1 }}
              </td>

              <td class="px-4 py-3">
                <img v-if="v.profile_image_url" :src="v.profile_image_url"
                  class="w-10 h-10 rounded-full object-cover" />
                <span v-else class="text-gray-400">—</span>
              </td>

              <td class="px-4 py-3">{{ v.name }}</td>
              <td class="px-4 py-3">{{ v.email }}</td>
              <td class="px-4 py-3">{{ v.country_code }} {{ v.phone }}</td>

              <td class="px-4 py-3">
                <span :class="v.is_enabled ? 'text-green-600' : 'text-gray-400'">
                  {{ v.is_enabled ? 'Yes' : 'No' }}
                </span>
              </td>

              <td class="px-4 py-3 text-right space-x-2">
                <button class="text-blue-600" @click="openEdit(v)">Edit</button>
                <button class="text-red-600" @click="remove(v)">Delete</button>
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
      <div class="flex justify-between">
        <span class="text-sm">Page {{ currentPage }} of {{ totalPages }}</span>
        <span class="text-sm text-gray-500">
          Showing {{ paginatedRows.length }} of {{ filteredRows.length }}
        </span>

        <div class="space-x-2">
          <button @click="currentPage--" :disabled="currentPage === 1">Prev</button>
          <button @click="currentPage++" :disabled="currentPage === totalPages">Next</button>
        </div>
      </div>

      <!-- MODAL -->
      <Teleport to="body">
        <div v-if="showModal" class="fixed inset-0 bg-black/40 z-[9999] flex items-center justify-center">
          <div class="bg-white w-full max-w-2xl rounded-xl p-6 relative">

            <button class="absolute top-3 right-3 text-gray-400" @click="closeModal">✕</button>

            <h3 class="text-lg font-bold mb-4">
              {{ editingId ? 'Edit PM' : 'Add PM' }}
            </h3>

            <!-- SERVER ERROR -->
            <div v-if="serverError" class="mb-3 p-3 bg-red-50 text-red-700 rounded text-sm">
              {{ serverError }}
            </div>

            <Form :key="formKey" :validation-schema="schema" :initial-values="form" v-slot="{ setFieldValue }"
              @submit="submit">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <div class="md:col-span-2">
                  <Field name="name" class="form-input" placeholder="Name" />
                  <ErrorMessage name="name" class="error" />
                </div>

                <div>
                  <Field name="email" class="form-input" placeholder="Email" />
                  <ErrorMessage name="email" class="error" />
                </div>

                <!-- PHONE -->
                <div class="grid grid-cols-[100px_1fr] gap-2">
                  <div>
                    <Field as="select" name="country_code" class="form-input">
                      <option value="">Code</option>
                      <option value="+20">+20</option>
                      <option value="+965">+965</option>
                    </Field>
                    <ErrorMessage name="country_code" class="error" />
                  </div>
                  <div>
                    <Field name="phone" class="form-input" placeholder="Phone number" />
                    <ErrorMessage name="phone" class="error" />
                  </div>
                </div>

                <div>
                  <Field name="password" type="password" class="form-input"
                    :placeholder="editingId ? 'Password (optional)' : 'Password'" />
                  <ErrorMessage name="password" class="error" />
                </div>

                <div>
                  <Field as="select" name="gender" class="form-input">
                    <option value="">Select gender</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                  </Field>
                  <ErrorMessage name="gender" class="error" />
                </div>

                <div>
                  <Field as="select" name="country_name" class="form-input">
                    <option value="">Select country</option>
                    <option value="Egypt">Egypt</option>
                    <option value="Kuwait">Kuwait</option>
                  </Field>
                  <ErrorMessage name="country_name" class="error" />
                </div>

                <div class="flex items-center gap-2">
                  <Field type="checkbox" name="is_enabled" :value="true" />
                  <span class="text-sm">Enabled</span>
                </div>

                <!-- IMAGE -->
                <div class="md:col-span-2">
                  <input type="file" accept="image/*" @change="(e) => onFile(e, setFieldValue)" />
                  <ErrorMessage name="profile_image" class="error" />
                </div>

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
      </Teleport>

    </div>
  </AuthenticatedLayout>
</template>
<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import { Head } from "@inertiajs/vue3"
import { ref, onMounted, computed, watch } from "vue"
import { Form, Field, ErrorMessage } from "vee-validate"
import * as yup from "yup"
import { VendorsApi, buildVendorCreateFD, buildVendorUpdateFD } from "@/Services/vendors"

const rows = ref([])
const search = ref("")
const currentPage = ref(1)
const perPage = 10

const showModal = ref(false)
const editingId = ref(null)
const imageFile = ref(null)
const imagePreview = ref(null)
const serverError = ref("")
const formKey = ref(0)

const form = ref({})

const schema = yup.object({
  name: yup.string().required(),
  email: yup.string().email().required(),
  country_code: yup.string().required(),
  phone: yup.string().required(),
  gender: yup.string().required(),
  country_name: yup.string().required(),
  password: yup.string().min(8).nullable(),
  is_enabled: yup.boolean(),

  profile_image: yup
    .mixed()
    .nullable()
    .test("required", "Please select profile image", function (value) {
      if (editingId.value) return true
      return value instanceof File
    }),
})


async function fetchVendors() {
  rows.value = await VendorsApi.list()
}

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

const paginatedRows = computed(() =>
  filteredRows.value.slice((currentPage.value - 1) * perPage, currentPage.value * perPage)
)

function openCreate() {
  editingId.value = null
  serverError.value = ""
  form.value = {
    name: "",
    email: "",
    phone: "",
    country_code: "",
    gender: "",
    country_name: "",
    is_enabled: true,
    password: "",
    profile_image: null,
  }
  imageFile.value = null
  imagePreview.value = null
  formKey.value++
  showModal.value = true
}

function openEdit(v) {
  editingId.value = v.id
  serverError.value = ""
  form.value = {
    name: v.name,
    email: v.email,
    phone: v.phone,
    country_code: v.country_code,
    gender: v.gender,
    country_name: v.country_name,
    is_enabled: !!v.is_enabled,
    password: "",
    profile_image: null,
  }
  imagePreview.value = v.profile_image_url || null
  formKey.value++
  showModal.value = true
}

function closeModal() {
  showModal.value = false
}

function onFile(e, setFieldValue) {
  const file = e.target.files?.[0] || null

  imageFile.value = file
  imagePreview.value = file ? URL.createObjectURL(file) : null

  setFieldValue("profile_image", file)
}




async function submit(values) {
  serverError.value = "";

  try {
    const payload = {
      ...values,
      is_enabled: values.is_enabled ? 1 : 0,
      ...(imageFile.value ? { profile_image: imageFile.value } : {}),
    };

    let response;

    if (editingId.value) {
      response = await VendorsApi.update(
        editingId.value,
        buildVendorUpdateFD(payload)
      );
    } else {
      response = await VendorsApi.create(
        buildVendorCreateFD(payload)
      );
    }

    if (response?.code === 422 || response?.key === "Invalid data sent") {
      serverError.value = response.msg || "Validation error";
      return;
    }

    closeModal();
    fetchVendors();

  } catch (e) {
    console.log(serverError)
    serverError.value =
      e.response?.data?.msg ||
      e.response?.data?.message ||
      "Something went wrong. Please try again.";
  }
}



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
  @apply text-red-600 text-xs;
}

.back-drop {
  margin-top: -25px !important;
}
</style>