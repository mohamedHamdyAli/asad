<template>
  <AuthenticatedLayout>
    <div class="p-6 space-y-6">
      <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-white">Unit Drawings</h2>
        <a :href="backToUnits" class="px-3 py-1 border rounded text-white">Back to Units</a>
      </div>

      <!-- Create (batch) -->
      <div class="bg-white p-4 rounded shadow">
        <h3 class="text-lg font-bold mb-3">Add Drawings</h3>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <FolderPicker type="drawing" label="Folder *" fileType="drawing" v-model="createForm.folder_id"
            :options="folders" :unitId="props.unitId" @created="folders.unshift($event)" @refresh="fetchFolders" />
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
          <input type="file" multiple @change="onNewFiles" />
          <div v-if="newPreviews.length" class="mt-3 flex flex-wrap gap-2">
            <a v-for="(src, i) in newPreviews" :key="i" :href="src" target="_blank" rel="noopener">
              <img :src="src" class="w-24 h-16 object-cover rounded border" />
            </a>
          </div>
        </div>

        <div class="mt-4 flex items-center gap-3">
          <button :disabled="creating || !canCreate" @click="createBatch"
            class="px-4 py-2 bg-green-600 text-white rounded disabled:opacity-60">
            {{ creating ? 'Uploading…' : 'Upload' }}
          </button>
          <span v-if="createErr" class="text-red-600 text-sm">{{ createErr }}</span>
        </div>
      </div>

      <!-- List -->
      <div class="bg-white p-5 rounded-2xl shadow-sm ring-1 ring-black/[0.05]">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-gray-800">Drawings</h3>
          <button @click="fetchList" class="px-3 py-1.5 text-sm border rounded-lg hover:bg-gray-50">
            Refresh
          </button>
        </div>

        <div v-if="loading" class="text-sm text-gray-500">Loading…</div>

        <div v-else class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-3 gap-5">
          <div v-for="g in rows" :key="g.id"
            class="group rounded-2xl overflow-hidden bg-white border border-gray-200/70 shadow-[0_1px_2px_rgba(0,0,0,0.04)] hover:shadow-[0_8px_24px_rgba(0,0,0,0.08)] hover:-translate-y-0.5 transition-all duration-200">
            <!-- File/Image header -->
            <div class="relative aspect-[4/3] bg-gray-50 flex items-center justify-center">
              <!-- Images -->
              <img v-if="isImage(g)" :src="fileUrl(g)" class="absolute inset-0 w-full h-full object-cover" />

              <!-- PDFs -->
              <div v-else-if="isPdf(g)"
                class="relative z-10 flex flex-col items-center justify-center text-gray-500 text-xs pointer-events-auto">
                <div class="text-4xl mb-2">📕</div>
                <span class="px-2">PDF Document</span>
                <a :href="fileUrl(g)" target="_blank" rel="noopener"
                  class="mt-2 px-2 py-1 border rounded text-xs hover:bg-gray-100">
                  Open
                </a>
              </div>

              <!-- Other -->
              <div v-else
                class="relative z-10 flex flex-col items-center justify-center text-gray-500 text-xs pointer-events-auto">
                <div class="text-4xl mb-2">📄</div>
                <span class="break-all px-2">{{ getFileName(g) }}</span>
                <a :href="fileUrl(g)" target="_blank" rel="noopener"
                  class="mt-2 px-2 py-1 border rounded text-xs hover:bg-gray-100">
                  Open
                </a>
              </div>

              <!-- gradient overlay -->
              <div
                class="pointer-events-none absolute inset-0 bg-gradient-to-b from-black/20 via-transparent to-transparent">
              </div>
            </div>


            <!-- Body -->
            <div class="p-3.5 space-y-3 text-sm">
              <!-- Date -->
              <div>
                <label class="block text-[11px] text-gray-500 mb-1">Date *</label>
                <input v-model="edit[g.id].date" class="form-input" type="date" />
              </div>

              <!-- Folder -->
              <FolderPicker type="drawing" :label="'Folder *'" v-model="edit[g.id].folder_id" :options="folders"
                @created="folders.unshift($event)" @refresh="fetchFolders" />

              <!-- Titles -->
              <div>
                <label class="block text-[11px] text-gray-500 mb-1">Title (EN)</label>
                <textarea v-model="edit[g.id].title.en" class="form-input !h-auto min-h-9" rows="1"
                  @input="autoGrow($event)"></textarea>
              </div>

              <div>
                <label class="block text-[11px] text-gray-500 mb-1">Title (AR)</label>
                <textarea v-model="edit[g.id].title.ar" class="form-input !h-auto min-h-9" rows="1" dir="rtl"
                  @input="autoGrow($event)"></textarea>
              </div>

              <!-- Replace image -->
              <div class="text-[11px]">
                <label class="block text-gray-500 mb-1">Replace File</label>
                <input type="file" @change="onReplaceFile(g.id, $event)" />
                <p v-if="pendingImage?.[g.id]" class="mt-1 text-[11px] text-gray-500">
                  Selected: {{ pendingImage[g.id]?.name }}
                </p>
              </div>

              <!-- Actions -->
              <div class="flex items-center gap-2 pt-2">
                <button
                  class="px-3 py-1.5 text-sm rounded-lg border border-gray-300 bg-white hover:bg-gray-50 disabled:opacity-50"
                  @click="saveOne(g.id)" :disabled="saving[g.id]">
                  {{ saving[g.id] ? 'Saving…' : 'Save' }}
                </button>
                <button
                  class="px-3 py-1.5 text-sm rounded-lg border border-red-200 text-red-700 bg-red-50 hover:bg-red-100"
                  @click="remove(g.id)">
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
  try {
    folders.value = await FolderApi.list('drawing', props.unitId)
  } finally {
    folderLoading.value = false
  }
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

function fileUrl(file) {
  if (!file) return ''
  return `/storage/${file.image}`
}

function isImage(file) {
  if (!file?.image) return false
  return /\.(jpe?g|png|gif|webp|svg|bmp|tiff?|heic|heif|avif)$/i.test(file.image)
}

function isPdf(file) {
  if (!file?.image) return false
  return /\.pdf$/i.test(file.image)
}

function getFileName(file) {
  if (!file?.image) return 'Unknown file'
  return decodeURIComponent(file.image.split('/').pop())
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
      folder_id: g.folder_id ?? null,
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
function autoGrow(e) {
  const el = e?.target
  if (!el) return
  el.style.height = 'auto'
  el.style.height = `${el.scrollHeight}px`
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
      folder_id: item.folder_id,
      date: item.date,
      title: item.title,
      image: pendingImage[id],
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
.form-input {
  @apply w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500;
}

.aspect-\[4\/3\] {
  aspect-ratio: 4 / 3;
}
</style>
