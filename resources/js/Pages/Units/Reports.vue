<template>
  <AuthenticatedLayout>
    <div class="p-6 space-y-6">
      <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-dash-title">Project Reports</h2>
        <a :href="backToUnits" class="px-3 py-1 border rounded text-black">Back to Projects</a>
      </div>

      <!-- 🔗 Unit navigation -->
      <UnitNav :unit-id="Number(unitId)" :cols="2" />

      <!-- Create (batch) -->
      <Form :validation-schema="createSchema" :validate-on-mount="false" :initial-values="createForm"
        @submit="createBatchValidated" v-slot="{ setFieldValue, submitCount }">
        <div class="bg-white p-4 rounded shadow">
          <h3 class="text-lg font-bold mb-3">Add Reports</h3>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <!-- Title EN -->
            <div>
              <label class="block text-xs text-gray-500 mb-1">Title (EN) *</label>
              <Field name="title.en" class="form-input" />
              <ErrorMessage v-if="submitCount > 0" name="title.en" class="error" />
            </div>

            <!-- Title AR -->
            <div>
              <label class="block text-xs text-gray-500 mb-1">Title (AR) *</label>
              <Field name="title.ar" class="form-input" />
              <ErrorMessage v-if="submitCount > 0" name="title.ar" class="error" />
            </div>
          </div>

          <!-- Files -->
          <div class="mt-4">
            <input ref="filesInputRef" type="file" multiple :accept="accepts"
              @change="(e) => onNewFilesValidated(e, setFieldValue)" />
            <ErrorMessage v-if="submitCount > 0" name="files" class="error" />

            <div v-if="newFiles.length" class="mt-3 text-sm text-gray-600">
              {{ newFiles.length }} file(s) selected
            </div>
          </div>

          <!-- Actions -->
          <div class="mt-4 flex items-center gap-3">
            <button type="submit" :disabled="creating" class="px-4 py-2 bg-black text-white rounded hover:bg-gray-700">
              {{ creating ? 'Uploading…' : 'Upload' }}
            </button>

            <span v-if="createErr" class="text-red-600 text-sm">
              {{ createErr }}
            </span>
          </div>
        </div>
      </Form>


      <!-- List -->
      <div class="bg-white p-4 rounded shadow">
        <div class="flex items-center justify-between mb-3">
          <h3 class="text-lg font-bold">Reports</h3>
          <button @click="fetchList" class="px-3 py-1 border rounded">Refresh</button>
        </div>

        <div v-if="loading" class="text-sm text-gray-500">Loading…</div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
          <div v-for="r in rows" :key="r.id" class="rounded border bg-gray-50 overflow-hidden">
            <div class="h-32 bg-white flex items-center justify-center p-3 text-center">
              <div class="text-gray-600">
                <div class="text-3xl">{{ r.file_icon.split(' ')[0] }}</div>
                <div class="text-xs mt-1 break-all">{{ r.file_name }}</div>
              </div>
            </div>

            <div class="p-3 space-y-2 text-sm">
              <div>
                <label class="block text-[11px] text-gray-500">Title (EN)</label>
                <input v-model="edit[r.id].title.en" class="form-input" type="text" />
                <p v-if="editErrors?.[r.id]?.['title.en']" class="error">
                  {{ editErrors[r.id]['title.en'] }}
                </p>

              </div>
              <div>
                <label class="block text-[11px] text-gray-500">Title (AR)</label>
                <input v-model="edit[r.id].title.ar" class="form-input" type="text" />
                <p v-if="editErrors?.[r.id]?.['title.ar']" class="error">
                  {{ editErrors[r.id]['title.ar'] }}
                </p>

              </div>

              <div class="text-[11px]">
                <label class="block text-gray-500">Replace File</label>
                <input type="file" :accept="accepts" @change="onReplaceFile(r.id, $event)" />
              </div>

              <div class="flex gap-2 pt-1">
                <a v-if="r.file_url" :href="r.file_url" target="_blank" class="px-2 py-1 border rounded">Open</a>
                <button class="px-2 py-1 border rounded" @click="saveOne(r.id)" :disabled="saving[r.id]">
                  {{ saving[r.id] ? 'Saving…' : 'Save' }}
                </button>
                <button class="px-2 py-1 border rounded text-red-600" @click="remove(r.id)">
                  Delete
                </button>
              </div>
            </div>
          </div>

          <div v-if="!rows.length" class="col-span-full text-center text-gray-500 py-8">
            No reports found.
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import UnitNav from '@/Components/UnitNav.vue'
import { ref, reactive, onMounted, computed } from 'vue'
import { UnitReportsApi, buildReportsCreateFD, buildReportsUpdateFD } from '@/Services/unitReports'
import { Form, Field, ErrorMessage } from "vee-validate"
import * as yup from "yup"

const createSchema = yup.object({
  title: yup.object({
    en: yup.string().required("English title is required"),
    ar: yup.string().required("Arabic title is required"),
  }),
  files: yup
    .mixed()
    .nullable()
    .test(
      "required",
      "Please select at least one file",
      value => Array.isArray(value) && value.length > 0
    ),
})

const editSchema = yup.object({
  title: yup.object({
    en: yup.string().required("English title is required"),
    ar: yup.string().required("Arabic title is required"),
  }),
})

const editErrors = reactive({})
const filesInputRef = ref(null)

const props = defineProps({
  unitId: { type: [Number, String], required: true },
})

const unitId = computed(() => props.unitId)
const backToUnits = computed(() => '/units-management')

const accepts =
  '.pdf,.doc,.docx,.xls,.xlsx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'

const createForm = ref({ title: { en: '', ar: '' }, files: [] })
const newFiles = ref([])
const creating = ref(false)
const createErr = ref('')

const canCreate = computed(
  () =>
    !!createForm.value.title.en?.trim() &&
    !!createForm.value.title.ar?.trim() &&
    newFiles.value.length > 0
)

function onNewFilesValidated(e, setFieldValue) {
  const files = Array.from(e.target.files || [])
  newFiles.value = files
  setFieldValue("files", files)
}


async function createBatchValidated(values, { resetForm }) {
  creating.value = true
  createErr.value = ""

  try {
    const items = values.files.map(f => ({
      title: { ...values.title },
      file: f,
    }))

    const fd = buildReportsCreateFD(unitId.value, items)
    await UnitReportsApi.create(fd)
    await fetchList()

    resetForm({
      values: {
        title: { en: "", ar: "" },
        files: [],
      },
    })

    // reset
    newFiles.value = []
    createForm.value.title = { en: "", ar: "" }

    if (filesInputRef.value) {
      filesInputRef.value.value = null
    }

  } catch (e) {
    console.error(e)
    createErr.value =
      e?.response?.data?.message ||
      e?.message ||
      "Upload failed"
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
    edit[r.id] = { title: { en: r.title_en || '', ar: r.title_ar || '' } }
  })
}

async function fetchList() {
  loading.value = true
  try {
    const data = await UnitReportsApi.list(unitId.value)
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
    const items = [{ id, title: item.title, file: pendingFile[id] }]
    const fd = buildReportsUpdateFD(unitId.value, items)
    await UnitReportsApi.update(fd)
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
  if (!confirm('Delete this report?')) return
  await UnitReportsApi.remove(id)
  await fetchList()
}

onMounted(fetchList)
</script>

<style scoped>
.form-input {
  @apply w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500;
}

.error {
  @apply text-red-600 text-xs mt-1;
}
</style>
