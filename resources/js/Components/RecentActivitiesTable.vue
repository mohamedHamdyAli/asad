<template>
    <div class="bg-white shadow rounded-lg p-6 overflow-x-auto">
      <h2 class="text-lg font-semibold text-gray-700 mb-4">Recent Service Requests</h2>
      <table class="w-full text-sm text-left text-gray-600">
        <thead class="text-xs uppercase bg-gray-50 text-gray-500">
          <tr>
            <th class="px-4 py-2">Request ID</th>
            <th class="px-4 py-2">Client</th>
            <th class="px-4 py-2">SPC</th>
            <th class="px-4 py-2">Service</th>
            <th class="px-4 py-2">Status</th>
            <th class="px-4 py-2">Date</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="request in requests"
            :key="request.id"
            class="border-b hover:bg-gray-50 transition"
          >
            <td class="px-4 py-2 font-medium text-gray-800">{{ request.id }}</td>
            <td class="px-4 py-2">{{ request.client }}</td>
            <td class="px-4 py-2">{{ request.spc }}</td>
            <td class="px-4 py-2">{{ request.service }}</td>
            <td class="px-4 py-2">
              <span
                :class="[
                  'px-2 py-1 rounded text-xs font-semibold',
                  request.status === 'Completed' ? 'bg-green-100 text-green-600' :
                  request.status === 'Pending' ? 'bg-yellow-100 text-yellow-600' :
                  'bg-red-100 text-red-600'
                ]"
              >
                {{ request.status }}
              </span>
            </td>
            <td class="px-4 py-2">{{ request.date }}</td>
          </tr>
        </tbody>
      </table>
      <div class="mt-4 flex justify-center">
        <button class="inline-flex items-center rounded-md border border-transparent bg-black px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-gray-700 focus:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-gray-900">View All</button>
      </div>
    </div>
  </template>
  
  <script setup>
  const requests = [
    { id: '#SR-1205', client: 'Omar S.', spc: 'ShinePro', service: 'Valet', status: 'Completed', date: '24 Apr 2025' },
    { id: '#SR-1206', client: 'Mariam A.', spc: 'AutoCare', service: 'Washing', status: 'Pending', date: '25 Apr 2025' },
    { id: '#SR-1207', client: 'Kareem B.', spc: 'Sparkle', service: 'Cover', status: 'Cancelled', date: '26 Apr 2025' },
    { id: '#SR-1208', client: 'Sara M.', spc: 'ShinePro', service: 'Valet', status: 'Completed', date: '26 Apr 2025' }
  ]
  </script>
  