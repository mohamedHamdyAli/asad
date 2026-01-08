<template>
  <header class="w-full custom-background shadow-sm border-b border-gray-200 px-4 sm:px-6 lg:px-8">
    <div class="h-16 flex justify-between items-center">
      <!-- Left side: Search + title -->
      <div class="flex items-center space-x-6">
        <!-- <div class="text-sm text-gray-500 hidden md:block">
            <span class="text-gray-400">Search</span> <kbd class="text-xs bg-gray-100 px-1 py-0.5 rounded">ctrl+k</kbd>
          </div> -->

        <!-- Sample Menu Dropdown -->
       <!-- Mobile Menu Button -->
<div class="relative lg:hidden">
  <button
    @click.stop="toggleSampleMenu"
    class="flex items-center gap-2 text-white font-medium"
  >
    <Icon icon="mdi:menu" class="w-6 h-6" />
    <span>Menu</span>
  </button>

  <!-- Dropdown -->
  <div
    v-if="sampleMenuOpen"
    class="absolute left-0 mt-2 w-64 bg-white rounded-xl shadow-lg z-50 overflow-hidden"
  >
    <template v-for="(item, i) in navigationItems" :key="i">
      <div v-if="item.divider" class="border-t my-1" />

      <Link
        v-else
        :href="route(item.route)"
        class="flex items-center gap-3 px-4 py-3 text-sm hover:bg-gray-100"
        @click="sampleMenuOpen = false"
      >
        <Icon :icon="item.icon" class="w-5 h-5 text-gray-600" />
        <span>{{ item.label }}</span>
      </Link>
    </template>
  </div>
</div>

      </div>

      <!-- Right side: Icons + profile -->
      <div class="flex items-center space-x-4">
        <!-- Profile Dropdown -->
        <div class="relative" @click="toggleDropdown">
          <button class="flex items-center space-x-2 text-gray-800 hover:text-gray-900 focus:outline-none">
            <!-- <img
                src="https://i.pravatar.cc/32"
                alt="Avatar"
                class="w-8 h-8 rounded-full object-cover"
              /> -->
            <span class="text-white font-medium">Hi, {{ user.name }}</span>
            <Icon icon="mdi:chevron-down" class="w-4 h-4" />
          </button>

          <div v-if="dropdownOpen" class="absolute right-0 mt-2 w-48 bg-white border rounded shadow z-50">
            <Link :href="route('profile.edit')" class="flex items-center px-4 py-2 text-sm hover:bg-gray-100">
              <Icon icon="mdi:account" class="me-2 w-4 h-4" />
              My Profile
            </Link>
            <div class="border-t my-1"></div>
            <Link :href="route('logout')" method="post" as="button"
              class="block w-full text-left px-4 py-2 text-sm hover:bg-gray-100">
              Logout
            </Link>
          </div>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { onMounted, ref } from 'vue'
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

const navigationItems = [
  { icon: "mdi:view-dashboard", label: "Dashboard", route: "dashboard" },
  { icon: "mdi:responsive", label: "Project Managers (PM)", route: "pm-management" },
  { icon: "mdi:responsive", label: "Units management", route: "unit-management" },
  { icon: "mdi:responsive", label: "Contractors management", route: "contractors-management" },
  { icon: "mdi:responsive", label: "Consultants management", route: "Consultants-management" },
  { icon: "mdi:responsive", label: "Quotations management", route: "unit-quotes-responses" },
  { icon: "mdi:responsive", label: "Price & Building Types", route: "unit-quote-types" },

  { divider: true },

  { icon: "mdi:file-document-edit-outline", label: "App Intros", route: "intro-management" },
  { icon: "mdi:file-document-edit-outline", label: "App Banners", route: "banner-management" },
  { icon: "mdi:world", label: "Languages", route: "language-management" },
  { icon: "mdi:world", label: "Contact Us Management", route: "contactus-management" },
  { icon: "mdi:people", label: "Users Roles & Permissions", route: "roles-management" },
  { icon: "mdi:people", label: "Unit Issues", route: "unit-issues" },
]

window.addEventListener("click", () => {
  sampleMenuOpen.value = false
})


</script>

<style>
.custom-background {
  /* background-color: #FF2D20;
  */
  background: linear-gradient(0deg, #fcca11 0%, #000000 93%);
}
</style>