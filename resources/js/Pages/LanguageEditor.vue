<template>
  <Head title="Language Editor" />

  <AuthenticatedLayout>
    <div class="p-6 space-y-6">
      <div class="flex justify-between items-center">
        <h2 class="text-2xl font-semibold text-dash-title">
          Translate {{ typeLabel }} Language File
        </h2>

        <!-- Type Switch Dropdown (NO @change) -->
        <select
          v-model="type"
          class="border border-gray-300 rounded px-3 py-1 text-sm"
        >
          <option v-for="t in editTypes" :key="t.type" :value="t.type">
            {{ t.label }}
          </option>
        </select>
      </div>

      <div v-if="loadError" class="p-3 bg-red-50 text-red-600 rounded">{{ loadError }}</div>
      <div v-if="loading" class="text-sm text-gray-500">Loading…</div>

      <!-- Keep the grid mounted so it doesn't disappear during in-flight requests -->
      <div v-show="keys.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div v-for="key in keys" :key="key">
          <!-- <label class="block text-sm font-medium text-gray-600 mb-1">{{ key }}</label> -->
         <label class="block text-sm font-medium text-gray-600 mb-1 whitespace-normal break-words line-clamp-2">
  {{ key }}
</label>

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
          class="bg-black text-white px-3 py-2 rounded hover:bg-gray-700 disabled:opacity-50"
        >
          {{ saving ? 'Saving…' : 'Save' }}
        </button>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted, computed, watch, nextTick } from 'vue'
import { Head } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import http from '@/lib/http' // axios instance

const props = defineProps({
  id: [Number, String],
  type: String,
})

const id = props.id
const type = ref(props.type || 'panel')

// State
const translations = ref({})
const loading = ref(false)
const saving = ref(false)
const loadError = ref('')
const initialized = ref(false)

// Abort the previous request if a new one starts
let currentController = null

// Dropdown options
const editTypes = [
  { type: 'panel',  label: 'Admin Panel' },
  { type: 'app',    label: 'User App' },
  { type: 'vendor', label: 'Vendor Dashboard' },
  { type: 'web',    label: 'Website' },
]

const typeLabel = computed(() => {
  const found = editTypes.find(t => t.type === type.value)
  return found ? found.label : 'Language'
})
const keys = computed(() => Object.keys(translations.value ?? {}))

// --- Fetch translations ---
async function fetchTranslations() {
  if (!id) {
    loadError.value = 'Missing language ID.'
    return
  }

  // Abort any in-flight request
  if (currentController) {
    try { currentController.abort() } catch (_) {}
  }
  currentController = new AbortController()

  // Set flags
  loading.value = true
  loadError.value = ''

  try {
    const res = await http.get(
      `/api/language/languageedit/${id}/${type.value}`,
      { signal: currentController.signal }
    )

    // Safety checks
    if (!res || !res.data || res.data.status !== 'success') {
      console.warn('Non-success response, keeping previous translations')
      return
    }

    const labels = res.data?.data?.enLabels
    if (labels && typeof labels === 'object' && Object.keys(labels).length > 0) {
      translations.value = labels
    } else {
      // Keep previous data; don’t blank the UI
      console.warn('Empty/invalid enLabels; keeping previous translations')
    }
  } catch (e) {
    // AbortError is expected when switching types fast
    if (e?.name === 'CanceledError' || e?.message?.includes('canceled')) {
      // silently ignore
    } else {
      console.error(e)
      loadError.value = 'Failed to load language data.'
      // IMPORTANT: do NOT clear translations on error
    }
  } finally {
    // Only clear loading if this is still the active controller
    if (currentController && !currentController.signal.aborted) {
      loading.value = false
      currentController = null
    } else {
      // If aborted, a new request already started; leave loading managed by that one
    }
  }
}

// --- Save translations ---
async function saveTranslations() {
  if (!id) return
  saving.value = true
  try {
    await http.post(`/api/language/updatelanguageValues/${id}/${type.value}`, {
      values: translations.value,
    })
    alert('Translations updated successfully.')
  } catch (e) {
    console.error(e)
    alert('Save failed.')
  } finally {
    saving.value = false
  }
}

// --- Initial load ---
onMounted(async () => {
  await nextTick()
  await fetchTranslations()
  initialized.value = true
})

// --- Refetch ONLY when user changes the dropdown ---
watch(type, async (newVal, oldVal) => {
  if (!initialized.value) return
  if (newVal !== oldVal) {
    await fetchTranslations()
  }
})
</script>
