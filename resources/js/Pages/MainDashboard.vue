<template>

  <Head title="Admin Dashboard" />

  <AuthenticatedLayout>
    <div class="p-6 space-y-10">

      <!-- ================= HEADER ================= -->
      <div class="flex justify-between items-center">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
        </div>

        <span class="px-3 py-1 text-xs rounded-full bg-green-100 text-green-700">
          System Online
        </span>
      </div>

      <!-- ================= KPI CARDS ================= -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

        <SmartCard title="Units" :value="stats.units" icon="mdi:domain" color="blue" hint="Active projects"
          @click="go('unit-management')" />

        <SmartCard title="Users" :value="stats.users" icon="mdi:account-group-outline" color="indigo"
          hint="Registered users" @click="go('roles-management')" />

        <SmartCard title="Open Issues" :value="stats.openIssues" icon="mdi:alert-circle-outline" color="red"
          hint="Requires attention" pulse @click="go('unit-issues')" />

        <SmartCard title="Quotations" :value="stats.quotations" icon="mdi:file-document-outline" color="emerald"
          hint="Client requests" @click="go('unit-quotes-responses')" />

      </div>

      <!-- ================= QUICK ACTIONS ================= -->
      <div class="bg-white rounded-2xl shadow p-6">
        <h3 class="font-semibold mb-4">Quick Actions</h3>

        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
          <ActionTile icon="mdi:plus-box" label="Add Unit" @click="go('unit-management')" />
          <ActionTile icon="mdi:account-hard-hat" label="Add Contractor" @click="go('contractors-management')" />
          <ActionTile icon="mdi:account-hard-hat" label="Add Consultant" @click="go('Consultants-management')" />
          <ActionTile icon="mdi:alert" label="Review Issues" @click="go('unit-issues')" />
          <ActionTile icon="mdi:file-document" label="View Quotations" @click="go('unit-quotes-responses')" />
        </div>
      </div>

      <!-- ================= MAIN GRID ================= -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- ===== PRIORITY ISSUES ===== -->
        <div class="bg-white rounded-2xl shadow p-6 lg:col-span-2">
          <div class="flex justify-between items-center mb-4">
            <h3 class="font-semibold">Latest Quotations</h3>
            <button class="text-sm text-blue-600" @click="go('unit-issues')">
              View all
            </button>
          </div>

          <div v-if="recentIssues.length === 0" class="text-gray-500 text-sm">
            No Current Quotations
          </div>

          <div v-else class="space-y-3">
            <div v-for="issue in recentIssues" :key="issue.id"
              class="flex items-center justify-between border rounded-xl p-4 hover:bg-gray-50">
              <div class="flex gap-3 items-start">
                <Icon icon="mdi:alert-circle" class="text-red-500 text-xl mt-1" />

                <div>
                  <div class="font-medium text-gray-900">
                    {{ issue.title }}
                  </div>

                  <div class="text-xs text-gray-500">
                    {{ issue.unit?.name?.en ?? 'Unit #' + issue.unit_id }}
                  </div>
                </div>
              </div>

              <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-700">
                OPEN
              </span>
            </div>
          </div>
        </div>

        <!-- ===== NAVIGATION HUB ===== -->
        <div class="bg-white rounded-2xl shadow p-6">
          <h3 class="font-semibold mb-4">Management</h3>

          <div class="space-y-3">
            <NavCard icon="mdi:domain" label="Units" @click="go('unit-management')" />
            <NavCard icon="mdi:account-hard-hat" label="Contractors" @click="go('contractors-management')" />
            <NavCard icon="mdi:account-hard-hat" label="Consultants" @click="go('Consultants-management')" />
            <NavCard icon="mdi:file-document" label="Quotations" @click="go('unit-quotes-responses')" />
            <NavCard icon="mdi:account-key" label="Users & Roles" @click="go('roles-management')" />
          </div>
        </div>

      </div>

    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import { Icon } from '@iconify/vue'
import SmartCard from '@/Components/SmartCard.vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import ActionTile from '@/Components/ActionTile.vue'
import NavCard from '@/Components/NavCard.vue'

// ===== MOCKED DATA (replace with APIs) =====
const stats = ref({
  units: 12,
  users: 48,
  openIssues: 5,
  quotations: 9,
})

const recentIssues = ref([])

function go(routeName) {
  router.visit(route(routeName))
}

onMounted(async () => {
 // fetch apis
})
</script>
