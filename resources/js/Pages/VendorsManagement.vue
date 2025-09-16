<template>
  <Head title="Vendors Management" />
  <AuthenticatedLayout>
    <div class="p-6 space-y-6">
      <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800">Project Managers (PM)</h2>
        <button
          @click="openCreate()"
          class="inline-flex items-center rounded-md bg-black px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
        >
          + Add PM
        </button>
      </div>

      <!-- List -->
      <div class="bg-white shadow rounded-lg p-4">
        <div class="flex items-center justify-between mb-3">
          <h3 class="text-lg font-bold">Project Managers</h3>
          <button @click="fetchVendors" class="px-3 py-1 border rounded">Refresh</button>
        </div>

        <div v-if="loading" class="text-sm text-gray-500">Loading…</div>

        <div v-else class="overflow-x-auto">
          <table class="w-full text-sm text-left text-gray-700">
            <thead class="text-xs uppercase bg-gray-50 text-gray-500">
              <tr>
                <th class="px-3 py-2">#</th>
                <th class="px-3 py-2">Avatar</th>
                <th class="px-3 py-2">Name</th>
                <th class="px-3 py-2">Email</th>
                <th class="px-3 py-2">Phone</th>
                <th class="px-3 py-2">Enabled</th>
                <th class="px-3 py-2 text-right">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="v in rows" :key="v.id" class="border-b">
                <td class="px-3 py-2">{{ v.id }}</td>
                <td class="px-3 py-2">
                  <div class="w-10 h-10 rounded-full overflow-hidden bg-gray-100 flex items-center justify-center">
                    <img v-if="v.profile_image_url" :src="v.profile_image_url" class="w-full h-full object-cover" />
                    <span v-else class="text-xs text-gray-400">—</span>
                  </div>
                </td>
                <td class="px-3 py-2 font-medium">{{ v.name }}</td>
                <td class="px-3 py-2">{{ v.email }}</td>
                <td class="px-3 py-2">{{ v.phone || '—' }}</td>
                <td class="px-3 py-2">
                  <span :class="v.is_enabled ? 'text-green-600' : 'text-gray-400'">
                    {{ v.is_enabled ? 'Yes' : 'No' }}
                  </span>
                </td>
                <td class="px-3 py-2 text-right space-x-2">
                  <button class="text-blue-600 hover:underline" @click="openEdit(v)">Edit</button>
                  <button class="text-red-600 hover:underline" @click="remove(v)">Delete</button>
                </td>
              </tr>
              <tr v-if="!rows.length">
                <td colspan="7" class="px-3 py-6 text-center text-gray-500">No vendors found.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Modal -->
      <div v-if="showModal" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl shadow-lg w-full max-w-2xl p-5 relative">
          <button @click="closeModal" class="absolute top-3 right-3 text-gray-400 hover:text-gray-600">✕</button>
          <h3 class="text-lg font-bold mb-4">{{ editingId ? 'Edit PM' : 'Add PM' }}</h3>

          <form @submit.prevent="submit">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="md:col-span-2">
                <label class="block text-xs text-gray-500 mb-1">Name</label>
                <input v-model.trim="form.name" class="form-input" type="text" required />
              </div>

              <div>
                <label class="block text-xs text-gray-500 mb-1">Email</label>
                <input v-model.trim="form.email" class="form-input" type="email" :required="!editingId" />
              </div>

                <div>
                <label class="block text-xs text-gray-500 mb-1">Country Code</label>
                <input v-model.trim="form.country_code" class="form-input" type="text" placeholder="+20" :required="!editingId" />
              </div>
              <div>
                <label class="block text-xs text-gray-500 mb-1">Phone</label>
                <input v-model.trim="form.phone" class="form-input" type="text" :required="!editingId" />
              </div>

              <div>
                <label class="block text-xs text-gray-500 mb-1">{{ editingId ? 'Password (optional)' : 'Password' }}</label>
                <input v-model="form.password" class="form-input" type="password" :required="!editingId" minlength="8" />
              </div>

              <div>
                <label class="block text-xs text-gray-500 mb-1">Gender</label>
                <select v-model="form.gender" class="form-input" :required="!editingId">
                  <option value="" disabled>Select gender</option>
                  <option value="male">male</option>
                  <option value="female">female</option>
                </select>
              </div>

              <div>
                <label class="block text-xs text-gray-500 mb-1">Country Name</label>
                <input v-model.trim="form.country_name" class="form-input" type="text" placeholder="Egypt" :required="!editingId" />
              </div>

              <div class="flex items-center gap-2">
                <input id="enabled" type="checkbox" v-model="form.is_enabled" />
                <label for="enabled" class="text-sm">Enabled</label>
              </div>

              <div class="md:col-span-2">
                <label class="block text-xs text-gray-500 mb-1">
                  Profile Image {{ editingId ? '(optional; select to replace)' : '(required)' }}
                </label>
                <input type="file" accept="image/*" @change="onFile" :required="!editingId" />
                <div v-if="imagePreview" class="mt-2">
                  <img :src="imagePreview" class="w-32 h-32 object-cover rounded border" />
                </div>
              </div>
            </div>

            <div class="mt-4 flex items-center gap-3">
              <button :disabled="saving" class="px-4 py-2 bg-indigo-600 text-white rounded disabled:opacity-60">
                {{ saving ? 'Saving…' : (editingId ? 'Update' : 'Create') }}
              </button>
              <button type="button" class="px-3 py-2 border rounded" @click="closeModal">Cancel</button>
            </div>

            <div v-if="errorMsg" class="text-sm text-red-600 mt-2">{{ errorMsg }}</div>
            <ul v-if="Object.keys(fieldErrors).length" class="mt-2 text-sm text-red-600 list-disc pl-6">
              <li v-for="(errs, key) in fieldErrors" :key="key">
                <strong>{{ key }}:</strong> {{ errs.join(', ') }}
              </li>
            </ul>
          </form>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { ref, onMounted } from 'vue'
