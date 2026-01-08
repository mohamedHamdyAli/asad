<template>
  <AuthenticatedLayout>
    <div class="p-6 space-y-6">

      <!-- HEADER -->
      <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold">Notifications</h2>
        <button class="px-4 py-2 bg-blue-600 text-white rounded" @click="openCreate">
          + Send Notification
        </button>
      </div>

      <!-- SEARCH -->
      <input v-model="search" placeholder="Search by title or user name" class="form-input w-72" />

      <!-- TABLE -->
      <div class="bg-white rounded-xl shadow border overflow-hidden">
        <table class="w-full text-sm">
          <thead class="bg-gray-100">
            <tr>
              <th class="px-4 py-3">ID</th>
              <th class="px-4 py-3">Users</th>
              <th class="px-4 py-3">Title</th>
              <th class="px-4 py-3">Body</th>
              <th class="px-4 py-3">Date</th>
              <th class="px-4 py-3 text-right">Actions</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="(n, index) in paginatedRows" :key="n.id" class="border-t">
              <td class="px-4 py-3">{{ (currentPage - 1) * perPage + index + 1 }}</td>
              <td class="px-4 py-3">
                <div v-if="n.users_count">
                  <button
                    @click="showUsers(n)"
                    class="text-blue-600 hover:underline flex items-center gap-1"
                  >
                    <span>{{ n.users_count }} user(s)</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                  </button>
                </div>
                <div v-else class="text-gray-400 italic">No users</div>
              </td>
              <td class="px-4 py-3 max-w-xs truncate">{{ n.title }}</td>
              <td class="px-4 py-3 max-w-md truncate">{{ n.body }}</td>
              <td class="px-4 py-3 text-xs text-gray-500">
                {{ formatDate(n.created_at) }}
              </td>
              <td class="px-4 py-3 text-right space-x-2">
                <button class="text-red-600" @click="remove(n.id)">Delete</button>
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
      <div v-if="modalOpen" class="fixed back-drop inset-0 bg-black/40 flex items-center justify-center z-50">
        <div class="bg-white w-full max-w-2xl rounded-xl p-6 max-h-[85vh] overflow-y-auto">
          <h3 class="text-lg font-semibold mb-4">Send Notification</h3>

          <Form ref="formRef" :validate-on-mount="false" :key="formKey" :validation-schema="schema"
            :initial-values="form" @submit="submit">

            <div class="space-y-4">

              <!-- Send to All or Specific User -->
              <div>
                <label class="flex items-center space-x-2 mb-2">
                  <input type="checkbox" v-model="sendToAll" @change="onSendToAllChange" class="form-checkbox" />
                  <span class="text-sm font-medium">Send to All Users</span>
                </label>
              </div>

              <!-- User Selection (if not sending to all) -->
              <div v-if="!sendToAll">
                <label class="block text-sm font-medium mb-2">Select Users</label>
                <div class="border border-gray-300 rounded px-3 py-2 max-h-[300px] overflow-y-auto">
                  <div class="space-y-2">
                    <label
                      v-for="user in users"
                      :key="user.id"
                      class="flex items-center space-x-2 cursor-pointer hover:bg-gray-50 p-2 rounded"
                    >
                      <input
                        type="checkbox"
                        :value="user.id"
                        v-model="selectedUsers"
                        class="form-checkbox h-4 w-4 text-blue-600"
                      />
                      <span class="text-sm">{{ user.name }} ({{ user.email }})</span>
                    </label>
                  </div>
                </div>
                <!-- Hidden Field for validation -->
                <Field name="user_ids" v-model="selectedUsers" type="hidden" />
                <ErrorMessage name="user_ids" class="error" />
                <div v-if="selectedUsers.length > 0" class="text-xs text-gray-600 mt-1">
                  {{ selectedUsers.length }} user(s) selected
                </div>
              </div>

              <!-- Title -->
              <div>
                <label class="block text-sm font-medium mb-1">Title</label>
                <Field name="title" class="form-input" placeholder="Notification Title" />
                <ErrorMessage name="title" class="error" />
              </div>

              <!-- Body -->
              <div>
                <label class="block text-sm font-medium mb-1">Message</label>
                <Field name="body" as="textarea" rows="5" class="form-input" placeholder="Notification Message" />
                <ErrorMessage name="body" class="error" />
              </div>

            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-2 mt-6">
              <button type="button" class="border px-4 py-2 rounded" @click="closeModal">
                Cancel
              </button>

              <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">
                Send
              </button>
            </div>
          </Form>
        </div>
      </div>

      <!-- USERS MODAL -->
      <div v-if="usersModalOpen" class="fixed back-drop inset-0 bg-black/40 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md mx-4 p-6">
          <h3 class="text-lg font-bold mb-4">
            Users who received this notification
            <span class="text-sm text-gray-500 font-normal">({{ selectedNotificationUsers.length }} total)</span>
          </h3>

          <div class="space-y-2 mb-4">
            <div
              v-for="user in paginatedModalUsers"
              :key="user.id"
              class="flex items-center gap-3 p-3 border rounded hover:bg-gray-50"
            >
              <div class="w-8 h-8 rounded-full bg-blue-500 text-white flex items-center justify-center font-bold">
                {{ user.name?.charAt(0).toUpperCase() }}
              </div>
              <div>
                <div class="font-medium">{{ user.name }}</div>
                <div class="text-xs text-gray-500">{{ user.email }}</div>
              </div>
            </div>
          </div>

          <!-- Pagination -->
          <div v-if="totalModalPages > 1" class="flex justify-between items-center mb-4 text-sm">
            <span class="text-gray-600">Page {{ modalCurrentPage }} of {{ totalModalPages }}</span>
            <div class="space-x-2">
              <button
                @click="modalCurrentPage--"
                :disabled="modalCurrentPage === 1"
                class="px-3 py-1 border rounded disabled:opacity-50 disabled:cursor-not-allowed"
              >
                Prev
              </button>
              <button
                @click="modalCurrentPage++"
                :disabled="modalCurrentPage === totalModalPages"
                class="px-3 py-1 border rounded disabled:opacity-50 disabled:cursor-not-allowed"
              >
                Next
              </button>
            </div>
          </div>

          <div class="flex justify-end">
            <button
              type="button"
              class="border px-4 py-2 rounded"
              @click="closeUsersModal"
            >
              Close
            </button>
          </div>
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
import { NotificationsApi, buildNotificationPayload } from '@/Services/notifications'
import { UsersApi } from '@/Services/users'
import { useServerError } from '@/composables/useServerError'

