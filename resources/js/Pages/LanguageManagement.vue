<template>
  <Head title="Language Management" />
  <AuthenticatedLayout>
    <div class="p-6">
      <h2 class="text-2xl font-semibold text-gray-800 mb-6">Language Management</h2>

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
                <input v-model.trim="form.english_name" type="text" placeholder="Language Name (English)"
                  class="form-input" />
                <p v-if="err('english_name')" class="text-xs text-red-600 mt-1">{{ err('english_name') }}</p>
              </div>

              <!-- Code -->
              <div>
                <input v-model.trim="form.code" type="text" placeholder="Code (e.g., ar, en)" class="form-input" />
                <p v-if="err('code')" class="text-xs text-red-600 mt-1">{{ err('code') }}</p>
              </div>

              <!-- Country code -->
              <div>
                <input v-model.trim="form.country_code" type="text" placeholder="Country Code (e.g., EG, US)"
                  class="form-input" />
                <p v-if="err('country_code')" class="text-xs text-red-600 mt-1">{{ err('country_code') }}</p>
              </div>

              <!-- Icon -->
              <div>
                <input type="file" accept="image/*" class="form-input" @change="onIconChange" />
                <p v-if="err('icon')" class="text-xs text-red-600 mt-1">{{ err('icon') }}</p>
              </div>

              <!-- Scope -->
              <!-- <div>
                <select v-model="form.scope" class="form-input">
                  <option disabled value="">Select Scope</option>
                  <option value="user">User</option>
                  <option value="vendor">Vendor</option>
                  <option value="admin">Admin</option>
                </select>
                <p v-if="err('scope')" class="text-xs text-red-600 mt-1">{{ err('scope') }}</p>
              </div> -->

              <!-- RTL -->
              <label class="flex items-center gap-2 text-sm text-gray-600">
                <input v-model="form.rtl" type="checkbox" />
                RTL (Right to Left)
              </label>

              <button :disabled="saving" @click="addLanguage"
                class="bg-gray-800 text-white px-3 py-2 rounded hover:bg-gray-700 disabled:opacity-50">
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
                <img :src="lang.icon_url || `/images/flags/${lang.code}.png`" :alt="lang.name"
                  class="w-8 h-6 object-cover" @error="useRandomInternetFlag" />
              </div>

              <div class="flex gap-3 items-center">
                <!-- edit menu -->
                <div class="relative">
                  <button @click="toggleMenu(lang.id)" title="Edit" class="text-blue-600 hover:text-blue-800">
                    <Icon icon="mdi:pencil-outline" class="w-5 h-5" />
                  </button>
                  <div v-if="openMenus[lang.id]"
                    class="absolute z-[100] mt-2 w-48 bg-white border rounded shadow -left-24">
                    <!-- Use Inertia Link so the editor page opens correctly -->
                    <Link class="block px-4 py-2 text-sm hover:bg-gray-100" :href="routeEditor(lang.id, 'all')"
                      @click="closeMenu(lang.id)">
                    Edit Language keywords
                    </Link>

                    <!-- If you also support car_* files, keep these: -->
                    <!--
                    <Link class="block px-4 py-2 text-sm hover:bg-gray-100" :href="routeEditor(lang.id,'car_type')" @click="closeMenu(lang.id)">Car Types</Link>
                    <Link class="block px-4 py-2 text-sm hover:bg-gray-100" :href="routeEditor(lang.id,'car_model')" @click="closeMenu(lang.id)">Car Models</Link>
                    <Link class="block px-4 py-2 text-sm hover:bg-gray-100" :href="routeEditor(lang.id,'car_color')" @click="closeMenu(lang.id)">Car Colors</Link>
                    -->
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
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Link } from '@inertiajs/vue3'
import { onMounted, ref } from 'vue'
import { Icon } from '@iconify/vue'
import { languagesApi } from '@/api/languages'
import { Head } from '@inertiajs/vue3'

const languages = ref([])
const openMenus = ref({})
const saving = ref(false)

const form = ref({
  name: '',
  name_en: '',
  code: '',
  country_code: '',
  // scope: '',
  rtl: false,
  icon: null,
})

const errors = ref({})

// helpers
const err = (k) => (errors.value?.[k] ? errors.value[k] : '')

function onIconChange(e) {
  form.value.icon = e.target.files?.[0] || null
}

function validate() {
  const e = {}
  if (!form.value.name) e.name = 'Language name is required.'
  if (!form.value.code) e.code = 'Code is required.'
  else if (form.value.code.length > 10) e.code = 'Code must be ≤ 10 characters.'
  // if (!form.value.scope) e.scope = 'Scope is required.'
  if (form.value.country_code && form.value.country_code.length > 5)
    e.country_code = 'Country code must be ≤ 5 characters.'
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
    // fd.append('scope', form.value.scope || '')
    fd.append('app_scope', 'all')
    fd.append('rtl', form.value.rtl ? 1 : 0)
    if (form.value.icon) fd.append('icon', form.value.icon)

    await languagesApi.create(fd)
    resetForm()
    await fetchLanguages()
  } catch (err) {
    // map server-side validation
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
    scope: '',
    rtl: false,
    icon: null,
  }
  errors.value = {}
}

function toggleMenu(id) {
  openMenus.value[id] = !openMenus.value[id]
}
function closeMenu(id) {
  openMenus.value[id] = false
}
function routeEditor(id, type='all') {
  return `/language/editor/${id}/${type}`
}


async function deleteLang(lang) {
  if (!confirm(`Delete ${lang.name}?`)) return
  await languagesApi.remove(lang.id)
  await fetchLanguages()
}

// fallback flag
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
</script>

<style scoped>
.form-input {
  @apply w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500;
}
</style>
