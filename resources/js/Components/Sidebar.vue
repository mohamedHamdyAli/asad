<template>
  <aside
    class="w-64 h-screen bg-dash-gray custom-background flex flex-col justify-between fixed border-r-2 border-black shadow-lg"
  >
    <div>
      <!-- Logo -->
      <div class="px-6 pt-8 pb-10 text-xl font-bold tracking-wide border-b-2 border-black">
        <Link :href="route('dashboard')" class="flex items-center justify-center">
          <NavLogo class="h-12 w-auto fill-current text-gray-900" />
        </Link>
      </div>

      <!-- Navigation -->
      <nav class="mt-2">
        <NavItem
          icon="mdi:view-dashboard"
          label="Dashboard"
          :to="route('dashboard')"
          :href="route('dashboard')"
          class="border-b border-gray-300/70"
        />
        <NavItem
          icon="mdi:responsive"
          label="Project Managers (PM)"
          :to="route('vendors-management')"
          :href="route('vendors-management')"
          class="border-b border-gray-300/70"
        />
        <NavItem
          icon="mdi:responsive"
          label="Units management"
          :to="route('unit-management')"
          :href="route('unit-management')"
          class="border-b border-gray-300/70"
        />
        <NavItem
          icon="mdi:responsive"
          label="Contractors management"
          :to="route('contractors-management')"
          :href="route('contractors-management')"
          class="border-b border-gray-300/70"
        />
        <NavItem
          icon="mdi:file-document-edit-outline"
          label="App Intros"
          :to="route('intro-management')"
          :href="route('intro-management')"
          class="border-b border-gray-300/70"
        />
        <NavItem
          icon="mdi:file-document-edit-outline"
          label="App Banners"
          :to="route('banner-management')"
          :href="route('banner-management')"
          class="border-b border-gray-300/70"
        />
        <NavItem
          icon="mdi:world"
          label="Languages"
          :to="route('language-management')"
          :href="route('language-management')"
          class="border-b border-gray-300/70"
        />
        <NavItem
          icon="mdi:people"
          label="Manage Users"
          :to="route('users-management')"
          :href="route('users-management')"
        />
      </nav>
    </div>
  </aside>
</template>

<script setup>
import NavItem from '@/Components/NavItem.vue'
import { Link } from '@inertiajs/vue3'
import NavLogo from './NavLogo.vue'
</script>

<style scoped>
.custom-background {
  background: linear-gradient(180deg, #fcca11 0%, #e5b90f 40%, #8c7373 100%);
}
</style>
