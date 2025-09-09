<template>
    <header class="w-full bg-white shadow-sm border-b border-gray-200 px-4 sm:px-6 lg:px-8">
      <div class="h-16 flex justify-between items-center">
        <!-- Left side: Search + title -->
        <div class="flex items-center space-x-6">
          <!-- <div class="text-sm text-gray-500 hidden md:block">
            <span class="text-gray-400">Search</span> <kbd class="text-xs bg-gray-100 px-1 py-0.5 rounded">ctrl+k</kbd>
          </div> -->
  
   <!-- Sample Menu Dropdown -->
<div class="relative" @click="toggleSampleMenu">

  <button class="flex items-center space-x-2 text-blue-600 hover:text-blue-800 focus:outline-none text-sm font-medium">
    <Icon icon="mdi:menu" class="w-5 h-5" />
    <span>Sample menu</span>
    <Icon icon="mdi:chevron-down" class="w-4 h-4" />
  </button>
<!-- tempo pause sample menu -->
  <!-- <div v-if="sampleMenuOpen" class="absolute left-0 mt-2 w-44 bg-white border rounded shadow z-50">
    <div class="flex items-center px-4 py-2 text-sm hover:bg-gray-100 cursor-pointer">
      <Icon icon="mdi:clock-outline" class="me-2 w-4 text-subtitle-2" />
<NavLink :href="route('dashboard')">Dashboard</NavLink>
    </div>
    <div class="flex items-center px-4 py-2 text-sm hover:bg-gray-100 cursor-pointer">
      <Icon icon="mdi:cloud-outline" class="me-2 w-4 h-4" />
<NavLink :href="route('finance-management')">Reports</NavLink>
    </div>
        <div class="flex items-center px-4 py-2 text-sm hover:bg-gray-100 cursor-pointer">
      <Icon icon="mdi:cloud-outline" class="me-2 w-4 h-4" />
<NavLink :href="route('bids-management')">Bids Management</NavLink>
    </div>
        <div class="flex items-center px-4 py-2 text-sm hover:bg-gray-100 cursor-pointer">
      <Icon icon="mdi:cloud-outline" class="me-2 w-4 h-4" />
<NavLink :href="route('vendors-management')">Vendors Managemnt</NavLink>
    </div>
        <div class="flex items-center px-4 py-2 text-sm hover:bg-gray-100 cursor-pointer">
      <Icon icon="mdi:cloud-outline" class="me-2 w-4 h-4" />
<NavLink :href="route('intro-management')">App Intro</NavLink>
    </div>
            <div class="flex items-center px-4 py-2 text-sm hover:bg-gray-100 cursor-pointer">
      <Icon icon="mdi:cloud-outline" class="me-2 w-4 h-4" />
<NavLink :href="route('language-management')">Language Management</NavLink>
    </div>
            <div class="flex items-center px-4 py-2 text-sm hover:bg-gray-100 cursor-pointer">
      <Icon icon="mdi:cloud-outline" class="me-2 w-4 h-4" />
<NavLink :href="route('users-management')">User Management</NavLink>
    </div>
  </div> -->
</div>
  <!-- <NavItem icon="mdi:table-large" label="Reports" :to="route('finance-management')" :href="route('finance-management')" />
                <NavItem icon="mdi:view-dashboard-outline" label="Bids" :to="route('bids-management')" :href="route('bids-management')" />
                <NavItem icon="mdi:responsive" label="SPCs" :to="route('spc-management')" :href="route('spc-management')" />
                <NavItem icon="mdi:file-document-edit-outline" label="App Intro" :to="route('intro-management')" :href="route('intro-management')" />             
                <NavItem icon="mdi:world" label="Languages" :to="route('language-management')" :href="route('language-management')" />
                <NavItem icon="mdi:people" label="Manage Users" :to="route('users-management')" :href="route('users-management')" /> -->
        </div>
  
        <!-- Right side: Icons + profile -->
        <div class="flex items-center space-x-4">
          <Icon icon="mdi:bell-outline" class="w-5 h-5 text-gray-500 hover:text-gray-700 cursor-pointer" />
  
          <!-- Profile Dropdown -->
          <div class="relative" @click="toggleDropdown">
            <button class="flex items-center space-x-2 text-gray-800 hover:text-gray-900 focus:outline-none">
              <img
                src="https://i.pravatar.cc/32"
                alt="Avatar"
                class="w-8 h-8 rounded-full object-cover"
              />
              <span class="text-blue-600 font-medium">{{ user.name }}</span>
              <Icon icon="mdi:chevron-down" class="w-4 h-4" />
            </button>
  
            <div
              v-if="dropdownOpen"
              class="absolute right-0 mt-2 w-48 bg-white border rounded shadow z-50"
            >
              <Link :href="route('profile.edit')" class="flex items-center px-4 py-2 text-sm hover:bg-gray-100">
                <Icon icon="mdi:account" class="me-2 w-4 h-4" />
                My Profile
              </Link>
              <div class="border-t my-1"></div>
              <Link :href="route('logout')" method="post" as="button" class="block w-full text-left px-4 py-2 text-sm hover:bg-gray-100">
                Logout
              </Link>
            </div>
          </div>
        </div>
      </div>
    </header>
  </template>
  
  <script setup>
  import { onMounted , ref } from 'vue'
  import { Link } from '@inertiajs/vue3'
  import { Icon } from '@iconify/vue'
  import NavLink from './NavLink.vue'
import NavItem from './NavItem.vue'
  
  defineProps({
    user: Object,
  })
  
  const dropdownOpen = ref(false)
  const toggleDropdown = () => {
    dropdownOpen.value = !dropdownOpen.value
  }

  const sampleMenuOpen = ref(false)
const toggleSampleMenu = () => {
  sampleMenuOpen.value = !sampleMenuOpen.value
}

const isMobile = ref(false)

onMounted(() => {
    const check = () => (isMobile.value = window.innerWidth < 500)
    check()
    window.addEventListener('resize', check)
})

  </script>
  