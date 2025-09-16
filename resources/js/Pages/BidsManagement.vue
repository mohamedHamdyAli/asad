<template>
    <AuthenticatedLayout>
      <div class="p-4">
  
        <!-- Top Header + Add Bid Button -->
        <div class="flex items-center justify-between space-y-4">
          <h2 class="text-2xl font-bold text-gray-800">Bids Management</h2>
          <button @click="showModal = true"  class="inline-flex items-center rounded-md border border-transparent bg-black px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-gray-700 focus:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-gray-900">
            + Add New Bid
          </button>
        </div>
  
        <!-- Bids Table -->
        <div class="bg-white shadow rounded-lg overflow-x-auto mt-2">
          <table class="w-full text-sm text-left text-gray-600">
            <thead class="text-xs uppercase bg-gray-50 text-gray-500">
              <tr>
                <th class="px-4 py-2">Title</th>
                <th class="px-4 py-2">Type</th>
                <th class="px-4 py-2">Location</th>
                <th class="px-4 py-2">Bidding Window</th>
                <th class="px-4 py-2">Expected Revenue</th>
                <th class="px-4 py-2">Status</th>
                <th class="px-4 py-2 text-right">Actions</th>
              </tr>
            </thead>
  
            <tbody>
              <tr v-for="bid in bids" :key="bid.id" class="border-b hover:bg-gray-50 transition">
                <td class="px-4 py-2 font-medium text-gray-800">{{ bid.title }}</td>
                <td class="px-4 py-2">{{ bid.type }}</td>
                <td class="px-4 py-2">{{ bid.location }}</td>
                <td class="px-4 py-2">{{ bid.bidding_window }}</td>
                <td class="px-4 py-2">{{ bid.revenue }}</td>
                <td class="px-4 py-2">
                  <span :class="['px-2 py-1 rounded text-xs font-semibold', bid.status === 'Open' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600']">
                    {{ bid.status }}
                  </span>
                </td>
                <td class="px-4 py-2 text-right space-x-2">
                  <button @click="viewBid(bid)" class="text-indigo-600 hover:text-indigo-800" title="View Details">
                    <Icon icon="mdi:eye-outline" class="w-5 h-5" />
                  </button>
                  <button @click="editBid(bid)" class="text-blue-600 hover:text-blue-800" title="Edit">
                    <Icon icon="mdi:pencil-outline" class="w-5 h-5" />
                  </button>
                  <button @click="deleteBid(bid)" class="text-red-600 hover:text-red-800" title="Delete">
                    <Icon icon="mdi:trash-can-outline" class="w-5 h-5" />
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
          <div class="mt-4 mb-4 flex justify-end items-center space-x-2 text-sm text-gray-600">
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
            <div class="bg-white rounded-2xl shadow-lg w-full max-w-2xl px-2 py-2 relative">
            
            <!-- Close Button -->
            <button @click="showModal = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition duration-200">
              <Icon icon="mdi:close" class="w-6 h-6" />
            </button>
  
            <!-- Modal Title -->
            <h3 class="text-2xl font-bold text-center text-gray-800 mb-2 mt-2">Add New Bid</h3>
  
            <!-- NewBidForm Component -->
            <NewBidForm />
          </div>
        </div>
  
      </div>
    </AuthenticatedLayout>
  </template>
  
  <script setup>
  import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
  import { Icon } from '@iconify/vue'
  import { ref } from 'vue'
  import NewBidForm from '@/Components/NewBidForm.vue'
  
  const showModal = ref(false)
  
  const bids = [
    { id: 1, title: 'New Cairo Parking', type: 'Government', location: 'New Cairo', bidding_window: '12 Apr - 28 Apr', revenue: 'EGP 250,000', status: 'Open' },
    { id: 2, title: 'Zayed Smart Valet', type: 'Private', location: 'Sheikh Zayed', bidding_window: '10 Apr - 25 Apr', revenue: 'EGP 180,000', status: 'Closed' },
  ]
  
  const viewBid = (bid) => {
    console.log('Viewing Bid:', bid.title)
  }
  
  const editBid = (bid) => {
    console.log('Editing Bid:', bid.title)
  }
  
  const deleteBid = (bid) => {
    if (confirm(`Are you sure you want to delete "${bid.title}"?`)) {
      console.log('Deleting Bid:', bid.title)
    }
  }

  const currentPage = ref(1)
  const pageSize = 15
  const totalPages = Math.ceil(bids.length / pageSize)

  const startIndex = (currentPage.value - 1) * pageSize
  const endIndex = startIndex + pageSize

  const filteredBids = bids.slice(startIndex, endIndex)
  </script>
  