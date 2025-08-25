<template>
  <AuthenticatedLayout>
    <div class="p-2">

      <!-- Title + Add Button -->
      <div class="flex items-center justify-between space-y-4">
        <h2 class="text-2xl font-bold text-gray-800">vendors Management</h2>
        <button @click="showModal = true"
          class="inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-gray-700 focus:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-gray-900">
          + Add New Vendor
        </button>
      </div>

      <!-- SPCs Table -->
      <div class="bg-white shadow rounded-lg p-2 overflow-x-auto mt-2">
        <table class="w-full text-sm text-left text-gray-600">
          <thead class="text-xs uppercase bg-gray-50 text-gray-500">
            <tr>
              <th class="px-4 py-2">Company Name</th>
              <th class="px-4 py-2">Admin Name</th>
              <th class="px-4 py-2">Admin Phone</th>
              <th class="px-4 py-2">Admin Email</th>
              <th class="px-4 py-2">Status</th>
              <th class="px-4 py-2 text-right">Actions</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="spc in rows" :key="spc.id" class="border-b hover:bg-gray-50 transition">
              <td class="px-4 py-2 font-medium">
                <Link :href="`/spc-main-admin/${spc.id}`"
                  class="text-indigo-600 hover:text-indigo-800 underline text-lg font-semibold">
                {{ spc.name }}
                </Link>
              </td>
              <td class="px-4 py-2">{{ spc.admin_name }}</td>
              <td class="px-4 py-2">{{ spc.admin_phone }}</td>
              <td class="px-4 py-2">{{ spc.admin_email }}</td>
              <td class="px-4 py-2">
                <span
                  :class="['px-2 py-1 rounded text-xs font-semibold', spc.status === 'Active' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600']">
                  {{ spc.status }}
                </span>
              </td>
              <td class="px-4 py-2 text-right space-x-2">
                <button @click="viewSPC(spc)" class="text-indigo-600 hover:text-indigo-800" title="View Details">
                  <Icon icon="mdi:eye-outline" class="w-5 h-5" />
                </button>
                <button @click="editSPC(spc)" class="text-blue-600 hover:text-blue-800" title="Edit">
                  <Icon icon="mdi:pencil-outline" class="w-5 h-5" />
                </button>
                <button @click="toggleSPCStatus(spc)" :title="spc.status === 'Active' ? 'Deactivate' : 'Activate'"
                  :class="spc.status === 'Active' ? 'text-yellow-600 hover:text-yellow-800' : 'text-green-600 hover:text-green-800'">
                  <Icon :icon="spc.status === 'Active' ? 'mdi:block-helper' : 'mdi:check-circle-outline'"
                    class="w-5 h-5" />
                </button>
                <button @click="deleteSPC(spc)" class="text-red-600 hover:text-red-800" title="Delete">
                  <Icon icon="mdi:trash-can-outline" class="w-5 h-5" />
                </button>
              </td>
            </tr>
          </tbody>
        </table>
        <!-- pagination -->
        <div class="mt-4 mb-2 flex justify-end items-center space-x-2 text-sm text-gray-600">
          <button @click="prevPage" :disabled="currentPage === 1"
            class="px-3 py-1 border rounded hover:bg-gray-100 disabled:opacity-50">Prev</button>
          <span>Page {{ currentPage }} of {{ totalPages }}</span>
          <button @click="nextPage" :disabled="currentPage === totalPages"
            class="px-3 py-1 border rounded hover:bg-gray-100 disabled:opacity-50">Next</button>
        </div>

      </div>

      <!-- Modal -->
      <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
        <div class="bg-white rounded-2xl shadow-lg w-full max-w-2xl p-2 relative">

          <!-- Close Button -->
          <button @click="showModal = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
            <Icon icon="mdi:close" class="w-6 h-6" />
          </button>

          <!-- Form Inside Modal -->
          <h3 class="text-xl font-bold text-gray-700 mt-2 mb-2 text-center">Register New Service Provider Company (SPC)
          </h3>
          <NewSPCForm @created="onCreated" />
        </div>
      </div>
      <!-- View Modal -->
      <ViewEntityModal :id="selectedId" :open="showView" @close="showView = false" />

      <!-- Edit Modal -->
      <EditEntityModal :open="showEdit" :spc="selectedRow" @close="showEdit = false" @updated="onUpdated" />
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Icon } from '@iconify/vue'
import { ref, onMounted } from 'vue'
import NewSPCForm from '@/Components/NewSPCForm.vue'
// import ViewSPCModal from '@/Components/SPC/ViewSPCModal.vue'
// import EditSPCModal from '@/Components/SPC/EditSPCModal.vue'
import ViewEntityModal from '@/Components/Entity/ViewEntityModal.vue'
import EditEntityModal from '@/Components/Entity/EditEntityModal.vue'
import { VendorsApi } from '@/Services/vendors'
import { Link } from '@inertiajs/vue3'

const showCreate = ref(false)
const showView = ref(false)
const showEdit = ref(false)

const showModal = ref(false)

const selectedId = ref(null)
const selectedRow = ref(null)

const rows = ref([])
const currentPage = ref(1)
const perPage = ref(15)
const totalPages = ref(1)
const totalItems = ref(0)
const search = ref("")
const loading = ref(false)

async function fetchList(page = 1) {
  loading.value = true
  try {
    const { data } = await VendorsApi.list({ page, per_page: perPage.value, search: search.value })
    rows.value = data.data
    currentPage.value = data.meta.current_page
    totalPages.value = data.meta.last_page
    totalItems.value = data.meta.total
  } finally {
    loading.value = false
  }
}
function nextPage() { if (currentPage.value < totalPages.value) fetchList(currentPage.value + 1) }
function prevPage() { if (currentPage.value > 1) fetchList(currentPage.value - 1) }

function viewSPC(spc) {
  selectedId.value = spc.id
  showView.value = true
}

function editSPC(spc) {
  selectedRow.value = { ...spc }
  showEdit.value = true
}

async function toggleSPCStatus(spc) {
  const original = spc.status
  try {
    await VendorsApi.toggle(spc.id)
    spc.status = original === 'Active' ? 'Inactive' : 'Active'
  } catch {
    spc.status = original
    alert('Could not toggle status.')
  }
}

async function deleteSPC(spc) {
  if (!confirm(`Are you sure you want to delete "${spc.name}"?`)) return
  await VendorsApi.remove(spc.id)
  if (rows.value.length === 1 && currentPage.value > 1) await fetchList(currentPage.value - 1)
  else await fetchList(currentPage.value)
}

function onCreated() { showModal.value = false; fetchList(currentPage.value) }
function onUpdated() { showEdit.value = false; fetchList(currentPage.value) }

onMounted(() => fetchList(1))
</script>