const { show } = useServerError()

const rows = ref([])
const users = ref([])
const search = ref('')
const currentPage = ref(1)
const perPage = 10

const modalOpen = ref(false)
const usersModalOpen = ref(false)
const sendToAll = ref(true)
const formKey = ref(0)
const selectedUsers = ref([])
const selectedNotificationUsers = ref([])
const modalCurrentPage = ref(1)
const modalPerPage = 5

const form = ref({})

const schema = computed(() => yup.object({
  title: yup.string().required('Title is required'),
  body: yup.string().required('Message body is required'),
  user_ids: sendToAll.value ? yup.array() : yup.array().min(1, 'Please select at least one user').required('Please select at least one user'),
}))

async function load() {
  rows.value = await NotificationsApi.list()
}

async function loadUsers() {
  users.value = await UsersApi.list()
}

const filteredRows = computed(() =>
  rows.value.filter(r =>
    r.title?.toLowerCase().includes(search.value.toLowerCase()) ||
    r.user_name?.toLowerCase().includes(search.value.toLowerCase()) ||
    r.body?.toLowerCase().includes(search.value.toLowerCase())
  )
)

const totalPages = computed(() =>
  Math.ceil(filteredRows.value.length / perPage)
)

const paginatedRows = computed(() =>
  filteredRows.value.slice((currentPage.value - 1) * perPage, currentPage.value * perPage)
)

// Modal pagination
const totalModalPages = computed(() =>
  Math.ceil(selectedNotificationUsers.value.length / modalPerPage)
)

const paginatedModalUsers = computed(() =>
  selectedNotificationUsers.value.slice(
    (modalCurrentPage.value - 1) * modalPerPage,
    modalCurrentPage.value * modalPerPage
  )
)

function openCreate() {
  sendToAll.value = true
  selectedUsers.value = []

  form.value = {
    title: '',
    body: '',
    user_ids: [],
  }

  formKey.value++
  modalOpen.value = true
}

function closeModal() {
  modalOpen.value = false
  selectedUsers.value = []
}

function onSendToAllChange() {
  if (sendToAll.value) {
    selectedUsers.value = []
    form.value.user_ids = []
  }
}

async function submit(values) {
  const payload = {
    title: values.title,
    body: values.body,
  }

  // If not sending to all, send to selected users
  if (!sendToAll.value && selectedUsers.value.length > 0) {
    payload.user_ids = selectedUsers.value.map(id => parseInt(id))
  }

  try {
    await NotificationsApi.create(buildNotificationPayload(payload))
    closeModal()
    load()
  } catch (e) {
    console.error('SEND ERROR', e.response?.data || e)
    show(e)
  }
}

async function remove(id) {
  if (!confirm('Delete notification?')) return
  await NotificationsApi.remove(id)
  load()
}

function showUsers(notification) {
  selectedNotificationUsers.value = notification.users || []
  modalCurrentPage.value = 1
  usersModalOpen.value = true
}

function closeUsersModal() {
  usersModalOpen.value = false
  modalCurrentPage.value = 1
  selectedNotificationUsers.value = []
}

function formatDate(dateString) {
  if (!dateString) return ''
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

onMounted(() => {
  load()
  loadUsers()
})
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
