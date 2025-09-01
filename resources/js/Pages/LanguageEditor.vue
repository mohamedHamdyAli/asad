<template>
  <AuthenticatedLayout>
    <div class="p-6 space-y-6">
      <div class="flex justify-between items-center">
        <h2 class="text-2xl font-semibold text-gray-800">Translate Language File</h2>
      </div>

      <div v-if="loadError" class="p-3 bg-red-50 text-red-600 rounded">{{ loadError }}</div>
      <div v-else-if="loading" class="text-sm text-gray-500">Loading…</div>

      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div v-for="key in keys" :key="key">
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
        <button
          :disabled="saving || loading"
          @click="saveTranslations"
          class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700 disabled:opacity-50"
        >
          {{ saving ? 'Saving…' : 'Save' }}
        </button>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import http from '@/lib/http'

const props = defineProps({
  id:   { type: [Number, String], required: true },
  type: { type: String, required: true },
})

const translations = ref({})
const loading = ref(false)
const saving = ref(false)
const loadError = ref('')

const fetchedOnce = ref(false)

const keys = computed(() => Object.keys(translations.value ?? {}))

async function fetchTranslations() {
  if (fetchedOnce.value) return
  fetchedOnce.value = true

  loading.value = true
  loadError.value = ''
  try {
    const { data } = await http.get(`/language/languageedit/${props.id}/${props.type}`)
    const payload = data?.data?.enLabels || {}
    translations.value = { ...payload }
  } catch (e) {
    console.error(e)
    loadError.value = 'Failed to load language data.'
  } finally {
    loading.value = false
  }
}

async function saveTranslations() {
  saving.value = true
  try {
    await http.post(`/api/language/updatelanguageValues/${props.id}/${props.type}`, {
      values: Object.values(translations.value),
    })
    alert('Translations updated')
  } catch (e) {
    console.error(e)
    alert('Save failed')
  } finally {
    saving.value = false
  }
}

onMounted(fetchTranslations)

watch(
  () => [props.id, props.type],
  () => {
    fetchedOnce.value = false
    fetchTranslations()
  }
)
</script>
