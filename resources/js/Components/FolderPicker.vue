<script setup>
import { ref } from 'vue'
import { FolderApi, buildFolderFD } from '@/Services/folders'

const props = defineProps({
  type: { type: String, required: true },
  modelValue: { type: [Number, String, null], default: null },
  label: { type: String, default: 'Folder' },
  options: { type: Array, required: true }, // parent always passes folders
  unitId: { type: [Number, String], required: true },
})

const emit = defineEmits(['update:modelValue', 'created', 'refresh', 'removed'])

/* Dropdown state */
const open = ref(false)

/* Modal state */
const showModal = ref(false)
const creating = ref(false)
const error = ref('')
const form = ref({ name: { en: '', ar: '' }, folder_image: null })
const preview = ref(null)
const editingId = ref(null)
const isEdit = ref(false)

/* Dropdown handlers */
function toggle() { open.value = !open.value }
function selectFolder(id) {
  emit('update:modelValue', id)
  open.value = false
}
async function deleteFolder(id) {
  if (!confirm('Delete this folder?')) return
  await FolderApi.remove(id)
  emit('removed', id)
  emit('refresh')
}

/* Modal helpers */
function onFile(e) {
  const f = e.target.files?.[0] || null
  form.value.folder_image = f
  preview.value = f ? URL.createObjectURL(f) : null
}
function resetForm() {
  form.value = { name: { en: '', ar: '' }, folder_image: null }
  preview.value = null
  error.value = ''
  editingId.value = null
  isEdit.value = false
}

function closeModal() {
  showModal.value = false
  resetForm()
}

async function submitFolder() {
  creating.value = true
  error.value = ''

  try {
    const fd = buildFolderFD({
      file_type: props.type,
      unit_id: props.unitId,
      name: form.value.name,
      folder_image: form.value.folder_image,
    })

    let result

    if (isEdit.value) {
      result = await FolderApi.update(editingId.value, fd)
      emit('refresh')
    } else {
      result = await FolderApi.create(fd)
      emit('created', result)
      emit('update:modelValue', result.id)
    }

    closeModal()
  } catch (e) {
    error.value =
      e?.response?.data?.message || e?.message || 'Operation failed'
  } finally {
    creating.value = false
  }
}


function editFolder(folder) {
  isEdit.value = true
  editingId.value = folder.id

  form.value = {
    name: {
      en: folder.name_en,
      ar: folder.name_ar,
    },
    folder_image: null,
  }

  preview.value = folder.folder_image
  open.value = false
  showModal.value = true
}

</script>

<template>
  <div class="relative w-full">
    <label v-if="label" class="block text-xs text-gray-500 mb-1">{{ label }}</label>

    <!-- Trigger -->
    <button type="button" class="form-input flex justify-between items-center w-full" @click="toggle">
      <span>
        {{options.find(f => f.id === modelValue)?.label || 'Select folder'}}
      </span>
      <svg class="w-4 h-4 ml-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
      </svg>
    </button>

    <!-- Dropdown -->
    <div v-if="open"
      class="absolute mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg z-50 max-h-64 overflow-auto">
      <div v-for="f in options" :key="f.id"
        class="flex items-center justify-between px-3 py-2 text-sm hover:bg-gray-50 cursor-pointer">
        <!-- Select -->
        <span class="truncate flex-1" @click="selectFolder(f.id)">
          {{ f.label }}
        </span>
        <!-- Delete -->
        <div class="flex gap-2">
          <!-- Edit -->
          <button class="text-blue-500 hover:text-blue-700 text-xs" @click.stop="editFolder(f)">
            Edit
          </button>

          <!-- Delete -->
          <button class="text-red-500 hover:text-red-700 text-xs" @click.stop="deleteFolder(f.id)">
            ✕
          </button>
        </div>

      </div>

      <!-- New folder -->
      <div class="px-3 py-2 text-sm text-green-600 hover:bg-gray-50 cursor-pointer border-t"
        @click="showModal = true; open = false">
        + New Folder
      </div>
    </div>

    <!-- Create Folder Modal -->
    <Teleport to="body">
      <div v-if="showModal" class="fixed inset-0 z-[1000] flex items-center justify-center">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="closeModal"></div>

        <!-- Panel -->
        <div class="relative z-10 bg-white rounded-2xl shadow-2xl w-full max-w-lg p-6 animate-fadeIn">
          <!-- Header -->
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-semibold text-gray-800">
              {{ isEdit ? 'Edit Folder' : 'Create Folder' }}
            </h3>
            <button class="text-gray-400 hover:text-gray-600" @click="closeModal">✕</button>
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
              <img v-if="preview" :src="preview" class="mt-2 w-24 h-16 object-cover rounded border" />
            </div>
          </div>

          <!-- Footer -->
          <div class="mt-6 flex justify-end gap-3">
            <button class="px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-100" @click="closeModal">
              Cancel
            </button>
            <button class="px-4 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 disabled:opacity-60"
              :disabled="creating || !form.name.en.trim() || !form.name.ar.trim()" @click="submitFolder">
              {{ creating ? 'Saving…' : isEdit ? 'Update' : 'Create' }}
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
  @apply w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white;
}

.animate-fadeIn {
  animation: fadeIn 0.2s ease-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: scale(0.95);
  }

  to {
    opacity: 1;
    transform: scale(1);
  }
}
</style>
