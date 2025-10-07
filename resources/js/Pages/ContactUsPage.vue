<template>
  <Head title="Contact Us Management" />
  <AuthenticatedLayout>
    <div class="p-6 space-y-6">
      <h2 class="text-2xl font-semibold text-dash-title">Contact Us Management</h2>

      <div v-if="loading" class="text-sm text-gray-500">Loading…</div>

      <div v-else class="space-y-6">
        <div
          v-for="item in contacts"
          :key="item.id"
          class="bg-white p-5 rounded-2xl shadow-sm border space-y-4"
        >
          <!-- Country -->
          <div>
            <label class="block text-xs text-gray-500 mb-1">Country</label>
            <select v-model="edit[item.id].country" class="form-input">
              <option value="">—</option>
              <option value="Egypt">Egypt</option>
              <option value="Kuwait">Kuwait</option>
            </select>
            <p v-if="err(item.id,'country')" class="text-red-600 text-xs">
              {{ err(item.id,'country') }}
            </p>
          </div>

          <!-- Telephone -->
          <div>
            <label class="block text-xs text-gray-500 mb-1">Telephone</label>
            <input v-model="edit[item.id].telephone" type="text" class="form-input" />
            <p v-if="err(item.id,'telephone')" class="text-red-600 text-xs">
              {{ err(item.id,'telephone') }}
            </p>
          </div>

          <!-- Bank Info -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs text-gray-500 mb-1">Account Name</label>
              <input v-model="edit[item.id].account_name" type="text" class="form-input" />
            </div>
            <div>
              <label class="block text-xs text-gray-500 mb-1">Bank Name</label>
              <input v-model="edit[item.id].bank_name" type="text" class="form-input" />
            </div>
            <div>
              <label class="block text-xs text-gray-500 mb-1">IBAN</label>
              <input v-model="edit[item.id].iban" type="text" class="form-input" />
            </div>
            <div>
              <label class="block text-xs text-gray-500 mb-1">Currency</label>
              <input v-model="edit[item.id].currency" type="text" class="form-input" />
            </div>
            <div>
              <label class="block text-xs text-gray-500 mb-1">Swift Code</label>
              <input v-model="edit[item.id].swift_code" type="text" class="form-input" />
            </div>
          </div>

          <!-- Location -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-xs text-gray-500 mb-1">Location</label>
              <input v-model="edit[item.id].location" type="text" class="form-input" />
            </div>
            <div>
              <label class="block text-xs text-gray-500 mb-1">Latitude</label>
              <input v-model.number="edit[item.id].lat" type="number" step="any" class="form-input" />
            </div>
            <div>
              <label class="block text-xs text-gray-500 mb-1">Longitude</label>
              <input v-model.number="edit[item.id].long" type="number" step="any" class="form-input" />
            </div>
          </div>

          <!-- Save button -->
          <div class="flex justify-end">
            <button
              class="px-4 py-2 bg-green-600 text-white rounded disabled:opacity-50"
              :disabled="saving[item.id]"
              @click="save(item.id)"
            >
              {{ saving[item.id] ? 'Saving…' : 'Save' }}
            </button>
          </div>

          <!-- Error Alert -->
          <div v-if="errorMsg[item.id]" class="text-sm text-red-600 mt-2">
            {{ errorMsg[item.id] }}
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, reactive, onMounted } from "vue"
import { Head } from "@inertiajs/vue3"
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import axios from "axios"

/* State */
const contacts = ref([])
const loading = ref(false)
const edit = reactive({})
const saving = reactive({})
const errorMsg = reactive({})
const fieldErrors = reactive({})

/* Fetch list */
async function fetchContacts() {
  loading.value = true
  try {
    const { data } = await axios.get("/api/contact-infos")
    contacts.value = data.data || []
    contacts.value.forEach((c) => {
      edit[c.id] = { ...c }
    })
  } finally {
    loading.value = false
  }
}

/* Save update */
async function save(id) {
  saving[id] = true
  errorMsg[id] = ""
  fieldErrors[id] = {}
  try {
    await axios.post(`/api/contact-infos/update/${id}`, edit[id])
  } catch (e) {
    if (e?.response?.status === 422) {
      fieldErrors[id] = e.response.data.errors
      errorMsg[id] = e.response.data.message || "Validation error"
    } else {
      errorMsg[id] = e?.response?.data?.message || e.message || "Save failed"
    }
  } finally {
    saving[id] = false
  }
}

/* Error helpers */
function err(id, field) {
  return fieldErrors[id]?.[field]?.[0] || ""
}

onMounted(fetchContacts)
</script>

<style scoped>
.form-input {
  @apply w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500;
}
</style>
