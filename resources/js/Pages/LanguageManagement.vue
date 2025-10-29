<template>
  <Head title="Language Management" />
  <AuthenticatedLayout>
    <div class="p-6">
      <h2 class="text-2xl font-semibold text-dash-title mb-6">Language Management</h2>

      <div class="flex flex-col lg:flex-row gap-6 items-start">
        <!-- Add form -->
        <div class="w-full lg:w-1/4">
          <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-6">Add New Language</h3>

            <div class="grid gap-3 mb-4">
              <!-- Name -->
              <div>
                <input v-model.trim="form.name" type="text" placeholder="Language Name" class="form-input" />
                <p v-if="err('name')" class="text-xs text-red-600 mt-1">{{ err('name') }}</p>
              </div>

              <!-- English name -->
              <div>
                <input v-model.trim="form.english_name" type="text" placeholder="Language Name (English)" class="form-input" />
                <p v-if="err('english_name')" class="text-xs text-red-600 mt-1">{{ err('english_name') }}</p>
              </div>

              <!-- Code -->
              <div>
                <input v-model.trim="form.code" type="text" placeholder="Code (e.g., ar, en)" class="form-input" />
                <p v-if="err('code')" class="text-xs text-red-600 mt-1">{{ err('code') }}</p>
              </div>

              <!-- Country code -->
              <div>
                <input v-model.trim="form.country_code" type="text" placeholder="Country Code (e.g., EG, US)" class="form-input" />
                <p v-if="err('country_code')" class="text-xs text-red-600 mt-1">{{ err('country_code') }}</p>
              </div>

              <!-- Icon -->
              <div>
                <input type="file" accept="image/*" class="form-input" @change="onIconChange" />
                <p v-if="err('icon')" class="text-xs text-red-600 mt-1">{{ err('icon') }}</p>
              </div>

              <!-- RTL -->
              <label class="flex items-center gap-2 text-sm text-gray-600">
                <input v-model="form.rtl" type="checkbox" />
                RTL (Right to Left)
              </label>

              <button :disabled="saving" @click="addLanguage" class="bg-black text-white px-3 py-2 rounded hover:bg-gray-700 disabled:opacity-50">
                {{ saving ? 'Saving…' : '+ Add New Language' }}
              </button>
            </div>
          </div>
        </div>

        <!-- List -->
        <div class="w-full lg:w-3/4">
          <div class="bg-white shadow rounded-lg p-4 h-[600px] overflow-y-auto">
            <h3 class="text-lg font-semibold mb-4">Languages</h3>

            <div v-for="lang in languages" :key="lang.id" class="border-b py-3 px-2 flex justify-between items-center">
              <div>
                <div class="font-bold text-gray-800">
                  {{ lang.name }}
                </div>
                <div class="text-sm text-gray-500">Code: {{ lang.code }}</div>
              </div>

              <div class="Lang-image">
                <img
                  :src="lang.icon_url || `/images/flags/${lang.code}.png`"
                  :alt="lang.name"
                  class="w-8 h-6 object-cover"
                  @error="useRandomInternetFlag"
                />
              </div>

              <div class="flex gap-3 items-center">
                <!-- edit menu -->
                <div class="relative">
                  <button @click="toggleMenu(lang.id)" title="Edit" class="text-blue-600 hover:text-blue-800">
                    <Icon icon="mdi:pencil-outline" class="w-5 h-5" />
                  </button>
                  <div
                    v-if="openMenus[lang.id]"
                    class="absolute z-[100] mt-2 w-56 bg-white border rounded shadow -left-40"
                  >
                    <!-- New: edit meta -->
                    <button class="dropdown-item w-full text-left" @click="openEdit(lang.id)">
                      Edit Language Details
                    </button>

                    <div class="h-px bg-gray-100 my-1"></div>

                    <!-- existing per-file editors -->
                    <Link
                      v-for="t in editTypes"
                      :key="t.type"
                      class="dropdown-item"
                      :href="routeEditor(lang.id, t.type)"
                      @click="closeMenu(lang.id)"
                    >
                      {{ t.label }}
                    </Link>
                  </div>
                </div>

                <!-- delete -->
                <button @click="deleteLang(lang)" title="Delete" class="text-red-600 hover:text-red-800">
                  <Icon icon="mdi:trash-can-outline" class="w-5 h-5" />
                </button>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>

    <!-- Edit Modal -->
    <div v-if="editOpen" class="fixed inset-0 z-[200] bg-black/40 flex items-center justify-center">
      <div class="bg-white w-full max-w-2xl rounded-xl shadow-lg p-6 relative">
        <button class="absolute top-3 right-4 text-gray-500 hover:text-black" @click="closeEdit">✕</button>
        <h3 class="text-xl font-bold text-gray-800 mb-4">Edit Language Details</h3>

        <div v-if="editLoading" class="text-sm text-gray-500">Loading…</div>

        <div v-else class="grid gap-3">
          <!-- Name -->
          <div>
            <label class="text-sm text-gray-600">Language Name</label>
            <input v-model.trim="editForm.name" type="text" class="form-input" />
            <p v-if="eerr('name')" class="text-xs text-red-600 mt-1">{{ eerr('name') }}</p>
          </div>

          <!-- English name -->
          <div>
            <label class="text-sm text-gray-600">Language Name (English)</label>
            <input v-model.trim="editForm.english_name" type="text" class="form-input" />
            <p v-if="eerr('english_name')" class="text-xs text-red-600 mt-1">{{ eerr('english_name') }}</p>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <!-- Code -->
            <div>
              <label class="text-sm text-gray-600">Code</label>
              <input v-model.trim="editForm.code" type="text" class="form-input" placeholder="e.g., en, ar" />
              <p v-if="eerr('code')" class="text-xs text-red-600 mt-1">{{ eerr('code') }}</p>
            </div>

            <!-- Country code -->
            <div>
              <label class="text-sm text-gray-600">Country Code</label>
              <input v-model.trim="editForm.country_code" type="text" class="form-input" placeholder="e.g., EG, US" />
              <p v-if="eerr('country_code')" class="text-xs text-red-600 mt-1">{{ eerr('country_code') }}</p>
            </div>
          </div>

          <!-- RTL -->
          <label class="flex items-center gap-2 text-sm text-gray-600">
            <input v-model="editForm.rtl" type="checkbox" />
            RTL (Right to Left)
          </label>

          <!-- Icon -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-3 items-center">
            <div>
              <label class="text-sm text-gray-600">Icon</label>
              <input type="file" accept="image/*" class="form-input" @change="onEditIconChange" />
              <p v-if="eerr('icon')" class="text-xs text-red-600 mt-1">{{ eerr('icon') }}</p>
            </div>
            <div class="flex items-center gap-3">
              <img
                v-if="editIconPreview"
                :src="editIconPreview"
                class="w-10 h-8 rounded border object-cover"
                alt="Preview"
              />
              <img
                v-else-if="editForm.icon_url"
                :src="editForm.icon_url"
                class="w-10 h-8 rounded border object-cover"
                alt="Current"
              />
              <span class="text-xs text-gray-500" v-else>No icon</span>
            </div>
          </div>

          <div class="text-right pt-2">
            <button
              :disabled="editSaving"
              @click="submitEdit"
              class="bg-black text-white px-3 py-2 rounded hover:bg-gray-700 disabled:opacity-50"
            >
              {{ editSaving ? 'Saving…' : 'Save Changes' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Link, Head } from '@inertiajs/vue3'
import { onMounted, ref } from 'vue'
import { Icon } from '@iconify/vue'
import { languagesApi } from '@/api/languages'
import http from '@/lib/http'

// ======= State =======
const languages = ref([])
const openMenus = ref({})
const saving = ref(false)

const form = ref({
  name: '',
  english_name: '',
  code: '',
  country_code: '',
  rtl: false,
  icon: null,
})

const errors = ref({})
const err = (k) => (errors.value?.[k] ? errors.value[k] : '')

// ======= Add Form Handlers =======
function onIconChange(e) {
  form.value.icon = e.target.files?.[0] || null
}

function validate() {
  const e = {}
  if (!form.value.name) e.name = 'Language name is required.'
  if (!form.value.code) e.code = 'Code is required.'
  else if (form.value.code.length > 10) e.code = 'Code must be ≤ 10 characters.'
  if (form.value.country_code && form.value.country_code.length > 5) e.country_code = 'Country code must be ≤ 5 characters.'
  errors.value = e
  return Object.keys(e).length === 0
}

async function fetchLanguages() {
  const { data } = await languagesApi.list()
  languages.value = data.data || []
}
onMounted(fetchLanguages)

async function addLanguage() {
  if (!validate()) return
  saving.value = true
  try {
    const fd = new FormData()
    fd.append('name', form.value.name)
    fd.append('name_en', form.value.english_name || '')
    fd.append('code', form.value.code)
    fd.append('country_code', form.value.country_code || '')
    fd.append('app_scope', 'all')
    fd.append('rtl', form.value.rtl ? 1 : 0)
    if (form.value.icon) fd.append('icon', form.value.icon)

    await languagesApi.create(fd)
    resetForm()
    await fetchLanguages()
  } catch (err) {
    const v = err?.response?.data?.errors
    if (v && typeof v === 'object') {
      const mapped = {}
      Object.keys(v).forEach(k => { mapped[k] = Array.isArray(v[k]) ? v[k][0] : String(v[k]) })
      errors.value = mapped
    } else {
      alert(err?.response?.data?.message || 'Create failed')
    }
  } finally {
    saving.value = false
  }
}

function resetForm() {
  form.value = {
    name: '',
    english_name: '',
    code: '',
    country_code: '',
    rtl: false,
    icon: null,
  }
  errors.value = {}
}

// ======= Editor Links =======
const editTypes = [
  { type: 'panel', label: 'Edit Admin Panel Keywords' },
  { type: 'app', label: 'Edit User App Keywords' },
  { type: 'vendor', label: 'Edit Dashboard Keywords' },
  { type: 'web', label: 'Edit Website Keywords' },
]

function toggleMenu(id) {
  openMenus.value[id] = !openMenus.value[id]
}
function closeMenu(id) {
  openMenus.value[id] = false
}
function routeEditor(id, type) {
  return `/language/editor/${id}/${type}`
}

// ======= Delete =======
async function deleteLang(lang) {
  if (!confirm(`Delete ${lang.name}?`)) return
  await languagesApi.remove(lang.id)
  await fetchLanguages()
}

// ======= Fallback Flag =======
const randomFlagURLs = [
  'https://flagcdn.com/w320/us.png',
  'https://flagcdn.com/w320/fr.png',
  'https://flagcdn.com/w320/de.png',
  'https://flagcdn.com/w320/eg.png',
  'https://flagcdn.com/w320/jp.png'
]
function useRandomInternetFlag(e) {
  const i = Math.floor(Math.random() * randomFlagURLs.length)
  e.target.src = randomFlagURLs[i]
}

// ======= Edit Modal State =======
const editOpen = ref(false)
const editLoading = ref(false)
const editSaving = ref(false)
const editErrors = ref({})
const eerr = (k) => (editErrors.value?.[k] ? editErrors.value[k] : '')

const editId = ref(null)
const editForm = ref({
  name: '',
  english_name: '',
  code: '',
  country_code: '',
  rtl: false,
  icon: null,       // File
  icon_url: null,   // existing url from server (if any)
})
const editIconPreview = ref(null)

// Load language details
async function openEdit(id) {
  closeMenu(id)
  editId.value = id
  editOpen.value = true
  editLoading.value = true
  editErrors.value = {}
  editIconPreview.value = null

  try {
    const { data } = await http.get(`/api/language/one/${id}`)
    const row = data?.data
    // your index() mapped english_name as 'english_name' (name_en in DB)
    editForm.value = {
      name: row?.name ?? '',
      english_name: row?.english_name ?? row?.name_en ?? '',
      code: row?.code ?? '',
      country_code: row?.country_code ?? '',
      rtl: !!row?.rtl ?? !!row?.is_rtl,
      icon: null,
      icon_url: row?.icon_url ?? null,
    }
  } catch (err) {
    console.error(err)
    alert('Failed to load language details.')
    editOpen.value = false
  } finally {
    editLoading.value = false
  }
}

function closeEdit() {
  editOpen.value = false
  editId.value = null
  editErrors.value = {}
  editIconPreview.value = null
  // don't reset editForm entirely to allow reopening quickly
}

function onEditIconChange(e) {
  const f = e.target.files?.[0] || null
  editForm.value.icon = f
  if (f) {
    const reader = new FileReader()
    reader.onload = () => { editIconPreview.value = reader.result }
    reader.readAsDataURL(f)
  } else {
    editIconPreview.value = null
  }
}

// Simple front validation before hitting server
function validateEdit() {
  const e = {}
  if (!editForm.value.name) e.name = 'Language name is required.'
  if (!editForm.value.code) e.code = 'Code is required.'
  else if (editForm.value.code.length > 10) e.code = 'Code must be ≤ 10 characters.'
  if (editForm.value.country_code && editForm.value.country_code.length > 5) e.country_code = 'Country code must be ≤ 5 characters.'
  editErrors.value = e
  return Object.keys(e).length === 0
}

async function submitEdit() {
  if (!editId.value) return
  if (!validateEdit()) return

  editSaving.value = true
  try {
    const fd = new FormData()
    fd.append('name', editForm.value.name)
    fd.append('name_en', editForm.value.english_name || '')
    fd.append('code', editForm.value.code)
    fd.append('country_code', editForm.value.country_code || '')
    fd.append('rtl', editForm.value.rtl ? 1 : 0)
    if (editForm.value.icon) fd.append('icon', editForm.value.icon)

    await http.post(`/api/language/update/${editId.value}`, fd, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })

    alert('Language updated successfully.')
    closeEdit()
    await fetchLanguages()
  } catch (err) {
    const v = err?.response?.data?.errors
    if (v && typeof v === 'object') {
      const mapped = {}
      Object.keys(v).forEach(k => { mapped[k] = Array.isArray(v[k]) ? v[k][0] : String(v[k]) })
      editErrors.value = mapped
    } else {
      console.error(err)
      alert(err?.response?.data?.message || 'Update failed.')
    }
  } finally {
    editSaving.value = false
  }
}
</script>

<style scoped>
.form-input {
  @apply w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500;
}
.dropdown-item {
  @apply block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition;
}
</style>
