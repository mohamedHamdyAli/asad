<template>
        <Head title="User Management" />

    <AuthenticatedLayout>
    <div class="bg-white p-6 rounded-lg shadow">
      <!-- Header -->
      <h2 class="text-2xl font-semibold text-white mb-4">User Management</h2>
  
      <!-- Tabs -->
      <div class="flex space-x-4 border-b mb-4">
        <button
          v-for="tab in tabs"
          :key="tab"
          @click="activeTab = tab"
          class="pb-2 text-sm font-medium border-b-2 transition"
          :class="{
            'border-blue-600 text-blue-600': activeTab === tab,
            'border-transparent text-gray-500 hover:text-blue-600 hover:border-blue-300': activeTab !== tab
          }"
        >
          {{ tabLabels[tab] }}
        </button>
      </div>
  
      <!-- Tab Content -->
      <div v-if="filteredUsers.length" class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-600">
          <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
            <tr>
              <th class="px-4 py-2">Name</th>
              <th class="px-4 py-2">Email</th>
              <th v-if="activeTab === 'spc_admin' || activeTab === 'employee'" class="px-4 py-2">SPC</th>
              <th class="px-4 py-2">Status</th>
              <th class="px-4 py-2 text-right">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="user in filteredUsers" :key="user.id" class="border-b hover:bg-gray-50 transition">
              <td class="px-4 py-2 font-medium text-gray-800">{{ user.name }}</td>
              <td class="px-4 py-2">{{ user.email }}</td>
              <td v-if="activeTab === 'spc_admin' || activeTab === 'employee'" class="px-4 py-2">
                {{ user.spc || 'N/A' }}
              </td>
              <td class="px-4 py-2">
                <span :class="[
                  'px-2 py-1 rounded text-xs font-semibold',
                  user.status === 'Active' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600'
                ]">
                  {{ user.status }}
                </span>
              </td>
        <td class="px-4 py-2 text-right space-x-2">
        <!-- Only allow editing if NOT in super_admin tab OR if the user is self -->
        <button
            v-if="activeTab !== 'super_admin' || user.id === loggedInUserId"
            @click="editUser(user)"
            class="text-blue-600 hover:text-blue-800"
            title="Edit"
        >
            <Icon icon="mdi:pencil-outline" class="w-5 h-5" />
        </button>

        <button
            v-if="activeTab !== 'super_admin' || user.id === loggedInUserId"
            @click="toggleUser(user)"
            class="text-yellow-600 hover:text-yellow-800"
            title="Toggle Status"
        >
            <Icon icon="mdi:account-lock-outline" class="w-5 h-5" />
        </button>

        <button
            v-if="activeTab !== 'super_admin' || user.id === loggedInUserId"
            @click="deleteUser(user)"
            class="text-red-600 hover:text-red-800"
            title="Delete"
        >
            <Icon icon="mdi:delete-outline" class="w-5 h-5" />
        </button>
        </td>

              <!-- <td class="px-4 py-2 text-right space-x-2">
                <button @click="editUser(user)" class="text-blue-600 hover:text-blue-800" title="Edit">
                  <Icon icon="mdi:pencil-outline" class="w-5 h-5" />
                </button>
                <button @click="toggleUser(user)" class="text-yellow-600 hover:text-yellow-800" title="Toggle Status">
                  <Icon icon="mdi:account-lock-outline" class="w-5 h-5" />
                </button>
                <button @click="deleteUser(user)" class="text-red-600 hover:text-red-800" title="Delete">
                  <Icon icon="mdi:delete-outline" class="w-5 h-5" />
                </button>
              </td> -->
            </tr>
          </tbody>
        </table>
        <div class="mt-4 flex justify-end items-center space-x-2 text-sm text-gray-600">
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
      <div v-else class="text-gray-500 text-sm mt-6">No users found for this category.</div>
    </div>
  </AuthenticatedLayout>
  </template>
  
  <script setup>
  import { ref, computed } from 'vue'
  import { Icon } from '@iconify/vue'
  import { Head } from '@inertiajs/vue3'
  import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
  
  const tabs = ['super_admin', 'admin', 'spc_admin', 'employee']
  const tabLabels = {
    super_admin: 'Super Admins',
    admin: 'Admins',
    spc_admin: 'SPC Admins',
    employee: 'Employees'
  }
  
  const activeTab = ref('super_admin')
  
  // Mocked user data (replace with API data)
  const loggedInUserId = 4 

  const users = ref([
    { id: 1, name: 'Mohamed', email: 'm@m.com', role: 'admin', status: 'Active' },
    { id: 2, name: 'Laila', email: 'l@spc.com', role: 'spc_admin', status: 'Active', spc: 'AutoCare' },
    { id: 3, name: 'Zayan', email: 'z@emp.com', role: 'employee', status: 'Inactive', spc: 'Sparkle Valet' },
    { id: 4, name: 'Sara', email: 's@root.com', role: 'super_admin', status: 'Active' },
    { id: 5, name: 'Ali', email: 's@root.com', role: 'super_admin', status: 'Active' },
    { id: 6, name: 'Saleh', email: 's@root.com', role: 'super_admin', status: 'Active' },
  ])
  
//   const filteredUsers = computed(() =>
//     users.value.filter(user => user.role === activeTab.value)
//   )
  
  const editUser = (user) => {
    console.log('Edit:', user)
  }
  const toggleUser = (user) => {
    user.status = user.status === 'Active' ? 'Inactive' : 'Active'
  }
  const deleteUser = (user) => {
    if (confirm(`Are you sure you want to delete "${user.name}"?`)) {
      users.value = users.value.filter(u => u.id !== user.id)
    }
  }

  const currentPage = ref(1)
const perPage = 15

const filteredUsers = computed(() => {
  const filtered = users.value.filter(user => user.role === activeTab.value)
  const start = (currentPage.value - 1) * perPage
  const end = start + perPage
  return filtered.slice(start, end)
})

const totalPages = computed(() => {
  const count = users.value.filter(user => user.role === activeTab.value).length
  return Math.ceil(count / perPage)
})

  </script>
  