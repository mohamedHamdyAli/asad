<template>
  <div class="space-y-6">
    <!-- Filters -->
    <div class="flex flex-wrap justify-between items-center gap-4">
      <input
        v-model="search"
        type="text"
        placeholder="Search by requester or category"
        class="form-input w-full md:w-1/3"
      />
      <select v-model="statusFilter" class="form-input w-full md:w-1/4">
        <option value="">All Statuses</option>
        <option value="Pending">Pending</option>
        <option value="In Progress">In Progress</option>
        <option value="Completed">Completed</option>
      </select>
    </div>

    <!-- Requests Table -->
    <div class="bg-white shadow rounded-lg overflow-x-auto">
      <table class="w-full text-sm text-left text-gray-600">
        <thead class="bg-gray-100 text-xs uppercase text-gray-500">
          <tr>
            <th class="px-4 py-2">Request ID</th>
            <th class="px-4 py-2">Requester</th>
            <th class="px-4 py-2">Category</th>
            <th class="px-4 py-2">Date</th>
            <th class="px-4 py-2">Status</th>
            <th class="px-4 py-2 text-right">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="request in filteredRequests"
            :key="request.id"
            class="border-b hover:bg-gray-50 transition"
          >
            <td class="px-4 py-2 font-medium text-gray-800">{{ request.id }}</td>
            <td class="px-4 py-2">{{ request.requester }}</td>
            <td class="px-4 py-2">{{ request.category }}</td>
            <td class="px-4 py-2">{{ request.date }}</td>
            <td class="px-4 py-2">
              <span
                :class="[
                  'px-2 py-1 rounded text-xs font-semibold',
                  statusColor(request.status)
                ]"
              >
                {{ request.status }}
              </span>
            </td>
            <td class="px-4 py-2 text-right space-x-2">
              <button @click="viewRequest(request)" class="text-blue-600 hover:text-blue-800" title="View">
                <Icon icon="mdi:eye-outline" class="w-5 h-5" />
              </button>
              <button
                v-if="request.status !== 'Completed'"
                @click="markCompleted(request)"
                class="text-green-600 hover:text-green-800"
                title="Mark as Complete"
              >
                <Icon icon="mdi:check-circle-outline" class="w-5 h-5" />
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Icon } from '@iconify/vue'

const props = defineProps({
  entityId: Number
})

const search = ref('')
const statusFilter = ref('')

// Dummy request data
const requests = ref([
  {
    id: 'REQ001',
    requester: 'Ali Hussein',
    category: 'Pickup',
    date: '2024-08-01',
    status: 'Pending'
  },
  {
    id: 'REQ002',
    requester: 'Mona Saleh',
    category: 'Consultation',
    date: '2024-08-02',
    status: 'In Progress'
  },
  {
    id: 'REQ003',
    requester: 'Yasser Kamal',
    category: 'Delivery',
    date: '2024-08-03',
    status: 'Completed'
  }
])

const filteredRequests = computed(() => {
  return requests.value.filter(r =>
    (!statusFilter.value || r.status === statusFilter.value) &&
    (!search.value ||
      r.requester.toLowerCase().includes(search.value.toLowerCase()) ||
      r.category.toLowerCase().includes(search.value.toLowerCase()))
  )
})

const markCompleted = (request) => {
  request.status = 'Completed'
}

const viewRequest = (request) => {
  console.log('Viewing request:', request.id)
  // Could open a modal or navigate
}

const statusColor = (status) => {
  switch (status) {
    case 'Pending':
      return 'bg-yellow-100 text-yellow-700'
    case 'In Progress':
      return 'bg-blue-100 text-blue-700'
    case 'Completed':
      return 'bg-green-100 text-green-700'
    default:
      return 'bg-gray-100 text-gray-500'
  }
}
</script>

<style scoped>
.form-input {
  @apply border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500;
}
</style>
