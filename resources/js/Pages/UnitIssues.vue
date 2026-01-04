<template>
  <Head title="Unit Issues" />

  <AuthenticatedLayout>
    <div class="p-6 space-y-6">

      <h2 class="text-2xl font-semibold">Unit Issues</h2>

      <!-- SEARCH -->
      <input
        v-model="search"
        class="form-input w-72"
        placeholder="Search by title, unit or user"
      />

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
            <tr
              v-for="issue in paginatedIssues"
              :key="issue.id"
              class="border-t"
            >
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
                <span
                  class="px-2 py-1 rounded text-xs"
                  :class="issue.status === 'open'
                    ? 'bg-green-100 text-green-700'
                    : 'bg-gray-200 text-gray-700'"
                >
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
        class="fixed back-drop inset-0 bg-black/40 flex items-center justify-center z-50"
      >
        <div class="bg-white w-full max-w-lg rounded-xl p-6">

          <h3 class="text-lg font-semibold mb-4">Edit Issue</h3>

          <Form
            :validation-schema="schema"
            :initial-values="form"
            @submit="submit"
          >
            <div class="space-y-3">

              <!-- UNIT -->
              <!-- <Field name="unit_id" as="select" class="form-input">
                <option value="">Select Unit</option>
                <option
                  v-for="u in units"
                  :key="u.id"
                  :value="u.id"
                >
                  {{ u.name?.en ?? `Unit #${u.id}` }}
                </option>
              </Field>
              <ErrorMessage name="unit_id" class="error" /> -->

              <!-- USER -->
              <!-- <Field name="user_id" as="select" class="form-input">
                <option value="">Select User</option>
                <option
                  v-for="u in users"
                  :key="u.id"
                  :value="u.id"
                >
                  {{ u.name ?? `User #${u.id}` }}
                </option>
              </Field>
              <ErrorMessage name="user_id" class="error" />

              <Field name="title" class="form-input" placeholder="Title" />
              <ErrorMessage name="title" class="error" /> -->

              <Field
                name="description"
                as="textarea"
                rows="3"
                class="form-input"
                placeholder="Description"
                disabled
              />
              <ErrorMessage name="description" class="error" />

              <Field name="status" as="select" class="form-input">
                <option value="open">Open</option>
                <option value="close">Close</option>
              </Field>

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

import { Form, Field, ErrorMessage } from 'vee-validate'
import * as yup from 'yup'

/* ================= STATE ================= */
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
//   unit_id: yup.number().required(),
//   user_id: yup.number().required(),
//   title: yup.string().required(),
  description: yup.string().required(),
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
}

/* ================= SAVE ================= */
async function submit(values) {
  await UnitIssuesApi.update(currentId.value, {
    ...values,
    // unit_id: Number(values.unit_id),
    // user_id: Number(values.user_id),
  })

  closeModal()
  load()
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
