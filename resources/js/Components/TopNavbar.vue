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
        <!-- <Icon icon="mdi:bell-outline" class="w-5 h-5 text-white hover:text-gray-700 cursor-pointer" /> -->
        <!--  Notifications Dropdown -->
        <div class="relative" @click.stop="toggleNotifications">
          <div class="relative">
            <Icon icon="mdi:bell-outline" class="w-5 h-5 text-white cursor-pointer hover:text-gray-300" />
            <span v-if="unreadCount > 0"
              class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full px-1">
              {{ unreadCount }}
            </span>
          </div>

          <div v-if="showNotifications"
            class="absolute right-0 mt-2 w-80 bg-white border rounded-lg shadow-lg z-50 overflow-hidden">
            <div class="flex justify-between items-center px-4 py-2 border-b bg-gray-50">
              <span class="font-semibold text-sm text-gray-700">Notifications</span>
              <button @click.stop="markAllSeen" class="text-xs text-blue-600 hover:underline">
                Mark all as seen
              </button>
            </div>

            <div v-if="loading" class="p-4 text-center text-gray-400 text-sm">
              Loading...
            </div>

            <div v-else>
              <div v-if="notifications.length === 0" class="p-4 text-center text-gray-400 text-sm">
                No notifications
              </div>

              <ul class="max-h-72 overflow-y-auto divide-y">
                <li v-for="notif in notifications" :key="notif.id" @click="markAsSeen(notif)"
                  class="flex justify-between items-start p-3 hover:bg-gray-50 cursor-pointer">
                  <div>
                    <p class="text-sm" :class="notif.seen_at ? 'text-gray-600' : 'text-black font-semibold'">
                      {{ notif.title }}
                    </p>
                    <p class="text-xs text-gray-500 mt-0.5">{{ notif.body }}</p>
                  </div>
                  <Icon icon="mdi:close" class="w-4 h-4 text-gray-400 hover:text-red-500"
                    @click.stop="deleteNotification(notif.id)" />
                </li>
              </ul>
            </div>
          </div>
        </div>

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
import NotificationsService from "@/Services/notificationsService"


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

const showNotifications = ref(false)
const notifications = ref([])
const unreadCount = ref(0)
const loading = ref(false)

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


function toggleNotifications() {
  showNotifications.value = !showNotifications.value
  if (showNotifications.value && notifications.value.length === 0) {
    fetchNotifications()
  }
}

async function fetchNotifications() {
  loading.value = true
  try {
    notifications.value = await NotificationsService.getUserNotifications()
    unreadCount.value = notifications.value.filter((n) => !n.seen_at).length
  } catch (e) {
    console.error("Error fetching notifications:", e)
  } finally {
    loading.value = false
  }
}

async function markAsSeen(notif) {
  if (notif.seen_at) return
  try {
    await NotificationsService.markAsSeen(notif.id)
    notif.seen_at = new Date()
    unreadCount.value = notifications.value.filter((n) => !n.seen_at).length
  } catch (e) {
    console.error("Mark seen error:", e)
  }
}

async function markAllSeen() {
  const unseenIds = notifications.value.filter((n) => !n.seen_at).map((n) => n.id)
  await NotificationsService.markAllAsSeen(unseenIds)
  notifications.value.forEach((n) => (n.seen_at = new Date()))
  unreadCount.value = 0
}

async function deleteNotification(id) {
  try {
    await NotificationsService.deleteNotification(id)
    notifications.value = notifications.value.filter((n) => n.id !== id)
    unreadCount.value = notifications.value.filter((n) => !n.seen_at).length
  } catch (e) {
    console.error("Delete error:", e)
  }
}

window.addEventListener("click", () => (showNotifications.value = false))
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