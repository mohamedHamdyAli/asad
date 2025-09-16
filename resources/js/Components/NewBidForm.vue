<template>
    <div class="bg-white rounded-2xl p-1 sm:p-2 w-full mx-auto">
      <form @submit.prevent="submitForm" class="grid grid-cols-1 sm:grid-cols-1 gap-2">
        <!-- Tender Title -->
        <input
          v-model="form.title"
          type="text"
          placeholder="Tender Title"
          class="form-input"
          required
        />
        <!-- Tender Type -->
        <select
          v-model="form.type"
          class="form-input"
          required
        >
          <option disabled value="">Select Tender Type</option>
          <option value="Government">Government</option>
          <option value="Private">Private</option>
        </select>

        <!-- Location -->
       <select
          v-model="form.location"
          class="form-input"
          required
        >
          <option disabled value="">Select Location Type</option>
          <option value="City">City</option>
          <option value="Area">Area</option>
        </select>
<!-- Tenderding Window Start -->
<div>
  <label class="block text-sm font-medium text-gray-500 mb-1">Bidding Window Start Date:</label>
  <input
    v-model="form.start_date"
    type="date"
    class="form-input w-full"
    required
  />
</div>

<!-- Tenderding Window End -->
<div >
  <label class="block text-sm font-medium text-gray-500 mb-1">Bidding Window End Date:</label>
  <input
    v-model="form.end_date"
    type="date"
    class="form-input w-full"
    required
  />
</div>

        <!-- Expected Revenue -->
        <input
          v-model="form.revenue"
          type="number"
          placeholder="Expected Revenue (SAR)"
          class="form-input"
          required
        />
    <input 
          v-on:change="form.document = $event.target.files[0]"
          type="file"
          class="form-input"
          required
        />
        <!-- special requirements -->
        <input
          v-model="form.special_requirements"
          type="text"
          placeholder="Special Requirements"
          class="form-input pb-2"
          required
        />
  
        <!-- Submit -->
        <div class="flex justify-center mt-2">
        <button
          type="submit"
          class="inline-flex items-center rounded-md border border-transparent bg-black px-2 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-gray-700 focus:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-gray-900" >
          Submit Tender Information
        </button>
        </div>
      </form>
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue'
  
  const form = ref({
    title: '',
    type: '',
    location: '',
    start_date: '',
    end_date: '',
    revenue: '',
    status: '',
    document: ''
  })
  
  const submitForm = () => {
    console.log('Submitting Tender:', form.value)
    // TODO: Send to backend API
  }
  </script>
  
  <style scoped>
  .form-input {
    @apply w-full border border-gray-300 rounded-lg py-3 px-2 text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition hover:border-gray-400;
  }
  </style>
  