<template>
    <div class="min-h-screen bg-gray-100 flex">
         <ServerErrorBanner />
        <!-- Sidebar (fixed on desktop) -->
        <Sidebar v-show="sidebarOpen || isDesktop" class="z-40" />

        <!-- Main area -->
        <div class="flex-1 flex flex-col min-h-screen" :class="{ 'ms-64': isDesktop }">
            <!-- Top Navbar -->
            <TopNavbar :user="user" @toggleSidebar="toggleSidebar" />

            <!-- Page Content -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8 bg-dash-light">
                <slot />
            </main>
        </div>
    </div>
</template>

<script setup>
import Sidebar from '@/Components/Sidebar.vue'
import TopNavbar from '@/Components/TopNavbar.vue'
import { computed, ref, onMounted } from 'vue'
import { usePage } from '@inertiajs/vue3'
import ServerErrorBanner from '@/Components/ServerErrorBanner.vue'

const sidebarOpen = ref(false)
const toggleSidebar = () => (sidebarOpen.value = !sidebarOpen.value)

// Determine if screen is desktop
const isDesktop = ref(false)
onMounted(() => {
    const check = () => (isDesktop.value = window.innerWidth >= 1024)
    check()
    window.addEventListener('resize', check)
})

const user = computed(() => usePage().props.auth.user)
</script>
