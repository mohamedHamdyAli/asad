<template>
  <AuthenticatedLayout>
    <div class="p-6 space-y-6">

      <!-- Header -->
      <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold">Users Management</h2>
        <button class="px-4 py-2 bg-black text-white rounded" @click="openCreate">
          + Add User
        </button>
      </div>

      <!-- Search -->
      <div class="flex gap-3">
        <input
          v-model="search"
          placeholder="Search by name / email / phone"
          class="form-input w-80"
        />
      </div>

      <!-- Table -->
      <div class="bg-white rounded shadow overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-gray-100">
            <tr>
              <th class="p-3 text-left">#</th>
              <th class="p-3 text-left">Name</th>
              <th class="px-4 py-3 text-center">Avatar</th>
              <th class="p-3 text-left">Email</th>
              <th class="p-3 text-left">Phone</th>
              <th class="p-3 text-center">Status</th>
              <th class="px-4 py-3 text-center">Actions</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="(u, i) in paginated" :key="u.id" class="border-t align-middle">
              <td class="p-3">{{ i + 1 + (page - 1) * perPage }}</td>
              <td class="p-3">{{ u.name }}</td>

              <!-- Avatar -->
              <td class="px-4 py-3 text-center">
                <img
                  v-if="u.profile_image_url"
                  :src="u.profile_image_url"
                  class="mx-auto w-9 h-9 rounded-full object-cover cursor-pointer
                         ring-1 ring-gray-300 hover:ring-2 hover:ring-black transition"
                  @click="openAvatar(u.profile_image_url)"
                />
                <div
                  v-else
                  class="mx-auto w-9 h-9 rounded-full bg-gray-200 flex items-center justify-center text-xs text-gray-500"
                >
                  —
                </div>
              </td>

              <td class="p-3">{{ u.email }}</td>
              <td class="p-3">{{ u.phone }}</td>

              <!-- Status -->
              <td class="p-3 text-center">
                <span
                  class="inline-flex px-2 py-0.5 text-xs rounded-full font-medium"
                  :class="u.is_enabled ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
                >
                  {{ u.is_enabled ? 'Enabled' : 'Disabled' }}
                </span>
              </td>

              <!-- Actions -->
              <td class="px-4 py-3">
                <div class="flex justify-center items-center gap-2">
                  <button class="btn-sm" @click="openEdit(u)">Edit</button>

                  <button
                    v-if="!u.is_enabled"
                    @click="toggleStatus(u, true)"
                    class="btn-sm enabled"
                  >
                    Enable
                  </button>

                  <button
                    v-else
                    @click="toggleStatus(u, false)"
                    class="btn-sm disabled"
                  >
                    Disable
                  </button>

                  <button class="btn-sm danger" @click="remove(u.id)">
                    Delete
                  </button>
                </div>
              </td>
            </tr>

            <tr v-if="!paginated.length">
              <td colspan="7" class="p-6 text-center text-gray-500">
                No users found
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="flex justify-end gap-2">
        <button class="btn-sm" :disabled="page === 1" @click="page--">Prev</button>
        <button class="btn-sm" :disabled="page * perPage >= filtered.length" @click="page++">Next</button>
      </div>

      <!-- MODAL -->
      <Teleport to="body">
        <div v-if="showModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
          <div class="bg-white rounded-xl w-full max-w-xl p-6 space-y-4">

            <h3 class="text-lg font-semibold">
              {{ editingId ? 'Edit User' : 'Create User' }}
            </h3>

            <!-- SERVER ERROR -->
            <div
              v-if="serverError"
              class="bg-red-50 border border-red-300 text-red-700 p-3 rounded text-sm"
            >
              {{ serverError }}
            </div>

            <Form
              :key="formKey"
              :validation-schema="schema"
              :initial-values="form"
              @submit="submit"
              v-slot="{ meta , submitCount, errors , values, setFieldValue }"
            >
              <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                  <Field name="name" class="form-input" placeholder="Name" />
                  <ErrorMessage name="name" class="error" />
                </div>

                <div class="col-span-2">
                  <Field name="email" class="form-input" placeholder="Email" />
                  <ErrorMessage name="email" class="error" />
                </div>

                <div class="col-span-2">
                  <Field name="phone" class="form-input" placeholder="Phone" />
                  <ErrorMessage name="phone" class="error" />
                </div>

                <div class="col-span-2" v-if="!editingId">
                  <Field name="password" type="password" class="form-input" placeholder="Password" />
                  <ErrorMessage name="password" class="error" />
                </div>

                <Field as="select" name="country_code" class="form-input">
                  <option value="+20">+20</option>
                  <option value="+965">+965</option>
                </Field>
                <Field as="select" name="country_name" class="form-input">
                  <option value="Egypt">Egypt</option>
                  <option value="Kuwait">Kuwait</option>
                </Field>

                <div class="col-span-2">
                  <Field as="select" name="gender" class="form-input">
                    <option value="">Gender</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                  </Field>
                  <ErrorMessage name="gender" class="error" />
                </div>

                <div class="col-span-2">
                  <Field name="profile_image" type="hidden" />
                <input type="file" accept="image/*" @change="e => onImage(e, setFieldValue)" />
