<template>
  <AuthenticatedLayout>
    <div class="p-6 space-y-6">
      <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800">Unit Documents</h2>
        <a :href="backToUnits" class="px-3 py-1 border rounded">Back to Units</a>
      </div>

      <!-- Create (batch) -->
      <div class="bg-white p-4 rounded shadow">
        <h3 class="text-lg font-bold mb-3">Add Documents</h3>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-xs text-gray-500 mb-1">Folder ID *</label>
            <input v-model.number="createForm.folder_id" class="form-input" type="number" min="1" required />
          </div>
          <div>
            <label class="block text-xs text-gray-500 mb-1">Title (EN) *</label>
            <input v-model="createForm.title.en" class="form-input" type="text" required />
          </div>
          <div>
            <label class="block text-xs text-gray-500 mb-1">Title (AR) *</label>
            <input v-model="createForm.title.ar" class="form-input" type="text" required />
          </div>
        </div>

        <div class="mt-4">
          <input type="file" multiple accept="image/*,application/pdf" @change="onNewFiles" />
          <div v-if="newFiles.length" class="mt-3 text-sm text-gray-600">
            {{ newFiles.length }} file(s) selected
          </div>
        </div>

        <div class="mt-4 flex items-center gap-3">
          <button
            :disabled="creating || !canCreate"
            @click="createBatch"
            class="px-4 py-2 bg-green-600 text-white rounded disabled:opacity-60"
          >
            {{ creating ? 'Uploading…' : 'Upload' }}
          </button>
          <span v-if="createErr" class="text-red-600 text-sm">{{ createErr }}</span>
        </div>
      </div>

      <!-- List -->
      <div class="bg-white p-4 rounded shadow">
        <div class="flex items-center justify-between mb-3">
          <h3 class="text-lg font-bold">Documents</h3>
          <button @click="fetchList" class="px-3 py-1 border rounded">Refresh</button>
        </div>

        <div v-if="loading" class="text-sm text-gray-500">Loading…</div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
          <div v-for="d in rows" :key="d.id" class="rounded border bg-gray-50 overflow-hidden">
            <div class="aspect-[4/3] bg-white flex items-center justify-center">
              <template v-if="d.file_url && !d.is_pdf">
                <img :src="d.file_url" class="w-full h-full object-cover" />
              </template>
              <template v-else>
                <div class="text-gray-500 text-sm flex flex-col items-center">
                  <span class="text-4xl">📄</span>
                  <span class="mt-1">{{ d.file_name }}</span>
                </div>
              </template>
            </div>

            <div class="p-3 space-y-2 text-sm">
              <div class="flex items-center justify-between text-xs text-gray-500">
                <span>#{{ d.id }}</span>
                <span>Folder: {{ d.folder_id }}</span>
              </div>

              <div>
                <label class="block text-[11px] text-gray-500">Title (EN)</label>
                <input v-model="edit[d.id].title.en" class="form-input" type="text" />
              </div>
              <div>
                <label class="block text-[11px] text-gray-500">Title (AR)</label>
                <input v-model="edit[d.id].title.ar" class="form-input" type="text" />
              </div>

              <div>
                <label class="block text-[11px] text-gray-500">Folder ID *</label>
                <input v-model.number="edit[d.id].folder_id" class="form-input" type="number" min="1" />
              </div>

              <div class="text-[11px]">
                <label class="block text-gray-500">Replace file</label>
                <input type="file" accept="image/*,application/pdf" @change="onReplaceFile(d.id, $event)" />
              </div>

              <div class="flex gap-2 pt-1">
                <a v-if="d.file_url" :href="d.file_url" target="_blank" class="px-2 py-1 border rounded">Open</a>
                <button class="px-2 py-1 border rounded" @click="saveOne(d.id)" :disabled="saving[d.id]">
                  {{ saving[d.id] ? 'Saving…' : 'Save' }}
                </button>
                <button class="px-2 py-1 border rounded text-red-600" @click="remove(d.id)">Delete</button>
              </div>
            </div>
          </div>

          <div v-if="!rows.length" class="col-span-full text-center text-gray-500 py-8">
            No documents found.
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { ref, reactive, onMounted, computed } from 'vue'
import { UnitDocsApi, buildDocsCreateFD, buildDocsUpdateFD } from '@/Services/unitDocs'

const props = defineProps({
  unitId: { type: [Number, String], required: true },
})

const backToUnits = computed(() => '/units-management')

/* Create (batch) */
const createForm = ref({
  folder_id: null,
  title: { en: '', ar: '' },
})
const newFiles = ref([])
const creating = ref(false)
const createErr = ref('')

const canCreate = computed(() =>
  !!createForm.value.folder_id &&
  !!createForm.value.title.en?.trim() &&
  !!createForm.value.title.ar?.trim() &&
  newFiles.value.length > 0
)

function onNewFiles(e) {
  const files = Array.from(e.target.files || [])
  newFiles.value = files
}

async function createBatch() {
  creating.value = true
  createErr.value = ''
  try {
    const items = newFiles.value.map(f => ({
      folder_id: createForm.value.folder_id,
      title: { ...createForm.value.title },
      file: f,
    }))
    const fd = buildDocsCreateFD(props.unitId, items)
    await UnitDocsApi.create(fd)
    await fetchList()
    // reset
    newFiles.value = []
    createForm.value.title = { en: '', ar: '' }
  } catch (e) {
    console.error(e)
    createErr.value = e?.response?.data?.message || e?.message || 'Upload failed'
  } finally {
    creating.value = false
  }
}

/* List + per-card edit */
const rows = ref([])
const loading = ref(false)
const edit = reactive({})
const pendingFile = reactive({})
const saving = reactive({})

function seedEditState(arr) {
  arr.forEach(d => {
    edit[d.id] = {
      folder_id: d.folder_id ?? null,     // required on update
      title: { en: d.title_en || '', ar: d.title_ar || '' },
    }
  })
}

async function fetchList() {
  loading.value = true
  try {
    const data = await UnitDocsApi.list(props.unitId)
    rows.value = data
    seedEditState(data)
  } finally {
    loading.value = false
  }
}

function onReplaceFile(id, e) {
  const f = e.target.files?.[0] || null
  if (f) pendingFile[id] = f
}

async function saveOne(id) {
  const item = edit[id]
  if (!item) return
  saving[id] = true
  try {
    const items = [{
      id,
      folder_id: item.folder_id,   // required
      title: item.title,           // optional (rule requires shape if sent)
      file: pendingFile[id],       // optional
    }]
    const fd = buildDocsUpdateFD(props.unitId, items)
    await UnitDocsApi.update(fd)
    delete pendingFile[id]
    await fetchList()
  } catch (e) {
    console.error(e)
    alert(e?.response?.data?.message || 'Update failed')
  } finally {
    saving[id] = false
  }
}

async function remove(id) {
  if (!confirm('Delete this document?')) return
  await UnitDocsApi.remove(id)
  await fetchList()
}

onMounted(fetchList)
</script>

<style scoped>
.form-input { @apply w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500; }
.aspect-\[4\/3\] { aspect-ratio: 4 / 3; }
</style>
