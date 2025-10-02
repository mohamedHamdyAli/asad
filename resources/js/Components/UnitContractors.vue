<template>
  <div class="space-y-6">
    <h3 class="text-lg font-semibold text-gray-800">Assigned Contractors</h3>

    <!-- Add Contractor -->
    <div class="flex gap-3 items-end">
      <div class="flex-1">
        <label class="block text-xs text-gray-500 mb-1">Select Contractor</label>
        <select v-model="newContractorId" class="form-input w-full">
          <option value="">-- choose contractor --</option>
          <option v-for="c in allContractors" :key="c.id" :value="c.id">
            {{ c.name }}
          </option>
        </select>
      </div>
      <button
        class="px-4 py-2 bg-green-600 text-white rounded disabled:opacity-50"
        :disabled="!newContractorId || loading"
        @click="addContractor"
      >
        {{ loading ? 'Adding…' : 'Assign' }}
      </button>
    </div>

    <!-- List -->
    <div class="bg-white rounded-2xl border shadow-sm">
      <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
          <tr>
            <th class="text-left px-4 py-2">Contractor</th>
            <th class="text-left px-4 py-2">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="c in contractors" :key="c.id" class="border-b">
            <td class="px-4 py-2">
              {{ c.contractor?.title.en || '—' }}
            </td>
            <td class="px-4 py-2 flex gap-2">
              <!-- Update contractor -->
              <select
                v-model="edit[c.id]"
                class="form-input text-sm"
                @change="updateContractor(c.id, edit[c.id])"
              >
                <option v-for="opt in allContractors" :key="opt.id" :value="opt.id">
                  {{ opt.title.en }}
                </option>
              </select>

              <!-- Delete -->
              <button
                class="px-3 py-1.5 rounded bg-red-50 text-red-700 border border-red-200 hover:bg-red-100"
                @click="removeContractor(c.id)"
              >
                Delete
              </button>
            </td>
          </tr>
          <tr v-if="!contractors.length">
            <td colspan="2" class="px-4 py-3 text-center text-gray-500">
              No contractors assigned yet.
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { UnitContractorsApi } from '@/Services/unitContractors'
import { ContractorsApi } from '@/Services/contractors' // assumes you have this

const props = defineProps({
  unitId: { type: [Number, String], required: true }
})

const contractors = ref([])
const allContractors = ref([])
const newContractorId = ref('')
const edit = reactive({})
const loading = ref(false)

async function fetchContractors() {
  contractors.value = await UnitContractorsApi.list(props.unitId)
  contractors.value.forEach(c => {
    edit[c.id] = c.contractor_id
  })
}

async function fetchAllContractors() {
  allContractors.value = await ContractorsApi.list()
}

async function addContractor() {
  if (!newContractorId.value) return
  loading.value = true
  try {
    await UnitContractorsApi.create(props.unitId, [{ contractor_id: newContractorId.value }])
    await fetchContractors()
    newContractorId.value = ''
  } finally {
    loading.value = false
  }
}

async function updateContractor(id, contractorId) {
  loading.value = true
  try {
    await UnitContractorsApi.update(props.unitId, [{ id, contractor_id: contractorId }])
    await fetchContractors()
  } finally {
    loading.value = false
  }
}

async function removeContractor(id) {
  if (!confirm('Remove this contractor?')) return
  await UnitContractorsApi.remove(id)
  await fetchContractors()
}

onMounted(async () => {
  await fetchAllContractors()
  await fetchContractors()
})
</script>

<style scoped>
.form-input {
  @apply border border-gray-300 rounded-md px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500;
}
</style>
