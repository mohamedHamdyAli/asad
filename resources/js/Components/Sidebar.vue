<template>
    <aside class="w-64 h-screen bg-gray-900 text-white flex flex-col justify-between fixed">
        <div>
            <!-- Logo -->
            <div class="p-6 text-xl font-bold tracking-wide">
                CarBreak
            </div>

            <!-- Navigation -->
            <nav class="px-4 space-y-2">
                <NavItem icon="mdi:view-dashboard" label="Dashboard" :to="route('dashboard')" :href="route('dashboard')" />
                <NavItem icon="mdi:table-large" label="Reports" :to="route('finance-management')" :href="route('finance-management')" />
                <!-- <NavItem icon="mdi:file-document-edit-outline" label="Forms" /> -->
                <NavItem icon="mdi:view-dashboard-outline" label="Bids" :to="route('bids-management')" :href="route('bids-management')" />
                <NavItem icon="mdi:responsive" label="SPCs" :to="route('spc-management')" :href="route('spc-management')" />
                <NavItem icon="mdi:file-document-edit-outline" label="App Intros" :to="route('intro-management')" :href="route('intro-management')" />             
                <NavItem icon="mdi:file-document-edit-outline" label="App Banners" :to="route('banner-management')" :href="route('banner-management')" />             
                <NavItem icon="mdi:world" label="Languages" :to="route('language-management')" :href="route('language-management')" />
                <NavItem icon="mdi:people" label="Manage Users" :to="route('users-management')" :href="route('users-management')" />
                <!-- <NavItem icon="mdi:login" label="Login" />
                <NavItem icon="mdi:alert-circle-outline" label="Error" /> -->

                <!-- Dropdown Placeholder -->
                <!-- <div>
                    <button class="w-full flex items-center justify-between text-left px-3 py-2 hover:bg-gray-800 rounded-md">
                        <span class="flex items-center space-x-2">
                            <Icon icon="mdi:menu-down" class="text-lg" />
                            <span>Dropdown</span>
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                </div> -->

            </nav>
        </div>

        <!-- Logout at the bottom -->
        <div class="p-4">
            <NavItem icon="mdi:logout" label="Logout" :to="route('logout')" method="post" />
        </div>
    </aside>
</template>

<script setup>
import NavItem from '@/Components/NavItem.vue'
import { Link } from '@inertiajs/vue3'
import { Icon } from '@iconify/vue'
</script>
