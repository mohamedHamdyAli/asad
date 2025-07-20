<template>
  <div class="bg-white shadow rounded-lg p-6 overflow-x-auto">
    <h2 class="text-lg font-semibold text-gray-700 mb-4">Entities</h2>
    <table class="w-full text-sm text-left text-gray-600">
      <thead class="text-xs uppercase bg-gray-50 text-gray-500">
        <tr>
          <th class="px-4 py-2">Name</th>
          <th class="px-4 py-2">Contact Person</th>
          <th class="px-4 py-2">Phone</th>
          <th class="px-4 py-2">Email</th>
          <th class="px-4 py-2">Status</th>
          <th class="px-4 py-2 text-right">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="entity in entities"
          :key="entity.id"
          class="border-b hover:bg-gray-50 transition"
        >
          <td class="px-4 py-2 font-medium text-gray-800">{{ entity.name }}</td>
          <td class="px-4 py-2">{{ entity.contactPerson }}</td>
          <td class="px-4 py-2">{{ entity.phone }}</td>
          <td class="px-4 py-2">{{ entity.email }}</td>
          <td class="px-4 py-2">
            <span
              :class="[
                'px-2 py-1 rounded text-xs font-semibold',
                entity.status === 'Active'
                  ? 'bg-green-100 text-green-600'
                  : 'bg-red-100 text-red-600'
              ]"
            >
              {{ entity.status }}
            </span>
          </td>
          <td class="px-4 py-2 text-right space-x-3">
            <button @click="editEntity(entity)" class="text-blue-600 hover:text-blue-800" title="Edit">
              <Icon icon="mdi:pencil-outline" class="w-5 h-5" />
            </button>
            <button
              @click="toggleEntityStatus(entity)"
              :title="entity.status === 'Active' ? 'Disable' : 'Activate'"
              :class="entity.status === 'Active'
                ? 'text-yellow-600 hover:text-yellow-800'
                : 'text-green-600 hover:text-green-800'"
            >
              <Icon :icon="entity.status === 'Active' ? 'mdi:block-helper' : 'mdi:check-circle-outline'" class="w-5 h-5" />
            </button>
            <button @click="deleteEntity(entity)" class="text-red-600 hover:text-red-800" title="Delete">
              <Icon icon="mdi:trash-can-outline" class="w-5 h-5" />
            </button>
          </td>
        </tr>
      </tbody>
    </table>
    <div class="mt-4 flex justify-center">
      <button
        class="inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-gray-700 focus:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-gray-900"
      >
        View All
      </button>
    </div>
  </div>
</template>

<script setup>
import { Icon } from '@iconify/vue'

const entities = [
  { id: 1, name: 'ShinePro', contactPerson: 'Ahmed S.', phone: '+20123456789', email: 'contact@shinepro.com', status: 'Active' },
  { id: 2, name: 'AutoCare', contactPerson: 'Mona A.', phone: '+20122334455', email: 'info@autocare.com', status: 'Inactive' },
  { id: 3, name: 'Sparkle Valet', contactPerson: 'Youssef B.', phone: '+20111223344', email: 'service@sparkle.com', status: 'Active' }
]

const editEntity = (entity) => {
  console.log('Editing Entity:', entity.name)
  // TODO: open edit modal or navigate to edit view
}

const toggleEntityStatus = (entity) => {
  console.log('Toggling Status for:', entity.name)
  // TODO: update status via API
}

const deleteEntity = (entity) => {
  if (confirm(`Are you sure you want to delete "${entity.name}"?`)) {
    console.log('Deleting Entity:', entity.name)
    // TODO: delete from server
  }
}
</script>
