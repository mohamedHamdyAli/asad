<template>

  <Head title="Unit Issues" />

  <AuthenticatedLayout>
    <div class="p-6 space-y-6">

      <h2 class="text-2xl font-semibold">Unit Issues</h2>

      <!-- SEARCH -->
      <input v-model="search" class="form-input w-72" placeholder="Search by title, unit or user" />

      <!-- TABLE -->
      <div class="bg-white rounded-xl shadow border overflow-hidden">
        <table class="w-full text-sm">
          <thead class="bg-gray-100">
            <tr>
              <th class="px-4 py-3 text-left">Unit</th>
              <th class="px-4 py-3 text-left">User</th>
              <th class="px-4 py-3 text-left">Title</th>
              <th class="px-4 py-3 text-left">Status</th>
              <th class="px-4 py-3 text-right">Actions</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="issue in paginatedIssues" :key="issue.id" class="border-t">
              <td class="px-4 py-3">
                {{ issue.unit?.name?.en ?? issue.unit_id }}
              </td>

              <td class="px-4 py-3">
                {{ issue.user?.name ?? issue.user_id }}
              </td>

              <td class="px-4 py-3">
                {{ issue.title }}
              </td>

              <td class="px-4 py-3">
                <span class="px-2 py-1 rounded text-xs" :class="issue.status === 'open'
                  ? 'bg-green-100 text-green-700'
                  : 'bg-gray-200 text-gray-700'">
                  {{ issue.status }}
                </span>
              </td>

              <td class="px-4 py-3 text-right space-x-2">
                <button class="text-blue-600" @click="openEdit(issue)">
                  Edit
                </button>
                <button class="text-red-600" @click="remove(issue.id)">
                  Delete
                </button>
              </td>
            </tr>

            <tr v-if="filteredIssues.length === 0">
              <td colspan="5" class="text-center py-6 text-gray-500">
                No issues found
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
      <div v-if="modalOpen" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 back-drop">
        <div class="bg-white w-full max-w-lg rounded-xl p-6">

          <Form :validation-schema="schema" :initial-values="form" @submit="submit">
            <div class="space-y-4">

              <!-- UNIT DETAILS -->
              <div v-if="selectedIssue?.unit" class="border rounded-lg p-3 bg-gray-50">

                <div class="flex gap-4 items-start">
                  <img v-if="selectedIssue.unit.cover_image" :src="`/${selectedIssue.unit.cover_image}`"
                    class="w-28 h-20 object-cover rounded border" />

                  <div class="space-y-1 text-sm">
                    <div class="font-semibold">
                      {{ selectedIssue.unit.name?.en }}
                    </div>

                    <div class="text-gray-500 text-xs">
                      {{ selectedIssue.unit.name?.ar }}
                    </div>

                    <div class="text-xs text-gray-600">
                      📍 {{ selectedIssue.unit.location }}
                    </div>

                    <div class="text-xs text-gray-500">
                      {{ selectedIssue.unit.start_date }} → {{ selectedIssue.unit.end_date }}
                    </div>
                  </div>
                </div>
              </div>

              <!-- ISSUE INFO -->
              <div class="text-sm">
                <div class="font-medium">Issue title</div>
                <div class="text-gray-600">
                  {{ selectedIssue?.title }}
                </div>
              </div>

              <div class="text-sm">
                <div class="font-medium">Description</div>
                <div class="text-gray-600 whitespace-pre-line">
                  {{ selectedIssue?.description }}
                </div>
              </div>

              <!-- STATUS -->
              <div>
                <label class="block text-xs text-gray-500 mb-1">Status</label>
                <Field name="status" as="select" class="form-input">
                  <option value="open">Open</option>
                  <option value="close">Close</option>
                </Field>
                <ErrorMessage name="status" class="error" />
              </div>

            </div>

            <div class="flex justify-end gap-2 mt-4">
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
import { ref, computed, onMounted, watch } from 'vue'
import { Head } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

import { UnitIssuesApi } from '@/Services/unitIssues'
import { UnitsApi } from '@/Services/units'
import { UsersApi } from '@/Services/users'
import { useServerError } from '@/composables/useServerError'
import { Form, Field, ErrorMessage } from 'vee-validate'
import * as yup from 'yup'

const { show } = useServerError()

/* ================= STATE ================= */
const selectedIssue = ref(null)

const issues = ref([])
const units = ref([])
const users = ref([])

const search = ref('')
const currentPage = ref(1)
const perPage = 10

const modalOpen = ref(false)
const currentId = ref(null)
const form = ref({})

/* ================= VALIDATION ================= */
const schema = yup.object({
  status: yup.string().oneOf(['open', 'close']).required(),
})

/* ================= LOAD ================= */
async function load() {
  const [issuesRes, unitsRes, usersRes] = await Promise.all([
    UnitIssuesApi.list(),
    // UnitsApi.list(),
    // UsersApi.list(),
  ])

  issues.value = issuesRes
  units.value = unitsRes
  users.value = usersRes
}

/* ================= FILTER ================= */
const filteredIssues = computed(() => {
  const q = search.value.toLowerCase()
  return issues.value.filter(i =>
    i.title?.toLowerCase().includes(q) ||
    i.unit?.name?.en?.toLowerCase().includes(q) ||
    i.user?.name?.toLowerCase().includes(q)
  )
})

watch(search, () => currentPage.value = 1)

/* ================= PAGINATION ================= */
const totalPages = computed(() =>
  Math.ceil(filteredIssues.value.length / perPage)
)

const paginatedIssues = computed(() => {
  const start = (currentPage.value - 1) * perPage
  return filteredIssues.value.slice(start, start + perPage)
})

/* ================= MODAL ================= */
function openEdit(issue) {
  currentId.value = issue.id
  selectedIssue.value = issue
  form.value = {
    // unit_id: issue.unit_id,
    // user_id: issue.user_id,
    title: issue.title,
    description: issue.description,
    status: issue.status,
  }
  modalOpen.value = true
}

function closeModal() {
  modalOpen.value = false
  form.value = {}
  selectedIssue.value = null
}

/* ================= SAVE ================= */

async function submit(values) {
  try {
    await UnitIssuesApi.update(currentId.value, {
      status: values.status,
    })

    closeModal()
    load()
  } catch (e) {
    show(e)
  }
}

/* ================= DELETE ================= */
async function remove(id) {
  if (!confirm('Delete this issue?')) return
  await UnitIssuesApi.remove(id)
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

.back-drop {
  margin-top: -25px !important;
}
</style>
