<template>
  <AuthenticatedLayout>
    <div class="p-6 space-y-6">
      <!-- Top Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
        <DashboardCard icon="mdi:domain" title="Total Units" :value="metrics.totalEntities" />
        <DashboardCard icon="mdi:account-group-outline" title="Total Users" :value="metrics.totalMembers" />
      </div>

      <!-- <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <RevenueTrendChart />
        <CategoryDistributionChart />
      </div>

      <QuickActionsPanel />
      <RecentActivitiesTable />
      <OpportunitiesTable />
      <EntitiesTable /> -->
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import DashboardCard from '@/Components/DashboardCard.vue'
import RevenueTrendChart from '@/Components/RevenueTrendChart.vue'
import CategoryDistributionChart from '@/Components/CategoryDistributionChart.vue'
import QuickActionsPanel from '@/Components/QuickActionsPanel.vue'
import RecentActivitiesTable from '@/Components/RecentActivitiesTable.vue'
import OpportunitiesTable from '@/Components/OpportunitiesTable.vue'
import EntitiesTable from '@/Components/EntitiesTable.vue'

const currency = 'EGP'
const metrics = ref({
  totalEntities: 12,
  totalMembers: 158,
  totalTasks: 1250,
  pendingApplications: 8,
  earnings: 75000,
  completedTasks: 1045
})
</script>
