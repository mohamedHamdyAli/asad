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
                <li v-for="notif in notifications" :key="notif.id" @click="openNotification(notif)"
                  class="flex justify-between items-start p-3 hover:bg-gray-50 cursor-pointer">
                  <div class="flex-1 min-w-0 pr-2">
                    <p class="text-sm truncate" :class="notif.seen_at ? 'text-gray-600' : 'text-black font-semibold'">
                      {{ notif.title }}
                    </p>
                    <p class="text-xs text-gray-500 mt-0.5 line-clamp-2">{{ notif.body }}</p>
                  </div>
                  <Icon icon="mdi:close" class="w-4 h-4 text-gray-400 hover:text-red-500 shrink-0"
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

    <!-- Notification Details Modal -->
    <Teleport to="body">
      <div
        v-if="selectedNotification"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-[100] p-4"
        @click.self="closeNotification"
      >
        <div class="bg-white rounded-xl shadow-xl w-full max-w-lg overflow-hidden">
          <div class="flex items-start justify-between px-5 py-4 border-b bg-gray-50">
            <div class="min-w-0 pr-3">
              <h3 class="text-base font-semibold text-gray-900 truncate">
                {{ selectedNotification.title }}
              </h3>
              <p class="text-xs text-gray-500 mt-0.5">
                {{ formatNotifDate(selectedNotification.created_at) }}
              </p>
            </div>
            <button
              @click="closeNotification"
              class="text-gray-400 hover:text-gray-700 shrink-0"
              aria-label="Close"
            >
              <Icon icon="mdi:close" class="w-5 h-5" />
            </button>
          </div>

          <div class="px-5 py-4 max-h-[60vh] overflow-y-auto">
            <ul v-if="parsedChanges.bullets.length" class="space-y-2">
              <li
                v-if="parsedChanges.intro"
                class="text-sm text-gray-800 leading-relaxed"
              >
                {{ parsedChanges.intro }}
              </li>
              <li
                v-for="(line, i) in parsedChanges.bullets"
                :key="i"
                class="flex gap-2 text-sm text-gray-700"
              >
                <span class="text-gray-400 mt-0.5">•</span>
                <span class="flex-1">{{ line }}</span>
              </li>
            </ul>
            <p v-else class="text-sm text-gray-700 whitespace-pre-line leading-relaxed">
              {{ selectedNotification.body }}
            </p>
          </div>

          <div class="flex justify-end gap-2 px-5 py-3 border-t bg-gray-50">
            <Link
              v-if="canOpenProject"
              :href="projectDetailsUrl"
              class="px-3 py-1.5 text-sm rounded bg-black text-white hover:bg-gray-800"
              @click="closeNotification"
            >
              View project
            </Link>
            <button
              @click="closeNotification"
              class="px-3 py-1.5 text-sm rounded border hover:bg-gray-100"
            >
              Close
            </button>
          </div>
        </div>
      </div>
    </Teleport>
  </header>
</template>

