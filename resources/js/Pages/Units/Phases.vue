<template>
  <AuthenticatedLayout>
    <div class="p-6 space-y-8">

      <!-- Header -->
      <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800">Unit Phases</h2>
        <a :href="backToUnits" class="px-3 py-1 border rounded text-gray-700 hover:bg-gray-100">
          ← Back to Units
        </a>
      </div>

      <!-- Create / Upsert Phase -->
      <div class="bg-white p-6 rounded-2xl shadow space-y-4">
        <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
          Add / Upsert Phase
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div>
            <label class="block text-xs text-gray-500 mb-1">Status *</label>
            <select v-model="newPhase.status" class="form-input">
              <option value="" disabled>Select status</option>
              <option v-for="s in STATUSES" :key="s.value" :value="s.value">
                {{ s.label }}
              </option>
            </select>
          </div>
          <div>
            <label class="block text-xs text-gray-500 mb-1">Description (EN) *</label>
            <textarea v-model="newPhase.description.en" class="form-input"></textarea>
          </div>
          <div>
            <label class="block text-xs text-gray-500 mb-1">Description (AR) *</label>
            <textarea v-model="newPhase.description.ar" class="form-input" dir="rtl"></textarea>
          </div>
          <div class="flex items-end">
            <button
              class="px-4 py-2 bg-black text-white rounded hover:bg-gray-700"
              :disabled="creating || !canCreate"
              @click="createOrUpsert"
            >
              {{ creating ? 'Saving…' : 'Save Phase' }}
            </button>
          </div>
        </div>
        <div v-if="createErr" class="text-sm text-red-600">{{ createErr }}</div>
      </div>

      <!-- Existing Phases -->
      <div class="bg-white p-6 rounded-2xl shadow">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-gray-800">Existing Phases</h3>
          <button @click="fetchList" class="px-3 py-1.5 text-sm border rounded-lg hover:bg-gray-50">
            Refresh
          </button>
        </div>

        <div v-if="loading" class="text-sm text-gray-500">Loading…</div>

        <div v-else>
          <div v-if="!rows.length" class="text-center text-gray-500 py-10">
            No phases found.
          </div>

          <div v-else class="space-y-5">
            <div
              v-for="(p, i) in rows"
              :key="p.id"
              class="rounded-xl p-5 border shadow-sm hover:shadow-md transition bg-white"
              :class="i === 0 ? 'border-yellow-400' : 'border-gray-200'"
            >
              <!-- Header -->
              <div class="flex items-center justify-center mb-4">

                     <span
                  class="inline-flex items-center justify-center rounded-full bg-blue-50 text-blue-700 px-2.5 py-0.5 text-[13px] border border-blue-200">
                  {{ p.status_label }}
                </span>

              </div>

              <!-- Editable Fields -->
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div>
                  <label class="block text-[11px] text-gray-500 mb-1">Status *</label>
                  <select v-model="edit[p.id].status" class="form-input">
                    <option v-for="s in STATUSES" :key="s.value" :value="s.value">
                      {{ s.label }}
                    </option>
                  </select>
                </div>
                <div>
                  <label class="block text-[11px] text-gray-500 mb-1">Description (EN)</label>
                  <textarea
                    v-model="edit[p.id].description.en"
                    class="form-input min-h-[60px]"
                  ></textarea>
                </div>
                <div>
                  <label class="block text-[11px] text-gray-500 mb-1">Description (AR)</label>
                  <textarea
                    v-model="edit[p.id].description.ar"
                    class="form-input min-h-[60px]"
                    dir="rtl"
                  ></textarea>
                </div>
              </div>

              <!-- Actions -->
              <div class="mt-4 flex justify-end">
                <button
                  class="px-4 py-2 rounded-lg border border-gray-300 bg-white hover:bg-gray-50 disabled:opacity-60"
                  :disabled="saving[p.id]"
                  @click="saveOne(p.id)"
                >
                  {{ saving[p.id] ? 'Saving…' : 'Save Changes' }}
                </button>

                                <button
                  class="px-3 py-1.5 text-sm rounded-lg border border-red-200 text-red-600 bg-red-50 hover:bg-red-100 ml-3"
                  @click="remove(p.id)"
                >
                  Delete
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

const props = defineProps({ unitId: { type: [Number, String], required: true } })
const backToUnits = computed(() => '/units-management')

const STATUSES = UnitPhasesApi.statuses()

/* Create */
const newPhase = ref({ status: '', description: { en: '', ar: '' } })
const creating = ref(false)
const createErr = ref('')
const canCreate = computed(() =>
  !!newPhase.value.status && !!newPhase.value.description.en?.trim() && !!newPhase.value.description.ar?.trim()
)

async function createOrUpsert() {
  creating.value = true
  try {
    const payload = buildCreatePayload(props.unitId, [newPhase.value])
    await UnitPhasesApi.create(payload)
    await fetchList()
    newPhase.value = { status: '', description: { en: '', ar: '' } }
  } catch (e) {
    createErr.value = e?.response?.data?.message || 'Save failed'
  } finally {
    creating.value = false
  }
}

/* List + Edit */
const rows = ref([])
const edit = reactive({})
const saving = reactive({})
const loading = ref(false)

function seedEditState(arr) {
  Object.keys(edit).forEach(k => delete edit[k])
  arr.forEach(p => {
    edit[p.id] = {
      status: p.status,
      description: { en: p.desc_en || '', ar: p.desc_ar || '' }
    }
  })
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
    const payload = buildUpdatePayload(props.unitId, [{ id, status: item.status, description: item.description }])
    await UnitPhasesApi.update(payload)
    await fetchList()
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
.form-input {
  @apply w-full border border-gray-300 rounded-md px-3 py-2 text-sm
         focus:outline-none focus:ring-2 focus:ring-yellow-400;
}
</style>
