<template>
    <aside
        class="w-64 h-screen custom-background flex flex-col justify-between fixed border-r border-black/20 shadow-2xl">
        <!-- Logo -->
        <div class="px-6 pt-8 pb-6 border-b border-black/10">
            <Link :href="route('dashboard')" class="flex items-center justify-center">
                <NavLogo class="h-12 w-auto fill-current text-gray-900" />
            </Link>

            <!-- subtle divider label -->
            <div class="mt-4 text-center text-[11px] tracking-widest uppercase text-black/60">
                Admin Panel
            </div>
        </div>

        <!-- Navigation -->
        <div class="flex-1 overflow-y-auto px-3 py-4 sidebar-scroll">
            <nav class="space-y-4">
                <!-- MAIN -->
                <div class="rounded-2xl bg-white/15 backdrop-blur-md border border-white/20 shadow-sm overflow-hidden">
                    <div class="px-3 pt-3 pb-2 text-[11px] font-semibold tracking-widest uppercase text-black/70">
                        Main
                    </div>

                    <div class="pb-2">
                        <NavItem icon="mdi:view-dashboard" label="Dashboard" :to="route('dashboard')"
                            :href="route('dashboard')" />
                    </div>
                </div>

                <!-- MANAGEMENT (collapsible) -->
                <div class="rounded-2xl bg-white/15 backdrop-blur-md border border-white/20 shadow-sm overflow-hidden">
                    <button type="button" class="w-full px-3 pt-3 pb-2 flex items-center justify-between"
                        @click="toggle('management')" :aria-expanded="open.management ? 'true' : 'false'">
                        <span class="text-[11px] font-semibold tracking-widest uppercase text-black/70">
                            Management
                        </span>

                        <svg class="h-4 w-4 text-black/60 transition-transform duration-200"
                            :class="open.management ? 'rotate-180' : 'rotate-0'" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M6 9l6 6 6-6" />
                        </svg>
                    </button>

                    <div v-show="open.management" class="pb-2">
                        <NavItem icon="mdi:responsive" label="Project Managers (PM)" :to="route('pm-management')"
                            :href="route('pm-management')" class="border-b border-gray-300/50" />
                        <NavItem icon="mdi:responsive" label="Units management" :to="route('unit-management')"
                            :href="route('unit-management')" class="border-b border-gray-300/50" />
                        <NavItem icon="mdi:responsive" label="Contractors management"
                            :to="route('contractors-management')" :href="route('contractors-management')"
                            class="border-b border-gray-300/50" />
                        <NavItem icon="mdi:responsive" label="Consultants management"
                            :to="route('Consultants-management')" :href="route('Consultants-management')"
                            class="border-b border-gray-300/50" />
                        <NavItem icon="mdi:responsive" label="Quotations management"
                            :to="route('unit-quotes-responses')" :href="route('unit-quotes-responses')"
                            class="border-b border-gray-300/50" />
                        <NavItem icon="mdi:responsive" label="Price & Building Types" :to="route('unit-quote-types')"
                            :href="route('unit-quote-types')" />
                    </div>
                </div>

                <!-- APP CONTENT (collapsible) -->
                <div class="rounded-2xl bg-white/15 backdrop-blur-md border border-white/20 shadow-sm overflow-hidden">
                    <button type="button" class="w-full px-3 pt-3 pb-2 flex items-center justify-between"
                        @click="toggle('app')" :aria-expanded="open.app ? 'true' : 'false'">
                        <span class="text-[11px] font-semibold tracking-widest uppercase text-black/70">
                            App Content
                        </span>

                        <svg class="h-4 w-4 text-black/60 transition-transform duration-200"
                            :class="open.app ? 'rotate-180' : 'rotate-0'" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M6 9l6 6 6-6" />
                        </svg>
                    </button>

                    <div v-show="open.app" class="pb-2">
                        <NavItem icon="mdi:file-document-edit-outline" label="App Intros"
                            :to="route('intro-management')" :href="route('intro-management')"
                            class="border-b border-gray-300/50" />
                        <NavItem icon="mdi:file-document-edit-outline" label="App Banners"
                            :to="route('banner-management')" :href="route('banner-management')" />
                    </div>
                </div>

                <!-- SYSTEM (collapsible) -->
                <div class="rounded-2xl bg-white/15 backdrop-blur-md border border-white/20 shadow-sm overflow-hidden">
                    <button type="button" class="w-full px-3 pt-3 pb-2 flex items-center justify-between"
                        @click="toggle('system')" :aria-expanded="open.system ? 'true' : 'false'">
                        <span class="text-[11px] font-semibold tracking-widest uppercase text-black/70">
                            System
                        </span>

                        <svg class="h-4 w-4 text-black/60 transition-transform duration-200"
                            :class="open.system ? 'rotate-180' : 'rotate-0'" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M6 9l6 6 6-6" />
                        </svg>
                    </button>

                    <div v-show="open.system" class="pb-2">
                        <NavItem icon="mdi:world" label="Languages" :to="route('language-management')"
                            :href="route('language-management')" class="border-b border-gray-300/50" />
                        <NavItem icon="mdi:world" label="Contact Us Management" :to="route('contactus-management')"
                            :href="route('contactus-management')"  class="border-b border-gray-300/50" />
                        <NavItem icon="mdi:bell-outline" label="Notifications Management"
                            :to="route('notifications-management')" :href="route('notifications-management')" />
                    </div>
                </div>

                <!-- SECURITY -->
                <div class="rounded-2xl bg-white/15 backdrop-blur-md border border-white/20 shadow-sm overflow-hidden">
                    <div class="px-3 pt-3 pb-2 text-[11px] font-semibold tracking-widest uppercase text-black/70">
                        Security
                    </div>

                    <div class="pb-2">
                        <NavItem icon="mdi:people" label="Users Roles & Permissions" :to="route('roles-management')"
                            :href="route('roles-management')" />
                    </div>
                </div>

                <!-- SUPPORT -->
                <div class="rounded-2xl bg-white/15 backdrop-blur-md border border-white/20 shadow-sm overflow-hidden">
                    <div class="px-3 pt-3 pb-2 text-[11px] font-semibold tracking-widest uppercase text-black/70">
                        Support
                    </div>

                    <div class="pb-2">
                        <NavItem icon="mdi:people" label="Unit Issues" :to="route('unit-issues')"
                            :href="route('unit-issues')" />
                    </div>
                </div>
            </nav>
        </div>

        <!-- Footer spacer (optional but looks nicer) -->
        <div class="px-4 py-3 border-t border-black/10">
            <div class="text-[11px] text-black/60">
                © {{ new Date().getFullYear() }}
            </div>
        </div>
    </aside>
</template>

<script setup>
import { ref } from 'vue'
import NavItem from '@/Components/NavItem.vue'
import { Link } from '@inertiajs/vue3'
import NavLogo from './NavLogo.vue'

const open = ref({
    management: true,
    app: true,
    system: true,
})

function toggle(key) {
    open.value[key] = !open.value[key]
}
</script>

<style scoped>
.custom-background {
    background: linear-gradient(180deg, #fcca11 0%, #e5b90f 40%, #8c7373 100%);
}

/* nicer scrollbar */
.sidebar-scroll::-webkit-scrollbar {
    width: 10px;
}

.sidebar-scroll::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.15);
    border-radius: 999px;
}

.sidebar-scroll::-webkit-scrollbar-thumb {
    background: rgba(0, 0, 0, 0.25);
    border-radius: 999px;
}

.sidebar-scroll::-webkit-scrollbar-thumb:hover {
    background: rgba(0, 0, 0, 0.35);
}
</style>