<ErrorMessage name="profile_image" class="error" />

                </div>
              </div>

              <div class="flex justify-end gap-3 pt-4">
                <button type="button" class="btn-sm" @click="close">Cancel</button>
                <button type="submit" class="btn-sm primary" :disabled="!meta.valid">
                  Save
                </button>
              </div>
            </Form>
          </div>
        </div>
      </Teleport>

      <!-- Avatar Modal -->
      <Teleport to="body">
        <div
          v-if="avatarPreview"
          class="fixed inset-0 bg-black/60 z-[9999] flex items-center justify-center"
          @click.self="closeAvatar"
        >
          <div class="bg-white p-4 rounded-xl shadow-xl">
            <img :src="avatarPreview" class="max-w-[90vw] max-h-[80vh] rounded-lg" />
          </div>
        </div>
      </Teleport>

    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Form, Field, ErrorMessage } from 'vee-validate'
import * as yup from 'yup'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { UsersApi, buildUserCreateFD, buildUserUpdateFD } from '@/Services/users'
import { useServerError } from '@/composables/useServerError'

const { show } = useServerError()

const rows = ref([])
const page = ref(1)
const perPage = 8
const search = ref('')

const showModal = ref(false)
const editingId = ref(null)
const serverError = ref('')
const imageFile = ref(null)
const formKey = ref(0)

const form = ref({
  name: '',
  email: '',
  phone: '',
  password: '',
  country_code: '+20',
  country_name: 'Egypt',
  gender: 'male',
  is_enabled: true,
})

const avatarPreview = ref(null)
const isEdit = computed(() => !!editingId.value)

const schema = computed(() =>
  yup.object({
    name: yup.string().required('Name is required'),
    email: yup.string().email('Invalid email').required('Email is required'),
    phone: yup.string().required('Phone is required'),
    password: isEdit.value
      ? yup.string().nullable()
      : yup.string().required('Password is required').min(6)
          .matches(/[a-zA-Z]/, 'Must include letters')
          .matches(/\d/, 'Must include numbers'),
    country_code: yup.string().required('Country code is required'),
    country_name: yup.string().required('Country name is required'),
    gender: yup.string().required('Gender is required'),
    profile_image: isEdit.value ? yup.mixed().nullable() : yup.mixed().required('Image is required'),
  })
)

const filtered = computed(() => {
  const q = search.value.toLowerCase().trim()

  return rows.value
    .filter(u =>
      [u.name, u.email, u.phone].some(v =>
        (v || '').toLowerCase().includes(q)
      )
    )
    .sort((a, b) => {
      const da = new Date(a.created_at || a.createdAt || 0).getTime()
      const db = new Date(b.created_at || b.createdAt || 0).getTime()
      return db - da
    })
})


const paginated = computed(() =>
  filtered.value.slice((page.value - 1) * perPage, page.value * perPage)
)

async function fetchUsers() {
  rows.value = await UsersApi.list()
}

function openAvatar(url) {
  avatarPreview.value = url
}
function closeAvatar() {
  avatarPreview.value = null
}

function openCreate() {
  editingId.value = null
  imageFile.value = null
  serverError.value = ''

  form.value = {
    name: '',
    email: '',
    phone: '',
    password: '',
    country_code: '+20',
    country_name: 'Egypt',
    gender: 'male',
    is_enabled: true,
  }

  formKey.value++
  showModal.value = true
}

function openEdit(u) {
  editingId.value = u.id
  imageFile.value = null
  serverError.value = ''

  form.value = {
    name: u.name || '',
    email: u.email || '',
    phone: u.phone || '',
    password: '',
    country_code: ['+20', '+965'].includes(u.country_code) ? u.country_code : '+20',
    country_name: ['Egypt', 'Kuwait'].includes(u.country_name) ? u.country_name : 'Egypt',
    gender: u.gender || 'male',
    is_enabled: !!u.is_enabled,
  }

  formKey.value++
  showModal.value = true
}

function close() {
  showModal.value = false
}

function onImage(e, setFieldValue) {
  const file = e.target.files?.[0] || null
  imageFile.value = file

  setFieldValue('profile_image', file, true)
}



async function submit(values) {
  serverError.value = ''
  try {
    if (editingId.value) {
      const fd = buildUserUpdateFD({ ...values, profile_image: imageFile.value })
      await UsersApi.update(editingId.value, fd)
    } else {
      const fd = buildUserCreateFD({ ...values, profile_image: imageFile.value })
      await UsersApi.create(fd)
    }
    close()
    await fetchUsers()
  } catch (e) {
    show(e)
    serverError.value =
      Object.values(e?.response?.data?.errors || {})[0]?.[0] ||
      e?.response?.data?.msg ||
      e?.response?.data?.message ||
      'Operation failed'
  }
}

/** ✅ THIS WAS MISSING — enable/disable */
async function toggleStatus(u, enabled) {
  try {
    await UsersApi.toggleStatus(u.id, enabled)
    await fetchUsers()
  } catch (e) {
    console.error(e)
    alert('Failed to update status')
  }
}

async function remove(id) {
  if (!confirm('Delete this user?')) return
  await UsersApi.remove(id)
  await fetchUsers()
}

onMounted(fetchUsers)
</script>

<style scoped>
.form-input {
  @apply w-full border border-gray-300 rounded-md px-3 py-2;
}

.btn-sm {
  @apply px-3 py-1.5 border rounded text-sm;
}

.primary {
  @apply bg-black text-white;
}

.danger {
  @apply border-red-300 text-red-700 hover:bg-red-50;
}

.enabled {
  @apply border-green-300 text-green-700 hover:bg-green-50;
}

.disabled {
  @apply border-gray-300 text-gray-700 hover:bg-gray-50;
}

.error {
  @apply text-xs text-red-600 mt-1;
}
</style>