import { VendorsApi, buildVendorCreateFD, buildVendorUpdateFD } from '@/Services/vendors'
import { Head } from '@inertiajs/vue3'

const rows = ref([])
const loading = ref(false)

const showModal = ref(false)
const editingId = ref(null)
const original = ref(null)

const form = ref({
  name: '',
  email: '',
  phone: '',
  password: '',
  gender: '',
  country_code: '',
  country_name: '',
  is_enabled: true,
  profile_image: null,
})
const imagePreview = ref(null)

const saving = ref(false)
const errorMsg = ref('')
const fieldErrors = ref({})

function resetForm() {
  form.value = {
    name: '',
    email: '',
    phone: '',
    password: '',
    gender: '',
    country_code: '',
    country_name: '',
    is_enabled: true,
    profile_image: null,
  }
  imagePreview.value = null
  errorMsg.value = ''
  fieldErrors.value = {}
  editingId.value = null
  original.value = null
}
function openCreate() { resetForm(); showModal.value = true }
function openEdit(vendor) {
  resetForm()
  editingId.value = vendor.id
  original.value = { ...vendor }
  Object.assign(form.value, {
    name: vendor.name,
    email: vendor.email,
    phone: vendor.phone,
    gender: vendor.gender || '',
    country_code: vendor.country_code || '',
    country_name: vendor.country_name || '',
    is_enabled: !!vendor.is_enabled,
  })
  imagePreview.value = vendor.profile_image_url || null
  showModal.value = true
}
function closeModal() { showModal.value = false }

function onFile(e) {
  const f = e.target.files?.[0] || null
  form.value.profile_image = f
  imagePreview.value = f ? URL.createObjectURL(f) : imagePreview.value
}

async function fetchVendors() {
  loading.value = true
  try {
    rows.value = await VendorsApi.list()
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
      const changed = {}
      const keys = ["name","email","phone","gender","country_code","country_name","is_enabled"]
      keys.forEach(k => {
        if ((original.value?.[k] ?? "") !== (form.value?.[k] ?? "")) changed[k] = form.value[k]
      })
      if (form.value.password && form.value.password.trim() !== "") changed.password = form.value.password
      if (form.value.profile_image) changed.profile_image = form.value.profile_image

      const fd = buildVendorUpdateFD(changed)
      await VendorsApi.update(editingId.value, fd)
    } else {
      const fd = buildVendorCreateFD(form.value)
      await VendorsApi.create(fd)
    }
    await fetchVendors()
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

async function remove(vendor) {
  if (!confirm(`Delete vendor "${vendor.name}"?`)) return
  await VendorsApi.remove(vendor.id)
  await fetchVendors()
}



onMounted(fetchVendors)
</script>

<style scoped>
.form-input {
  @apply w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500;
}
</style>
