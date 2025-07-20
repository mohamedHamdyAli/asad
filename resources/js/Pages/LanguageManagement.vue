<template>
  <AuthenticatedLayout>
    <div class="p-6">
      <h2 class="text-2xl font-semibold text-gray-800 mb-6">Language Management</h2>

      <div class="flex flex-col lg:flex-row gap-6 items-start">
        <!-- Left: Add Language Button -->
        <div class="w-full lg:w-1/4">
          <div class="bg-white rounded-xl shadow-lg w-full max-w-5xl max-h-[90vh] overflow-y-auto p-6 relative">
        <h3 class="text-xl font-bold text-gray-800 mb-6">Add New Language</h3>
  
        <!-- Top Info -->
        <div class="grid grid-cols-1 sm:grid-cols-1 gap-4 mb-6">

          <input v-model="form.name" type="text" placeholder="Language Name" class="form-input" />
          <input v-model="form.english_name" type="text" placeholder="Language Name (in English)" class="form-input" />
          <input v-model="form.code" type="text" placeholder="Code" class="checkbox-form-input" />
          <input v-model="form.code" type="text" placeholder="Country Code" class="checkbox-form-input" />
          <input type="file" class="form-input" v-on:change="form.document = $event.target.files[0]" />
          <select v-model="form.scope" class="form-input">
            <option disabled value="">Select App</option>
            <option value="user">User App</option>
            <option value="worker">Worker App</option>
          </select>
          <span class="text-sm text-gray-600">
          <input v-model="form.rtl" type="checkbox" placeholder="RTL" label="RTL" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
            RTL (Right to Left direction)
          </span>

          <button
            @click="addLanguage"
            class="flex items-center rounded-md border border-transparent bg-gray-800 px-2 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-gray-700 focus:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-gray-900 align-center">
            + Add New Language
          </button>
        </div>
          </div>
        </div>

        <!-- Right: Language List -->
        <div class="w-full lg:w-3/4">
          <div class="bg-white shadow rounded-lg p-4 h-[600px] overflow-y-auto">
            <h3 class="text-lg font-semibold mb-4">Languages</h3>

            <div
              v-for="lang in languages"
              :key="lang.code"
              class="border-b py-3 px-2 flex justify-between items-center"
            >
              <div>
                <div class="font-bold text-gray-800">
                  {{ lang.name }} <span class="text-sm text-gray-500">({{ lang.scope }})</span>
                </div>
                <div class="text-sm text-gray-500">Code: {{ lang.code }}</div>
              </div>
              <div class="Lang-image">
              <img
                :src="`/images/flags/${lang.code}.png`"
                :alt="lang.name"
                class="w-8 h-6 object-cover"
                @error="useRandomInternetFlag($event)"
              />
            </div>

              <div class="flex gap-3 items-center">
                <div class="relative">
        <button @click="toggleSampleMenu(lang.code)" title="Edit" class="text-blue-600 hover:text-blue-800">
          <Icon icon="mdi:pencil-outline" class="w-5 h-5" />
        </button>
        <div
        v-if="openMenus[lang.code]"
        class="absolute z-[100] mt-2 w-44 bg-white border rounded shadow left-1/2 transform -translate-x-1/2
      "
      >
    <Link :href="route('language-editor', lang.code)" class="flex items-center px-4 py-2 text-sm hover:bg-gray-100 cursor-pointer">
    <div class="flex items-center px-4 py-2 text-sm hover:bg-gray-100 cursor-pointer">
      <!-- <Icon icon="mdi:clock-outline" class="me-2 w-4 h-4" /> -->
    Panel translation
    </div>
  </Link>
    <div class="border-t my-1"></div>
    <Link :href="route('language-editor', lang.code)" class="flex items-center px-4 py-2 text-sm hover:bg-gray-100 cursor-pointer">
    <div class="flex items-center px-4 py-2 text-sm hover:bg-gray-100 cursor-pointer">
      <!-- <Icon icon="mdi:cloud-outline" class="me-2 w-4 h-4" /> -->
      Web translation
    </div>
    </Link>
    <div class="border-t my-1"></div>
    <Link :href="route('language-editor', lang.code)" class="flex items-center px-4 py-2 text-sm hover:bg-gray-100 cursor-pointer">
    <div class="flex items-center px-4 py-2 text-sm hover:bg-gray-100 cursor-pointer">
      <!-- <Icon icon="mdi:selection" class="me-2 w-4 h-4" /> -->
      App translation
    </div>
  </Link>
  </div>
</div>

                <button
                  @click="toggle(lang)"
                  :title="lang.enabled ? 'Disable' : 'Enable'"
                  :class="lang.enabled ? 'text-yellow-600 hover:text-yellow-800' : 'text-green-600 hover:text-green-800'"
                >
                  <Icon
                    :icon="lang.enabled ? 'mdi:block-helper' : 'mdi:check-circle-outline'"
                    class="w-5 h-5"
                  />
                </button>
                <button @click="deleteLang(lang)" title="Delete" class="text-red-600 hover:text-red-800">
                  <Icon icon="mdi:trash-can-outline" class="w-5 h-5" />
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal -->
      <LanguageModal v-if="showModal" @close="showModal = false" />
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { onMounted, ref } from 'vue'
import { Icon } from '@iconify/vue'
import LanguageModal from '@/Components/LanguageModal.vue'
import { Link } from '@inertiajs/vue3'
import axios from 'axios'

const showModal = ref(false)

const form = ref({
    name: '',
    english_name: '',
    code: '',
    document: null,
    country_code: '',
    scope: '',
    rtl: false,
  })

const languages = ref([])

const fetchLanguages = async () => {
  try {
    const res = await axios.get('/api/get-languages')
    languages.value = res.data.data
  } catch (err) {
    console.error(err)
  }
}

onMounted(fetchLanguages)

const addLanguage = async () => {
  console.log('Add Language')
  const payload = new FormData() 
  FormData.append('name', form.value.name)
  FormData.append('english_name', form.value.english_name)
  FormData.append('code', form.value.code)
  FormData.append('country_code', form.value.country_code)
  FormData.append('document', form.value.document)
  FormData.append('scope', form.value.scope)
  FormData.append('rtl', form.value.rtl ? 1 : 0)
  try {
    const res = await axios.post('/language/create', payload)
    alert(res.data.message)
    fetchLanguages()
  } catch (err) {
    console.error(err)
  }
}
const openMenus = ref({})

const toggleSampleMenu = (langCode) => {
  openMenus.value[langCode] = !openMenus.value[langCode]
}

const toggle = (lang) => {
  lang.enabled = !lang.enabled
}

const deleteLang = async (lang) => {
  if (!confirm(`Are you sure you want to delete ${lang.name}?`)) return
  try {
    const res = await axios.delete(`/language/delete/${lang.id}`)
    alert(res.data.message)
    fetchLanguages()
  } catch (err) {
    console.error('Delete failed:', err)
  }
}

const randomFlagURLs = [
  'https://flagcdn.com/w320/us.png',
  'https://flagcdn.com/w320/fr.png',
  'https://flagcdn.com/w320/de.png',
  'https://flagcdn.com/w320/eg.png',
  'https://flagcdn.com/w320/jp.png'
]

const useRandomInternetFlag = (e) => {
  const index = Math.floor(Math.random() * randomFlagURLs.length)
  e.target.src = randomFlagURLs[index]
}


</script>
<style scoped>
.form-input {
  @apply w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500;
}

.checkbox-form-input {
  @apply w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500;
}
</style>

