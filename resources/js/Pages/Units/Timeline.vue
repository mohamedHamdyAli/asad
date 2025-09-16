<template>
  <AuthenticatedLayout>
    <div class="p-6 space-y-6">
      <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800">Unit Timeline</h2>
        <a :href="backToUnits" class="px-3 py-1 border rounded">Back to Units</a>
      </div>

      <!-- Create (batch) -->
      <div class="bg-white p-4 rounded shadow">
        <h3 class="text-lg font-bold mb-3">Add Timeline Items</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
          <input type="file" multiple :accept="accepts" @change="onNewFiles" />
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

   <div class="bg-white p-5 rounded-2xl shadow-sm ring-1 ring-black/[0.05]">
  <div class="flex items-center justify-between mb-4">
    <h3 class="text-lg font-semibold text-gray-800">Timeline Items</h3>
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
      v-for="r in rows"
      :key="r.id"
      class="group rounded-2xl overflow-hidden bg-white border border-gray-200/70 shadow-[0_1px_2px_rgba(0,0,0,0.04)] hover:shadow-[0_8px_24px_rgba(0,0,0,0.08)] hover:-translate-y-0.5 transition-all duration-200"
    >
      <!-- File header -->
      <div
        class="h-32 bg-gray-50 flex flex-col items-center justify-center text-center p-3 relative"
      >
        <div class="text-3xl text-gray-600">{{ r.file_icon.split(' ')[0] }}</div>
        <div
          class="mt-2 text-xs text-gray-700 break-words max-w-[90%] whitespace-pre-wrap"
        >
          {{ r.file_name }}
        </div>

        <!-- ID badge -->
        <span
          class="absolute top-2 left-2 inline-flex items-center rounded-full bg-white/90 backdrop-blur px-2 py-0.5 text-[11px] font-medium text-gray-600 border border-gray-200"
        >
          #{{ r.id }}
        </span>
      </div>

      <!-- Body -->
      <div class="p-3.5 space-y-3 text-sm">
        <!-- Title EN -->
        <div>
          <label class="block text-[11px] text-gray-500 mb-1">Title (EN)</label>
          <textarea
            v-model="edit[r.id].title.en"
            class="form-input !h-auto min-h-9"
            rows="1"
            @input="autoGrow($event)"
          />
        </div>

        <!-- Title AR -->
        <div>
          <label class="block text-[11px] text-gray-500 mb-1">Title (AR)</label>
          <textarea
            v-model="edit[r.id].title.ar"
            class="form-input !h-auto min-h-9"
            rows="1"
            dir="rtl"
            @input="autoGrow($event)"
          />
        </div>

        <!-- Replace file -->
        <div class="text-[11px]">
          <label class="block text-gray-500 mb-1">Replace File</label>
          <input
            type="file"
            :accept="accepts"
            @change="onReplaceFile(r.id, $event)"
          />
          <p
            v-if="pendingFile?.[r.id]"
            class="mt-1 text-[11px] text-gray-500"
          >
            Selected: {{ pendingFile[r.id]?.name }}
          </p>
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-2 pt-2">
          <a
            v-if="r.file_url"
            :href="r.file_url"
            target="_blank"
            class="px-3 py-1.5 text-sm rounded-lg border border-blue-200 text-blue-700 bg-blue-50 hover:bg-blue-100"
          >
            Open
          </a>
          <button
            class="px-3 py-1.5 text-sm rounded-lg border border-gray-300 bg-white hover:bg-gray-50 disabled:opacity-50"
            @click="saveOne(r.id)"
            :disabled="saving[r.id]"
          >
            {{ saving[r.id] ? 'Saving…' : 'Save' }}
          </button>
          <button
            class="px-3 py-1.5 text-sm rounded-lg border border-red-200 text-red-700 bg-red-50 hover:bg-red-100"
            @click="remove(r.id)"
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
      No timeline items found.
    </div>
  </div>
</div>

    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { ref, reactive, onMounted, computed } from 'vue'
import { UnitTimelineApi, buildTimelineCreateFD, buildTimelineUpdateFD } from '@/Services/unitTimeline'

const props = defineProps({
  unitId: { type: [Number, String], required: true },
})

const backToUnits = computed(() => '/units-management')

const accepts =
  '.pdf,.doc,.docx,.xls,.xlsx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'

const createForm = ref({ title: { en: '', ar: '' } })
const newFiles = ref([])
const creating = ref(false)
const createErr = ref('')

const canCreate = computed(() =>
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
      title: { ...createForm.value.title },
      file: f,
    }))
    const fd = buildTimelineCreateFD(props.unitId, items)
    await UnitTimelineApi.create(fd)
    await fetchList()
    newFiles.value = []
    createForm.value.title = { en: '', ar: '' }
  } catch (e) {
    console.error(e)
    createErr.value = e?.response?.data?.message || e?.message || 'Upload failed'
  } finally {
    creating.value = false
  }
}

const rows = ref([])
const loading = ref(false)
const edit = reactive({})
const pendingFile = reactive({})
const saving = reactive({})

function seedEditState(arr) {
  arr.forEach(r => {
    edit[r.id] = {
      title: { en: r.title_en || '', ar: r.title_ar || '' },
    }
  })
}

async function fetchList() {
  loading.value = true
  try {
    const data = await UnitTimelineApi.list(props.unitId)
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
      title: item.title,    
      file: pendingFile[id],
    }]
    const fd = buildTimelineUpdateFD(props.unitId, items)
    await UnitTimelineApi.update(fd)
    delete pendingFile[id]
    await fetchList()
  } catch (e) {
    console.error(e)
    alert(e?.response?.data?.message || 'Update failed')
  } finally {
    saving[id] = false
  }
}

function autoGrow(e) {
  const el = e?.target
  if (!el) return
  el.style.height = 'auto'
  el.style.height = `${el.scrollHeight}px`
}


async function remove(id) {
  if (!confirm('Delete this timeline item?')) return
  await UnitTimelineApi.remove(id)
  await fetchList()
}

onMounted(fetchList)
</script>

<style scoped>
.form-input { @apply w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500; }
</style>
