<template>
  <AuthenticatedLayout>
    <div class="p-6 space-y-6">
      <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-white">Contractors</h2>
        <button class="px-3 py-1 border rounded text-white" @click="fetchList">Refresh</button>
      </div>

      <!-- Create one contractor -->
      <div class="bg-white p-4 rounded shadow">
        <h3 class="text-lg font-bold mb-3">Add Contractor</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <div>
            <label class="block text-xs text-gray-500 mb-1">Title (EN) *</label>
            <input v-model="createForm.title.en" type="text" class="form-input" />
          </div>
          <div>
            <label class="block text-xs text-gray-500 mb-1">Title (AR) *</label>
            <input v-model="createForm.title.ar" type="text" class="form-input" />
          </div>
          <div>
            <label class="block text-xs text-gray-500 mb-1">Email *</label>
            <input v-model="createForm.email" type="email" class="form-input" />
          </div>
          <div class="md:col-span-1">
            <label class="block text-xs text-gray-500 mb-1">Image *</label>
            <input type="file" accept="image/*" @change="onCreateFile" />
            <img v-if="createPreview" :src="createPreview" class="mt-2 w-24 h-16 object-cover rounded border" />
          </div>
          <div class="md:col-span-2">
            <label class="block text-xs text-gray-500 mb-1">Description (EN) *</label>
            <textarea v-model="createForm.description.en" type="text" class="form-input" ></textarea>
          </div>
          <div class="md:col-span-2">
            <label class="block text-xs text-gray-500 mb-1">Description (AR) *</label>
            <textarea v-model="createForm.description.ar" type="text" class="form-input" ></textarea>
          </div>
        </div>

        <div class="mt-4 flex items-center gap-3">
          <button
            class="px-4 py-2 bg-green-600 text-white rounded disabled:opacity-60"
            :disabled="creating || !canCreate"
            @click="createOne"
          >
            {{ creating ? 'Saving…' : 'Add' }}
          </button>
          <span v-if="createErr" class="text-sm text-red-600">{{ createErr }}</span>
        </div>
      </div>

      <!-- List -->
      <div class="bg-white p-4 rounded shadow">
        <h3 class="text-lg font-bold mb-3">All Contractors</h3>
        <div v-if="loading" class="text-sm text-gray-500">Loading…</div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
          <div v-for="c in rows" :key="c.id" class="border rounded bg-gray-50 overflow-hidden">
            <div class="h-40 bg-white flex items-center justify-center">
              <img v-if="c.image_url" :src="c.image_url" class="max-h-40 object-contain" />
              <div v-else class="text-gray-400">No image</div>
            </div>

            <div class="p-3 space-y-3 text-sm">
              <div class="flex items-center justify-between">
                <div class="font-semibold">#{{ c.id }}</div>
                <button class="px-2 py-1 border rounded text-red-600" @click="remove(c.id)">Delete</button>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                  <label class="block text-[11px] text-gray-500 mb-1">Title (EN)</label>
                  <input v-model="edit[c.id].title.en" class="form-input" type="text" />
                </div>
                <div>
                  <label class="block text-[11px] text-gray-500 mb-1">Title (AR)</label>
                  <input v-model="edit[c.id].title.ar" class="form-input" type="text" />
                </div>
                <div class="md:col-span-2">
                  <label class="block text-[11px] text-gray-500 mb-1">Description (EN)</label>
                  <input v-model="edit[c.id].description.en" class="form-input" type="text" />
                </div>
                <div class="md:col-span-2">
                  <label class="block text-[11px] text-gray-500 mb-1">Description (AR)</label>
                  <input v-model="edit[c.id].description.ar" class="form-input" type="text" />
                </div>
                <div>
                  <label class="block text-[11px] text-gray-500 mb-1">Email</label>
                  <input v-model="edit[c.id].email" class="form-input" type="email" />
                </div>
                <div>
                  <label class="block text-[11px] text-gray-500 mb-1">Replace Image</label>
                  <input type="file" accept="image/*" @change="onReplaceFile(c.id, $event)" />
                </div>
              </div>

              <div>
                <button class="px-3 py-1 border rounded" :disabled="saving[c.id]" @click="saveOne(c.id)">
                  {{ saving[c.id] ? 'Saving…' : 'Save' }}
                </button>
              </div>
            </div>
          </div>

          <div v-if="!rows.length" class="col-span-full text-center text-gray-500 py-8">
            No contractors found.
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { ref, reactive, onMounted, computed } from 'vue'
import {
  ContractorsApi,
  buildContractorsCreateFD,
  buildContractorsUpdateFD
} from '@/Services/contractors'

/* Create */
const createForm = ref({
  title: { en: '', ar: '' },
  description: { en: '', ar: '' },
  email: '',
  image: null,
})
const createPreview = ref(null)
function onCreateFile(e){
  const f = e.target.files?.[0] || null
  createForm.value.image = f
  createPreview.value = f ? URL.createObjectURL(f) : null
}
const creating = ref(false)
const createErr = ref('')
const canCreate = computed(() =>
  !!createForm.value.title.en?.trim() &&
  !!createForm.value.title.ar?.trim() &&
  !!createForm.value.description.en?.trim() &&
  !!createForm.value.description.ar?.trim() &&
  !!createForm.value.email?.trim() &&
  !!createForm.value.image
)

async function createOne(){
  creating.value = true
  createErr.value = ''
  try {
    const fd = buildContractorsCreateFD([ createForm.value ])
    await ContractorsApi.create(fd)
    await fetchList()
    // reset
    createForm.value = { title:{en:'',ar:''}, description:{en:'',ar:''}, email:'', image:null }
    createPreview.value = null
  } catch (e) {
    console.error(e)
    // surface validation errors (422)
    createErr.value = e?.response?.data?.message || e?.message || 'Create failed'
  } finally {
    creating.value = false
  }
}

/* List + per-card edit */
const rows = ref([])
const loading = ref(false)
const edit = reactive({})
const saving = reactive({})
const pendingFile = reactive({})

function seedEditState(arr){
  Object.keys(edit).forEach(k => delete edit[k])
  arr.forEach(c => {
    edit[c.id] = {
      title: { en: c.title_en || '', ar: c.title_ar || '' },
      description: { en: c.description_en || '', ar: c.description_ar || '' },
      email: c.email || '',
    }
  })
}

async function fetchList(){
  loading.value = true
  try {
    rows.value = await ContractorsApi.list()
    seedEditState(rows.value)
  } finally {
    loading.value = false
  }
}

function onReplaceFile(id, e){
  const f = e.target.files?.[0] || null
  if (f) pendingFile[id] = f
}

async function saveOne(id){
  const item = edit[id]
  if (!item) return
  saving[id] = true
  try {
    const payload = {
      title: item.title,
      description: item.description,
      email: item.email,
      image: pendingFile[id],
    }
    const fd = buildContractorsUpdateFD(payload)
    await ContractorsApi.update(id, fd)
    delete pendingFile[id]
    await fetchList()
  } catch (e) {
    console.error(e)
    alert(e?.response?.data?.message || 'Update failed')
  } finally {
    saving[id] = false
  }
}

async function remove(id){
  if (!confirm('Delete this contractor?')) return
  await ContractorsApi.remove(id)
  await fetchList()
}

onMounted(fetchList)
</script>

<style scoped>
.form-input { @apply w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500; }
</style>
