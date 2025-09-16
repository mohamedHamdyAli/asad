<template>
    <div class="bg-white shadow rounded-lg p-6 overflow-x-auto">
      <h2 class="text-lg font-semibold text-gray-700 mb-4"> Active Tenders</h2>
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
          <tr v-for="tender in tenders" :key="tender.id" class="border-b hover:bg-gray-50 transition">
            <td class="px-4 py-2 font-medium text-gray-800">{{ tender.title }}</td>
            <td class="px-4 py-2">
              <span :class="tender.type === 'Government' ? 'text-green-600' : 'text-blue-600'">
                {{ tender.type }}
              </span>
            </td>
            <td class="px-4 py-2">{{ tender.location }}</td>
            <td class="px-4 py-2">{{ tender.window }}</td>
            <td class="px-4 py-2 font-medium">{{ tender.revenue }}</td>
            <td class="px-4 py-2">
              <span
                :class="[
                  'px-2 py-1 rounded text-xs font-semibold',
                  tender.status === 'Open' ? 'bg-green-100 text-green-600' : 'bg-gray-200 text-gray-500'
                ]"
              >
                {{ tender.status }}
              </span>
            </td>
            <td class="px-4 py-2 text-right space-x-3">
                <button @click="editTender(tender)" class="text-blue-600 hover:text-blue-800" title="Edit">
                <Icon icon="mdi:pencil-outline" class="w-5 h-5" />
              </button>
              <button @click="approveTender(tender)" class="text-green-600 hover:text-green-800" title="Approve">
                <Icon icon="mdi:check-circle-outline" class="w-5 h-5" />
              </button>
              <button @click="deleteTender(tender)" class="text-red-600 hover:text-red-800" title="Delete">
                <Icon icon="mdi:trash-can-outline" class="w-5 h-5" />
              </button>
            </td>
          </tr>
        </tbody>
      </table>
      <div class="mt-4 flex justify-center">
        <button class="inline-flex items-center rounded-md border border-transparent bg-black px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-gray-700 focus:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-gray-900">View All</button>
      </div>
    </div>
  </template>
  
  <script setup>
  import { Icon } from '@iconify/vue'
  
  const tenders = [
    {
      id: 1,
      title: 'New Cairo Parking',
      type: 'Government',
      location: 'New Cairo',
      window: '12 Apr – 28 Apr',
      revenue: 'EGP 250,000',
      status: 'Open'
    },
    {
      id: 2,
      title: 'Zayed Smart Valet',
      type: 'Private',
      location: 'Sheikh Zayed',
      window: '10 Apr – 25 Apr',
      revenue: 'EGP 180,000',
      status: 'Closed'
    },
    {
      id: 3,
      title: 'Mall Garage Tender',
      type: 'Private',
      location: 'Nasr City',
      window: '01 May – 15 May',
      revenue: 'EGP 210,000',
      status: 'Open'
    }
  ]
  
  const approveTender = (tender) => {
    console.log('Approving tender:', tender.title)
  }
  
  const editTender = (tender) => {
    console.log('Editing tender:', tender.title)
  }
  
  const deleteTender = (tender) => {
    if (confirm(`Are you sure you want to delete "${tender.title}"?`)) {
      console.log('Deleting tender:', tender.title)
    }
  }
  </script>
  