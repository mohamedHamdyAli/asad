<template>
  <AuthenticatedLayout>
    <div class="p-6 space-y-6">
      <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800">Unit Phases</h2>
        <a :href="backToUnits" class="px-3 py-1 border rounded">Back to Units</a>
      </div>

      <!-- Create/Upsert (by status) -->
      <div class="bg-white p-4 rounded shadow">
        <h3 class="text-lg font-bold mb-3">Add / Upsert Phase</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div class="md:col-span-1">
            <label class="block text-xs text-gray-500 mb-1">Status *</label>
            <select v-model="newPhase.status" class="form-input">
              <option value="" disabled>Select status</option>
              <option v-for="s in STATUSES" :key="s.value" :value="s.value">{{ s.label }}</option>
            </select>
          </div>
          <div>
            <label class="block text-xs text-gray-500 mb-1">Description (EN) *</label>
            <input v-model="newPhase.description.en" type="text" class="form-input" />
          </div>
          <div>
            <label class="block text-xs text-gray-500 mb-1">Description (AR) *</label>
            <input v-model="newPhase.description.ar" type="text" class="form-input" />
          </div>
          <div class="flex items-end">
            <button
              class="px-4 py-2 bg-green-600 text-white rounded disabled:opacity-60"
              :disabled="creating || !canCreate"
              @click="createOrUpsert"
            >
              {{ creating ? 'Saving…' : 'Save Phase' }}
            </button>
          </div>
        </div>
        <div v-if="createErr" class="mt-2 text-sm text-red-600">{{ createErr }}</div>
      </div>

      <!-- List + per-row edit -->
      <div class="bg-white p-4 rounded shadow">
        <div class="flex items-center justify-between mb-3">
          <h3 class="text-lg font-bold">Existing Phases</h3>
          <button @click="fetchList" class="px-3 py-1 border rounded">Refresh</button>
        </div>

        <div v-if="loading" class="text-sm text-gray-500">Loading…</div>

        <div v-else>
          <div v-if="!rows.length" class="text-center text-gray-500 py-8">No phases found.</div>

          <div v-else class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div v-for="p in rows" :key="p.id" class="border rounded p-3 bg-gray-50">
              <div class="flex items-center justify-between mb-2">
                <div class="font-semibold">#{{ p.id }} — {{ p.status_label }}</div>
                <button class="px-2 py-1 border rounded text-red-600" @click="remove(p.id)">Delete</button>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
                <div>
                  <label class="block text-[11px] text-gray-500 mb-1">Status *</label>
                  <select v-model="edit[p.id].status" class="form-input">
                    <option v-for="s in STATUSES" :key="s.value" :value="s.value">{{ s.label }}</option>
                  </select>
                </div>
                <div>
                  <label class="block text-[11px] text-gray-500 mb-1">Description (EN)</label>
                  <input v-model="edit[p.id].description.en" class="form-input" type="text" />
                </div>
                <div>
                  <label class="block text-[11px] text-gray-500 mb-1">Description (AR)</label>
                  <input v-model="edit[p.id].description.ar" class="form-input" type="text" />
                </div>
              </div>

              <div class="mt-3">
                <button class="px-3 py-1 border rounded" :disabled="saving[p.id]" @click="saveOne(p.id)">
                  {{ saving[p.id] ? 'Saving…' : 'Save' }}
                </button>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { ref, reactive, onMounted, computed } from 'vue'
import { UnitPhasesApi, buildCreatePayload, buildUpdatePayload } from '@/Services/unitPhases'

const props = defineProps({
  unitId: { type: [Number, String], required: true },
})

const backToUnits = computed(() => '/units-management')

const STATUSES = UnitPhasesApi.statuses()

/* Create / Upsert */
const newPhase = ref({
  status: '',
  description: { en: '', ar: '' },
})
const creating = ref(false)
const createErr = ref('')
const canCreate = computed(() =>
  !!newPhase.value.status &&
  !!newPhase.value.description.en?.trim() &&
  !!newPhase.value.description.ar?.trim()
)

async function createOrUpsert() {
  creating.value = true
  createErr.value = ''
  try {
    const payload = buildCreatePayload(props.unitId, [newPhase.value])
    await UnitPhasesApi.create(payload)
    await fetchList()
    newPhase.value = { status: '', description: { en: '', ar: '' } }
  } catch (e) {
    console.error(e)
    createErr.value = e?.response?.data?.message || e?.message || 'Save failed'
  } finally {
    creating.value = false
  }
}

/* List + edit */
const rows = ref([])
const loading = ref(false)
const edit = reactive({})
const saving = reactive({})

function seedEditState(arr) {
  editClear()
  arr.forEach(p => {
    edit[p.id] = {
      status: p.status,
      description: { en: p.desc_en || '', ar: p.desc_ar || '' },
    }
  })
}
function editClear() {
  Object.keys(edit).forEach(k => delete edit[k])
}

async function fetchList() {
  loading.value = true
  try {
    const data = await UnitPhasesApi.list(props.unitId)
    rows.value = data
    seedEditState(data)
  } finally {
    loading.value = false
  }
}

async function saveOne(id) {
  const item = edit[id]
  if (!item) return
  saving[id] = true
  try {
    const payload = buildUpdatePayload(props.unitId, [
      { id, status: item.status, description: item.description }
    ])
    await UnitPhasesApi.update(payload)
    await fetchList()
  } catch (e) {
    console.error(e)
    alert(e?.response?.data?.message || 'Update failed')
  } finally {
    saving[id] = false
  }
}

async function remove(id) {
  if (!confirm('Delete this phase?')) return
  await UnitPhasesApi.remove(id)
  await fetchList()
}

onMounted(fetchList)
</script>

<style scoped>
.form-input { @apply w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500; }
</style>
