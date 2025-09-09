<template>
  <div class="space-y-2">
    <label v-if="label" class="block text-xs text-gray-500">{{ label }}</label>
    <div class="flex gap-2">
      <select
        class="form-input flex-1"
        :disabled="loading"
        :value="modelValue ?? ''"
        @change="$emit('update:modelValue', toNumber($event.target.value))"
      >
        <option value="" disabled>{{ loading ? 'Loading folders…' : 'Select folder' }}</option>
        <option v-for="f in folders" :key="f.id" :value="f.id">
          {{ f.label }} ({{ f.id }})
        </option>
      </select>

      <button type="button" class="px-2 py-1 border rounded" @click="showModal = true">
        + New
      </button>
      <button type="button" class="px-2 py-1 border rounded" @click="fetchFolders">↻</button>
    </div>

    <!-- Create Folder Modal -->
    <div v-if="showModal" class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center">
      <div class="bg-white rounded-xl p-4 w-full max-w-md relative">
        <button class="absolute top-2 right-2 text-gray-400 hover:text-gray-600" @click="closeModal">✕</button>
        <h3 class="text-lg font-semibold mb-3">Create Folder</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
          <div>
            <label class="block text-xs text-gray-500 mb-1">Name (EN) *</label>
            <input v-model="form.name.en" type="text" class="form-input" />
          </div>
          <div>
            <label class="block text-xs text-gray-500 mb-1">Name (AR) *</label>
            <input v-model="form.name.ar" type="text" class="form-input" />
          </div>
          <div class="md:col-span-2">
            <label class="block text-xs text-gray-500 mb-1">Image (optional)</label>
            <input type="file" accept="image/*" @change="onFile" />
            <img v-if="preview" :src="preview" class="mt-2 w-24 h-16 object-cover rounded border" />
          </div>
        </div>

        <div class="mt-4 flex justify-end gap-2">
          <button class="px-3 py-1 border rounded" @click="closeModal">Cancel</button>
          <button class="px-3 py-1 bg-green-600 text-white rounded disabled:opacity-60"
                  :disabled="creating || !form.name.en.trim() || !form.name.ar.trim()"
                  @click="createFolder">
            {{ creating ? 'Saving…' : 'Create' }}
          </button>
        </div>

        <div v-if="error" class="mt-2 text-sm text-red-600">{{ error }}</div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'
import { FolderApi, buildFolderFD } from '@/Services/folders'

const props = defineProps({
  type: { type: String, required: true },
  modelValue: { type: [Number, String, null], default: null },
  label: { type: String, default: 'Folder' },
  options: { type: Array, default: null },
})

const emit = defineEmits(['update:modelValue', 'created', 'refresh'])

const loading = ref(false)
const folders = ref(props.options ?? [])
watch(() => props.options, (v) => { if (Array.isArray(v)) folders.value = v })

async function fetchFolders() {
  if (props.options) {
    emit('refresh')
    return
  }
  loading.value = true
  try {
    folders.value = await FolderApi.list(props.type)
  } finally {
    loading.value = false
  }
}
onMounted(() => { if (!props.options) fetchFolders() })

const showModal = ref(false)
const creating = ref(false)
const error = ref('')
const form = ref({ name: { en: '', ar: '' }, folder_image: null })
const preview = ref(null)

function onFile(e) {
  const f = e.target.files?.[0] || null
  form.value.folder_image = f
  preview.value = f ? URL.createObjectURL(f) : null
}
function closeModal() {
  showModal.value = false
  error.value = ''
  form.value = { name: { en: '', ar: '' }, folder_image: null }
  preview.value = null
}
function toNumber(v) {
  const n = Number(v)
  return Number.isNaN(n) ? null : n
}

async function createFolder() {
  creating.value = true
  error.value = ''
  try {
    const fd = buildFolderFD({
      file_type: props.type,
      name: form.value.name,
      folder_image: form.value.folder_image,
    })
    const created = await FolderApi.create(fd)
    folders.value = [created, ...folders.value]
    emit('created', created)
    emit('update:modelValue', created.id)
    closeModal()
  } catch (e) {
    error.value = e?.response?.data?.message || e?.message || 'Create failed'
  } finally {
    creating.value = false
  }
}
</script>

<style scoped>
.form-input { @apply w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500; }
</style>
