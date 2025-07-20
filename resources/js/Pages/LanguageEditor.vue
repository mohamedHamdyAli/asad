<template>
    <AuthenticatedLayout>
      <div class="p-6 space-y-6">
        <div class="flex justify-between items-center">
          <h2 class="text-2xl font-semibold text-gray-800">Translate Language File</h2>
          <button class="bg-gray-700 text-white px-4 py-2 rounded">Auto Translate</button>
        </div>
  
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div v-for="(value, key) in translations" :key="key">
            <label class="block text-sm font-medium text-gray-600 mb-1">{{ key }}</label>
            <input
              v-model="translations[key]"
              type="text"
              class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="Enter translation"
            />
          </div>
        </div>
  
        <div class="text-right pt-4">
          <button @click="saveTranslations" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">Save</button>
        </div>
      </div>
    </AuthenticatedLayout>
  </template>
  <script setup>
  import { onMounted, ref } from 'vue'
  import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
  import { usePage } from '@inertiajs/vue3'
  import axios from 'axios'
  
  const page = usePage()
  const id = page.props.id
  const type = page.props.type
  
  const translations = ref({})
  
  const fetchTranslations = async () => {
    try {
      const res = await axios.get(`/language/languageedit/${id}/${type}`)
      translations.value = res.data.data.enLabels || {}
    } catch (err) {
      console.error('Failed to load language data:', err)
    }
  }
  
  const saveTranslations = async () => {
    try {
      await axios.post(`/language/updatelanguageValues/${id}/${type}`, {
        values: Object.values(translations.value),
      })
      alert('Translations updated')
    } catch (err) {
      console.error('Save failed:', err)
    }
  }
  
  onMounted(fetchTranslations)
  </script>
  