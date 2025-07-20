<template>
  <div class="space-y-8">
    <!-- Entity Info Form -->
    <div class="bg-white shadow rounded-lg p-6">
      <h3 class="text-lg font-semibold text-gray-800 mb-4">Update Entity Info</h3>
      <form @submit.prevent="updateInfo" class="grid gap-4 md:grid-cols-2">
        <input v-model="form.name" placeholder="Entity Name" class="form-input" />
        <input v-model="form.email" placeholder="Email" class="form-input" />
        <input v-model="form.phone" placeholder="Phone" class="form-input" />
        <input v-model="form.contact" placeholder="Contact Person" class="form-input" />

        <div class="md:col-span-2">
          <label class="block mb-1 text-sm text-gray-600">Upload Document</label>
          <input type="file" @change="form.document = $event.target.files[0]" class="form-input" />
        </div>

        <div class="md:col-span-2 text-right">
          <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Save Changes</button>
        </div>
      </form>
    </div>

    <!-- Assignment Status -->
    <div class="bg-white shadow rounded-lg p-6">
      <h3 class="text-lg font-semibold text-gray-800 mb-4">Assignment Status</h3>
      <table class="w-full text-sm text-left text-gray-700">
        <thead class="bg-gray-100 text-xs uppercase text-gray-500">
          <tr>
            <th class="px-4 py-2">Title</th>
            <th class="px-4 py-2">Assigned Date</th>
            <th class="px-4 py-2">Status</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="task in assignedTasks"
            :key="task.id"
            class="border-b hover:bg-gray-50"
          >
            <td class="px-4 py-2 font-medium text-gray-800">{{ task.title }}</td>
            <td class="px-4 py-2">{{ task.date }}</td>
            <td class="px-4 py-2">
              <span
                :class="[ 'px-2 py-1 rounded text-xs font-semibold',
                  task.status === 'Accepted'
                    ? 'bg-green-100 text-green-600'
                    : task.status === 'Rejected'
                    ? 'bg-red-100 text-red-600'
                    : 'bg-yellow-100 text-yellow-600'
                ]"
              >
                {{ task.status }}
              </span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const form = ref({
  name: '',
  email: '',
  phone: '',
  contact: '',
  document: null
})

const updateInfo = () => {
  console.log('Submitting entity info:', form.value)
  // Future: call an API endpoint
}

const assignedTasks = ref([
  { id: 1, title: 'Task A', date: '2024-08-01', status: 'Pending' },
  { id: 2, title: 'Task B', date: '2024-08-02', status: 'Accepted' },
  { id: 3, title: 'Task C', date: '2024-08-05', status: 'Rejected' }
])
</script>

<style scoped>
.form-input {
  @apply w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500;
}
</style>
