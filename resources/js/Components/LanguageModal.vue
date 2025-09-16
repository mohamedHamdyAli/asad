<template>
    <div class="fixed inset-0 bg-black/30 z-50 flex items-center justify-center px-4">
      <div class="bg-white rounded-xl shadow-lg w-full max-w-5xl max-h-[90vh] overflow-y-auto p-6 relative">
        <h3 class="text-xl font-bold text-gray-800 mb-6">Add New Language</h3>
  
        <!-- Top Info -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">

          <input v-model="form.name" type="text" placeholder="Language Name (e.g. French)" class="form-input" />
          <input v-model="form.code" type="text" placeholder="Code (e.g. fr)" class="checkbox-form-input" />
          <select v-model="form.scope" class="form-input">
            <option disabled value="">Select App</option>
            <option value="user">User App</option>
            <option value="worker">Worker App</option>
          </select>
          <span class="text-sm text-gray-600">
          <input v-model="form.rtl" type="checkbox" placeholder="RTL" label="RTL" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
            RTL (Right to Left direction)
          </span>
        </div>
  
        <!-- Translation Sections -->
        <div class="space-y-6">
          <div v-for="(file, fileName) in translationFiles" :key="fileName" class="border rounded-lg p-4 bg-gray-50">
            <h4 class="text-md font-semibold mb-4 text-gray-700 border-b pb-2">{{ fileName }}.json</h4>
  
            <div class="space-y-3">
              <div v-for="(value, key) in file" :key="key" class="flex items-center gap-3">
                <label class="w-1/3 text-sm text-gray-600 truncate">{{ key }}</label>
                <input
                  v-model="translationFiles[fileName][key]"
                  type="text"
                  class="w-2/3 form-input"
                  placeholder="Translation"
                />
              </div>
            </div>
          </div>
        </div>
  
        <!-- Actions -->
        <div class="mt-8 flex justify-between">
          <button
            @click="$emit('close')"
            class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-md"
          >
            Cancel
          </button>
          <button
            @click="submitForm"
            class="inline-flex items-center rounded-md border border-transparent bg-black px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-gray-700 focus:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-gray-900" >
            Save Language
          </button>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue'
  
  const emit = defineEmits(['close'])
  
  const form = ref({
    name: '',
    code: '',
    scope: '',
    rtl: false,
  })
  
  // Example default structure
  const translationFiles = ref({
    auth: {
      login: '',
      register: '',
      logout: '',
    },
    validation: {
      required: '',
      email: '',
      min: '',
    },
    home: {
      welcome: '',
      description: '',
    },
  })
  
  const submitForm = () => {
    console.log('Language Meta:', form.value)
    console.log('Translation Files:', translationFiles.value)
    // TODO: Send data to API or store
    emit('close')
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
  