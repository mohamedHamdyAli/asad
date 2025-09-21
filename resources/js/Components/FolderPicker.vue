<script setup>
import { ref } from 'vue'
import { FolderApi, buildFolderFD } from '@/Services/folders'

const props = defineProps({
  type: { type: String, required: true },
  modelValue: { type: [Number, String, null], default: null },
  label: { type: String, default: 'Folder' },
  options: { type: Array, required: true }, // 👈 parent always passes folders
  unitId: { type: [Number, String], required: true },
})

const emit = defineEmits(['update:modelValue', 'created'])

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
      unit_id: props.unitId,
      name: form.value.name,
      folder_image: form.value.folder_image,
    })
    const created = await FolderApi.create(fd)
    emit('created', created) // parent merges it
    emit('update:modelValue', created.id) // select it immediately
    closeModal()
  } catch (e) {
    error.value = e?.response?.data?.message || e?.message || 'Create failed'
  } finally {
    creating.value = false
  }
}
</script>

<template>
  <div>
    <label v-if="label" class="block text-xs text-gray-500 mb-1">{{ label }}</label>
    <div class="flex gap-2">
      <select class="form-input flex-1" :value="modelValue ?? ''"
        @change="$emit('update:modelValue', toNumber($event.target.value))">
        <option value="" disabled>Select folder</option>
        <option v-for="f in options" :key="f.id" :value="f.id">
          {{ f.label }}
        </option>
      </select>

      <button type="button" class="px-2 py-1 border rounded" @click="showModal = true">
        + New
      </button>
    </div>

    <!-- create folder -->
<Teleport to="body">
  <div
    v-if="showModal"
    class="fixed inset-0 z-[1000] flex items-center justify-center"
  >
    <!-- Backdrop -->
    <div
      class="absolute inset-0 bg-black/50 backdrop-blur-sm"
      @click="closeModal"
    ></div>

    <!-- Panel -->
    <div
      class="relative z-10 bg-white rounded-2xl shadow-2xl w-full max-w-lg p-6 animate-fadeIn"
    >
      <!-- Header -->
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-xl font-semibold text-gray-800">Create Folder</h3>
        <button
          class="text-gray-400 hover:text-gray-600"
          @click="closeModal"
        >
          ✕
        </button>
      </div>

      <!-- Form -->
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
    <input type="file" accept="image/*" @change="onFile" class="form-input" />
    <img
      v-if="preview"
      :src="preview"
      class="mt-2 w-24 h-16 object-cover rounded border"
    />
  </div>
</div>


      <!-- Footer -->
      <div class="mt-6 flex justify-end gap-3">
        <button
          class="px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-100"
          @click="closeModal"
        >
          Cancel
        </button>
        <button
          class="px-4 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 disabled:opacity-60"
          :disabled="creating || !form.name.en.trim() || !form.name.ar.trim()"
          @click="createFolder"
        >
          {{ creating ? 'Saving…' : 'Create' }}
        </button>
      </div>

      <!-- Error -->
      <div v-if="error" class="mt-3 text-sm text-red-600">{{ error }}</div>
    </div>
  </div>
</Teleport>


  </div>
</template>

<style scoped>
.form-input {
  @apply w-full border border-gray-300 rounded-md px-3 py-2
         focus:outline-none focus:ring-2 focus:ring-blue-500;
}

</style>