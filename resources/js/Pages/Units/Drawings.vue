<template>
  <AuthenticatedLayout>
    <div class="p-6 space-y-6">
      <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800">Unit Drawings</h2>
        <a :href="backToUnits" class="px-3 py-1 border rounded">Back to Units</a>
      </div>

      <!-- Create (batch) -->
      <div class="bg-white p-4 rounded shadow">
        <h3 class="text-lg font-bold mb-3">Add Drawings</h3>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
    <FolderPicker
    type="drawing"
    label="Folder *"
    v-model="createForm.folder_id"
    :options="folders"
    @created="folders.unshift($event)"
    @refresh="fetchFolders"
  />
          <div>
            <label class="block text-xs text-gray-500 mb-1">Date *</label>
            <input v-model="createForm.date" class="form-input" type="date" required />
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
          <input type="file" multiple accept="image/*" @change="onNewFiles" />
          <div v-if="newPreviews.length" class="mt-3 flex flex-wrap gap-2">
            <img v-for="(src, i) in newPreviews" :key="i" :src="src" class="w-24 h-16 object-cover rounded border" />
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
          <h3 class="text-lg font-bold">Drawings</h3>
          <button @click="fetchList" class="px-3 py-1 border rounded">Refresh</button>
        </div>

        <div v-if="loading" class="text-sm text-gray-500">Loading…</div>

        <div v-else class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">
          <div v-for="g in rows" :key="g.id" class="rounded border overflow-hidden bg-gray-50">
            <div class="aspect-[4/3] bg-white flex items-center justify-center">
              <img v-if="g.image_url" :src="g.image_url" class="w-full h-full object-cover" />
              <div v-else class="text-gray-400 text-sm">No Image</div>
            </div>

            <div class="p-2 space-y-2 text-xs">
              <div class="flex items-center justify-between">
                <span>#{{ g.id }}</span>
                <span class="text-gray-500">Folder: {{ g.folder_id }}</span>
              </div>

              <div>
                <label class="block text-[11px] text-gray-500">Date *</label>
                <input v-model="edit[g.id].date" class="form-input" type="date" />
              </div>

         <FolderPicker
    type="drawing"
    :label="'Folder *'"
    v-model="edit[g.id].folder_id"
    :options="folders"
    @created="folders.unshift($event)"
    @refresh="fetchFolders"
  />


              <div>
                <label class="block text-[11px] text-gray-500">Title (EN)</label>
                <input v-model="edit[g.id].title.en" class="form-input" type="text" />
              </div>
              <div>
                <label class="block text-[11px] text-gray-500">Title (AR)</label>
                <input v-model="edit[g.id].title.ar" class="form-input" type="text" />
              </div>

              <div class="text-[11px]">
                <label class="block text-gray-500">Replace Image</label>
                <input type="file" accept="image/*" @change="onReplaceFile(g.id, $event)" />
              </div>

              <div class="flex gap-2 pt-1">
                <button class="px-2 py-1 border rounded" @click="saveOne(g.id)" :disabled="saving[g.id]">
                  {{ saving[g.id] ? 'Saving…' : 'Save' }}
                </button>
                <button class="px-2 py-1 border rounded text-red-600" @click="remove(g.id)">
                  Delete
                </button>
              </div>
            </div>
          </div>

          <div v-if="!rows.length" class="col-span-full text-center text-gray-500 py-8">
            No drawings found.
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { ref, reactive, onMounted, computed } from 'vue'
import { UnitDrawingsApi, buildDrawingsCreateFD, buildDrawingsUpdateFD } from '@/Services/unitDrawings'
  import FolderPicker from '@/Components/FolderPicker.vue'
  import { FolderApi } from '@/Services/folders'
  const folders = ref([])
  const folderLoading = ref(false)
  async function fetchFolders() {
    folderLoading.value = true
    try { folders.value = await FolderApi.list('drawing') }
    finally { folderLoading.value = false }
  }


const props = defineProps({
  unitId: { type: [Number, String], required: true },
})

const backToUnits = computed(() => '/units-management')

/* Create (batch) */
const createForm = ref({
  folder_id: null,
  date: '',
  title: { en: '', ar: '' },
})
const newFiles = ref([])
const newPreviews = ref([])
const creating = ref(false)
const createErr = ref('')

const canCreate = computed(() =>
  !!createForm.value.folder_id &&
  !!createForm.value.date &&
  !!createForm.value.title.en?.trim() &&
  !!createForm.value.title.ar?.trim() &&
  newFiles.value.length > 0
)

function onNewFiles(e) {
  const files = Array.from(e.target.files || [])
  newFiles.value = files
  newPreviews.value = files.map(f => URL.createObjectURL(f))
}

async function createBatch() {
  creating.value = true
  createErr.value = ''
  try {
    const items = newFiles.value.map(f => ({
      folder_id: createForm.value.folder_id,
      date: createForm.value.date,
      title: { ...createForm.value.title },
      image: f,
    }))
    const fd = buildDrawingsCreateFD(props.unitId, items)
    await UnitDrawingsApi.create(fd)
    await fetchList()
    // reset
    newFiles.value = []
    newPreviews.value = []
    createForm.value.title = { en: '', ar: '' }
  } catch (e) {
    console.error(e)
    createErr.value = e?.response?.data?.message || e?.message || 'Upload failed'
  } finally {
    creating.value = false
  }
}

/* List   edit per-card */
const rows = ref([])
const loading = ref(false)

// per-id edit state
const edit = reactive({})
const pendingImage = reactive({})
const saving = reactive({})

function seedEditState(arr) {
  arr.forEach(g => {
    edit[g.id] = {
      date: g.date || '',
      folder_id: g.folder_id ?? null, // required on update
      title: { en: g.title_en || '', ar: g.title_ar || '' },
    }
  })
}

async function fetchList() {
  loading.value = true
  try {
    const data = await UnitDrawingsApi.list(props.unitId)
    rows.value = data
    seedEditState(data)
  } finally {
    loading.value = false
  }
}

function onReplaceFile(id, e) {
  const f = e.target.files?.[0] || null
  if (f) pendingImage[id] = f
}

async function saveOne(id) {
  const item = edit[id]
  if (!item) return
  saving[id] = true
  try {
    const items = [{
      id,
      folder_id: item.folder_id,   // required
      date: item.date,             // optional (rule allows nullable)
      title: item.title,           // optional (if sent, both fields validated)
      image: pendingImage[id],     // optional
    }]
    const fd = buildDrawingsUpdateFD(props.unitId, items)
    await UnitDrawingsApi.update(fd)
    delete pendingImage[id]
    await fetchList()
  } catch (e) {
    console.error(e)
    alert(e?.response?.data?.message || 'Update failed')
  } finally {
    saving[id] = false
  }
}

async function remove(id) {
  if (!confirm('Delete this drawing?')) return
  await UnitDrawingsApi.remove(id)
  await fetchList()
}

onMounted(async () => { await fetchFolders(); await fetchList(); })

</script>

<style scoped>
.form-input { @apply w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500; }
.aspect-\[4\/3\] { aspect-ratio: 4 / 3; }
</style>
