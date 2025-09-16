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
  <FolderPicker
    type="document"
    label="Folder *"
    fileType="document"
    v-model="createForm.folder_id"
    :options="folders"
    @created="folders.unshift($event)"
    @refresh="fetchFolders"
  />
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
    <div class="bg-white p-5 rounded-2xl shadow-sm ring-1 ring-black/[0.05]">
  <div class="flex items-center justify-between mb-4">
    <h3 class="text-lg font-semibold text-gray-800">Documents</h3>
    <button
      @click="fetchList"
      class="px-3 py-1.5 text-sm border rounded-lg hover:bg-gray-50"
    >
      Refresh
    </button>
  </div>

  <div v-if="loading" class="text-sm text-gray-500">Loading…</div>

  <div
    v-else
    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5"
  >
    <div
      v-for="d in rows"
      :key="d.id"
      class="group rounded-2xl overflow-hidden bg-white border border-gray-200/70 shadow-[0_1px_2px_rgba(0,0,0,0.04)] hover:shadow-[0_8px_24px_rgba(0,0,0,0.08)] hover:-translate-y-0.5 transition-all duration-200"
    >
      <!-- File header -->
      <div class="relative aspect-[4/3] bg-gray-50 flex items-center justify-center">
        <template v-if="d.file_url && !d.is_pdf && /\.(jpg|jpeg|png|gif|webp)$/i.test(d.file_url)">
          <img
            :src="d.file_url"
            class="absolute inset-0 w-full h-full object-cover"
            alt="Document Preview"
          />
        </template>
        <template v-else>
          <div class="flex flex-col items-center text-gray-500 text-xs">
            <div class="text-4xl">📄</div>
            <span class="mt-2 break-all max-w-[90%]">{{ d.file_name }}</span>
            <a
              v-if="d.file_url"
              :href="d.file_url"
              target="_blank"
              class="mt-2 px-2 py-1 border rounded text-xs hover:bg-gray-100"
            >
              Open
            </a>
          </div>
        </template>
      </div>

      <!-- Body -->
      <div class="p-3.5 space-y-3 text-sm">
        <!-- Title EN -->
        <div>
          <label class="block text-[11px] text-gray-500 mb-1">Title (EN)</label>
          <textarea
            v-model="edit[d.id].title.en"
            class="form-input !h-auto min-h-9"
            rows="1"
            @input="autoGrow($event)"
          />
        </div>

        <!-- Title AR -->
        <div>
          <label class="block text-[11px] text-gray-500 mb-1">Title (AR)</label>
          <textarea
            v-model="edit[d.id].title.ar"
            class="form-input !h-auto min-h-9"
            rows="1"
            dir="rtl"
            @input="autoGrow($event)"
          />
        </div>

        <!-- Folder -->
        <FolderPicker
          type="document"
          :label="'Folder *'"
          v-model="edit[d.id].folder_id"
          :options="folders"
          @created="folders.unshift($event)"
          @refresh="fetchFolders"
        />

        <!-- Replace file -->
        <div class="text-[11px]">
          <label class="block text-gray-500 mb-1">Replace File</label>
          <input
            type="file"
            accept="image/*,application/pdf"
            @change="onReplaceFile(d.id, $event)"
          />
          <p
            v-if="pendingFile?.[d.id]"
            class="mt-1 text-[11px] text-gray-500"
          >
            Selected: {{ pendingFile[d.id]?.name }}
          </p>
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-2 pt-2">
          <button
            v-if="d.file_url"
            @click="window.open(d.file_url, '_blank')"
            class="px-3 py-1.5 text-sm rounded-lg border border-blue-200 text-blue-700 bg-blue-50 hover:bg-blue-100"
          >
            Open
          </button>
          <button
            class="px-3 py-1.5 text-sm rounded-lg border border-gray-300 bg-white hover:bg-gray-50 disabled:opacity-50"
            @click="saveOne(d.id)"
            :disabled="saving[d.id]"
          >
            {{ saving[d.id] ? 'Saving…' : 'Save' }}
          </button>
          <button
            class="px-3 py-1.5 text-sm rounded-lg border border-red-200 text-red-700 bg-red-50 hover:bg-red-100"
            @click="remove(d.id)"
          >
            Delete
          </button>
        </div>
      </div>
    </div>

    <div
      v-if="!rows.length"
      class="col-span-full text-center text-gray-500 py-8"
    >
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
  import FolderPicker from '@/Components/FolderPicker.vue'
  import { FolderApi } from '@/Services/folders'
  const folders = ref([])
  const folderLoading = ref(false)
  async function fetchFolders() {
    folderLoading.value = true
    try { folders.value = await FolderApi.list('document') }
    finally { folderLoading.value = false }
  }


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

/* List   per-card edit */
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

function autoGrow(e) {
  const el = e?.target
  if (!el) return
  el.style.height = 'auto'
  el.style.height = `${el.scrollHeight}px`
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

onMounted(async () => { await fetchFolders(); await fetchList(); })

</script>

<style scoped>
.form-input { @apply w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500; }
.aspect-\[4\/3\] { aspect-ratio: 4 / 3; }
</style>
