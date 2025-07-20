<template>
    <AuthenticatedLayout>
      <div class="p-2">
  
        <!-- Title + Add Button -->
        <div class="flex items-center justify-between space-y-4">
          <h2 class="text-2xl font-bold text-gray-800">Service Providers Companies (SPCs) Management</h2>
          <button @click="showModal = true"  class="inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-gray-700 focus:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-gray-900"
>
            + Add New SPC
          </button>
        </div>
  
        <!-- SPCs Table -->
        <div class="bg-white shadow rounded-lg p-2 overflow-x-auto mt-2">
          <table class="w-full text-sm text-left text-gray-600">
            <thead class="text-xs uppercase bg-gray-50 text-gray-500">
              <tr>
                <th class="px-4 py-2">Company Name</th>
                <th class="px-4 py-2">Admin Name</th>
                <th class="px-4 py-2">Admin Phone</th>
                <th class="px-4 py-2">Admin Email</th>
                <th class="px-4 py-2">Status</th>
                <th class="px-4 py-2 text-right">Actions</th>
              </tr>
            </thead>
  
            <tbody>
              <tr v-for="spc in spcs" :key="spc.id" class="border-b hover:bg-gray-50 transition">
                <td class="px-4 py-2 font-medium text-gray-800">{{ spc.name }}</td>
                <td class="px-4 py-2">{{ spc.admin_name }}</td>
                <td class="px-4 py-2">{{ spc.admin_phone }}</td>
                <td class="px-4 py-2">{{ spc.admin_email }}</td>
                <td class="px-4 py-2">
                  <span :class="['px-2 py-1 rounded text-xs font-semibold', spc.status === 'Active' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600']">
                    {{ spc.status }}
                  </span>
                </td>
                <td class="px-4 py-2 text-right space-x-2">
                  <button @click="viewSPC(spc)" class="text-indigo-600 hover:text-indigo-800" title="View Details">
                    <Icon icon="mdi:eye-outline" class="w-5 h-5" />
                  </button>
                  <button @click="editSPC(spc)" class="text-blue-600 hover:text-blue-800" title="Edit">
                    <Icon icon="mdi:pencil-outline" class="w-5 h-5" />
                  </button>
                  <button @click="toggleSPCStatus(spc)" :title="spc.status === 'Active' ? 'Deactivate' : 'Activate'"
                    :class="spc.status === 'Active' ? 'text-yellow-600 hover:text-yellow-800' : 'text-green-600 hover:text-green-800'">
                    <Icon :icon="spc.status === 'Active' ? 'mdi:block-helper' : 'mdi:check-circle-outline'" class="w-5 h-5" />
                  </button>
                  <button @click="deleteSPC(spc)" class="text-red-600 hover:text-red-800" title="Delete">
                    <Icon icon="mdi:trash-can-outline" class="w-5 h-5" />
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
          <div class="mt-4 mb-2 flex justify-end items-center space-x-2 text-sm text-gray-600">
        <button
          @click="currentPage--"
          :disabled="currentPage === 1"
          class="px-3 py-1 border rounded hover:bg-gray-100 disabled:opacity-50"
        >
          Prev
        </button>
        <span>Page {{ currentPage }} of {{ totalPages }}</span>
        <button
          @click="currentPage++"
          :disabled="currentPage === totalPages"
          class="px-3 py-1 border rounded hover:bg-gray-100 disabled:opacity-50"
        >
          Next
        </button>
      </div>

        </div>
  
        <!-- Modal -->
        <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
          <div class="bg-white rounded-2xl shadow-lg w-full max-w-2xl p-2 relative">
            
            <!-- Close Button -->
            <button @click="showModal = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
              <Icon icon="mdi:close" class="w-6 h-6" />
            </button>
  
            <!-- Form Inside Modal -->
            <h3 class="text-xl font-bold text-gray-700 mt-2 mb-2 text-center">Register New Service Provider Company (SPC)</h3>
            <NewSPCForm />
  
          </div>
        </div>
  
      </div>
    </AuthenticatedLayout>
  </template>
  
  <script setup>
  import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
  import { Icon } from '@iconify/vue'
  import { ref } from 'vue'
  import NewSPCForm from '@/Components/NewSPCForm.vue'
  
  const showModal = ref(false)
  
  const spcs = [
    { id: 1, name: 'ShinePro', admin_name: 'Sara Khaled', admin_phone: '+20123456789', admin_email: 'sara.k@shinepro.com', status: 'Active' },
    { id: 2, name: 'Ahmed Saber', admin_name: 'Ahmed Saber', admin_phone: '+20111222333', admin_email: 'ahmed.saber@email.com', status: 'Inactive' },
    { id: 3, name: 'AutoCare Egypt', admin_name: 'Mona Ashraf', admin_phone: '+20112233445', admin_email: 'mona.ashraf@autocare.com', status: 'Active' }
  ]
  
  const viewSPC = (spc) => {
    console.log('Viewing SPC details:', spc.name)
  }
  
  const editSPC = (spc) => {
    console.log('Editing SPC:', spc.name)
  }
  
  const toggleSPCStatus = (spc) => {
    console.log('Toggling SPC status:', spc.name)
  }
  
  const deleteSPC = (spc) => {
    if (confirm(`Are you sure you want to delete "${spc.name}"?`)) {
      console.log('Deleting SPC:', spc.name)
    }
  }

  const currentPage = ref(1)
  const itemsPerPage = 15
  const totalPages = Math.ceil(spcs.length / itemsPerPage)

  const startIndex = (currentPage.value - 1) * itemsPerPage
  const endIndex = startIndex + itemsPerPage 

  const filteredSPCs = spcs.slice(startIndex, endIndex)
  </script>
  