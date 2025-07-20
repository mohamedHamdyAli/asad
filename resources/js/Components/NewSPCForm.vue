<template>
    <div class=" p-2 max-w-3xl mx-auto space-y-2">
      <!-- <h2 class="text-2xl font-bold text-gray-800 mb-8 text-center">Register New Service Provider (SPC)</h2> -->
  
      <form @submit.prevent="submitForm" class="space-y-2 mx-2 my-2">
           <!-- Vendor Type -->
           <div>
          <select
            v-model="form.type"
            class="w-full border border-gray-300 rounded-lg shadow-sm py-2.5 px-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            required
          >
            <option disabled value="">Select Vendor Type</option>
            <option value="Company">Company</option>
            <option value="Individual">Individual</option>
          </select>
        </div>
  
        <!-- SPC Name -->
        <div  >
          <input
            v-model="form.spc_name"
            type="text"
            placeholder="Company or Individual Name"
            class="w-full border border-gray-300 rounded-lg shadow-sm py-2.5 px-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            required
          />
        </div>
  
        <!-- Contact Name -->
        <div  >
          <input
            v-model="form.contact_name"
            type="text"
            placeholder="Contact Person Name"
            class="w-full border border-gray-300 rounded-lg shadow-sm py-2.5 px-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            required
          />
        </div>
  
        <!-- Contact Email -->
        <div  >
          <input
            v-model="form.contact_email"
            type="email"
            placeholder="Contact Email Address"
            class="w-full border border-gray-300 rounded-lg shadow-sm py-2.5 px-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            required
          />
        </div>
  
        <!-- Contact Phone -->
        <div>
          <input
            v-model="form.contact_phone"
            type="text"
            placeholder="Contact Phone Number"
            class="w-full border border-gray-300 rounded-lg shadow-sm py-2.5 px-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            required
          />
        </div>
  
     
        <!-- Password -->
        <div>
          <input
            v-model="form.password"
            type="password"
            placeholder="Password"
            class="w-full border border-gray-300 rounded-lg shadow-sm py-2.5 px-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            required
          />
        </div>
  
        <!-- Confirm Password -->
        <div>
          <input
            v-model="form.password_confirmation"
            type="password"
            placeholder="Confirm Password"
            class="w-full border border-gray-300 rounded-lg shadow-sm py-2.5 px-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            required
          />
        </div>
        <div>
          <span
          ><p class="block text-sm font-medium text-gray-600 mb-2 mb-1">Upload Privacy Statement & Terms of Use Documents:</p>
          <input
            v-on:change="form.document = $event.target.files[0]"
            type="file"
            placeholder="Privacy Policy Document"
            class="w-full border border-gray-300 rounded-lg shadow-sm py-2.5 px-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            required
          />
          </span>
        </div>
  
        <!-- Submit -->
        <div class="pt-2 mb-2 flex items-center justify-center">
  <button
    type="submit"
    class="inline-flex items-center rounded-md border border-transparent bg-gray-800 px-2 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-gray-700 focus:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-gray-900"  >
    Register Service Provider
  </button>
</div>

      </form>
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue'
  
  const form = ref({
    spc_name: '',
    contact_name: '',
    contact_email: '',
    contact_phone: '',
    type: '',
    password: '',
    password_confirmation: ''
  })
  
  const submitForm = () => {
    if (form.value.password !== form.value.password_confirmation) {
      alert('Passwords do not match!')
      return
    }
  
    console.log('Submitting New SPC Form:', form.value)
    // TODO: Send to backend API
  }
  </script>
  