<script setup>
import { onMounted, onBeforeUnmount, ref, computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import { Icon } from '@iconify/vue'
import NavLink from './NavLink.vue'
import NavItem from './NavItem.vue'
import NotificationsService from "@/Services/notificationsService"


const props = defineProps({
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

const POLL_INTERVAL_MS = 15000
let pollTimer = null

onMounted(() => {
  const check = () => (isMobile.value = window.innerWidth < 500)
  check()
  window.addEventListener('resize', check)

  fetchNotifications()
  pollTimer = setInterval(fetchNotifications, POLL_INTERVAL_MS)
})

onBeforeUnmount(() => {
  if (pollTimer) {
    clearInterval(pollTimer)
    pollTimer = null
  }
})

const showNotifications = ref(false)
const notifications = ref([])
const unreadCount = ref(0)
const loading = ref(false)

const navigationItems = [
  { icon: "mdi:view-dashboard", label: "Dashboard", route: "dashboard" },
  { icon: "mdi:responsive", label: "Project Managers (PMs)", route: "pm-management" },
  { icon: "mdi:responsive", label: "Project management", route: "unit-management" },
  { icon: "mdi:responsive", label: "Sub Contractors Management", route: "contractors-management" },
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
  if (showNotifications.value) {
    fetchNotifications({ showLoading: true })
  }
}

function getSeenStorageKey() {
  const myId = props.user?.id || 'guest'
  return `notifBellSeen_${myId}`
}

function getNotifFingerprint(n) {
  const created = n.created_at ? new Date(n.created_at).toISOString().slice(0, 16) : ''
  return `${n.title || ''}|${n.body || ''}|${created}`
}

function loadSeenFingerprints() {
  try {
    return new Set(JSON.parse(localStorage.getItem(getSeenStorageKey()) || '[]'))
  } catch {
    return new Set()
  }
}

function persistSeenFingerprints(set) {
  try {
    localStorage.setItem(getSeenStorageKey(), JSON.stringify([...set]))
  } catch {}
}

async function fetchNotifications({ showLoading = false } = {}) {
  if (showLoading) loading.value = true
  try {
    const fresh = await NotificationsService.getUserNotifications()
    const myId = props.user?.id
    const systemNotifs = fresh.filter((n) => !!n.objectable_id)
    const mine = myId
      ? systemNotifs.filter((n) => Array.isArray(n.users) && n.users.some((u) => u.id === myId))
      : systemNotifs

    const sessionSeenIds = new Set(
      notifications.value.filter((n) => n.seen_at).map((n) => n.id)
    )
    const persistedSeen = loadSeenFingerprints()

    notifications.value = mine.map((n) => {
      const isSeen =
        sessionSeenIds.has(n.id) ||
        n.is_seen ||
        persistedSeen.has(getNotifFingerprint(n))
      return isSeen
        ? { ...n, seen_at: n.seen_time ? new Date(n.seen_time) : new Date() }
        : n
    })

    unreadCount.value = notifications.value.filter((n) => !n.seen_at).length
  } catch (e) {
    console.error("Error fetching notifications:", e)
  } finally {
    if (showLoading) loading.value = false
  }
}

async function markAsSeen(notif) {
  if (notif.seen_at) return
  try {
    await NotificationsService.markAsSeen(notif.id)
    notif.seen_at = new Date()
    const stored = loadSeenFingerprints()
    stored.add(getNotifFingerprint(notif))
    persistSeenFingerprints(stored)
    unreadCount.value = notifications.value.filter((n) => !n.seen_at).length
  } catch (e) {
    console.error("Mark seen error:", e)
  }
}

const selectedNotification = ref(null)

function openNotification(notif) {
  selectedNotification.value = notif
  showNotifications.value = false
  markAsSeen(notif)
}

function closeNotification() {
  selectedNotification.value = null
}

function formatNotifDate(value) {
  if (!value) return ''
  const d = new Date(value)
  if (isNaN(d.getTime())) return String(value)
  return d.toLocaleString()
}

const parsedChanges = computed(() => {
  const empty = { intro: '', bullets: [] }
  const body = selectedNotification.value?.body
  if (!body) return empty

  const parts = body.split(/\s*•\s*/).map((s) => s.trim()).filter(Boolean)
  if (parts.length < 2) return empty

  return {
    intro: parts[0].replace(/:$/, ''),
    bullets: parts.slice(1),
  }
})

const canOpenProject = computed(() => {
  const n = selectedNotification.value
  return !!(n?.objectable_id && n?.objectable_type?.endsWith('Unit'))
})

const projectDetailsUrl = computed(() => {
  const id = selectedNotification.value?.objectable_id
  return id ? `/projects-management/${id}/details` : '#'
})

async function markAllSeen() {
  try {
    await NotificationsService.markAllAsSeen()
    const stored = loadSeenFingerprints()
    notifications.value.forEach((n) => {
      n.seen_at = new Date()
      stored.add(getNotifFingerprint(n))
    })
    persistSeenFingerprints(stored)
    unreadCount.value = 0
  } catch (e) {
    console.error("Mark all seen error:", e)
  }
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