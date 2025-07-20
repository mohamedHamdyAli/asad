<template>
  <div class="space-y-8">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <h3 class="text-2xl font-semibold text-gray-800">Analytics & Financial Reports</h3>
    </div>

    <!-- Date Range Picker Placeholder -->
    <div class="max-w-xs">
      <label class="block text-sm text-gray-600 mb-1">Select Date Range</label>
      <!-- Future integration: VueDatePicker -->
    </div>

    <!-- KPIs -->
    <div class="grid md:grid-cols-3 gap-6">
      <div class="bg-white p-4 shadow rounded text-center">
        <p class="text-sm text-gray-500">Total Revenue</p>
        <p class="text-xl font-semibold text-green-600">{{ currency }} {{ totalRevenue }}</p>
      </div>
      <div class="bg-white p-4 shadow rounded text-center">
        <p class="text-sm text-gray-500">Completed Requests</p>
        <p class="text-xl font-semibold text-blue-600">{{ completedRequests }}</p>
      </div>
      <div class="bg-white p-4 shadow rounded text-center">
        <p class="text-sm text-gray-500">Top Category</p>
        <p class="text-xl font-semibold text-indigo-600">{{ topCategory }}</p>
      </div>
    </div>

    <!-- Chart -->
    <div class="bg-white p-6 shadow rounded-lg">
      <div class="flex items-center justify-between mb-4">
        <h4 class="text-lg font-semibold text-gray-700">Revenue Trend</h4>
        <div class="flex gap-2">
          <button class="btn-blue" @click="exportReport('pdf')">Export PDF</button>
          <button class="btn-green" @click="exportReport('excel')">Export Excel</button>
        </div>
      </div>
      <LineChart :data="lineChartData" :options="chartOptions" />
    </div>

    <!-- Transactions -->
    <div class="bg-white p-6 shadow rounded-lg">
      <h4 class="text-lg font-semibold text-gray-700 mb-4">Transactions & Payouts</h4>
      <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-700">
          <thead class="bg-gray-100 text-xs uppercase text-gray-500">
            <tr>
              <th class="px-4 py-2">Date</th>
              <th class="px-4 py-2">Amount</th>
              <th class="px-4 py-2">Method</th>
              <th class="px-4 py-2">Status</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="t in transactions" :key="t.id" class="border-b hover:bg-gray-50">
              <td class="px-4 py-2">{{ t.date }}</td>
              <td class="px-4 py-2">{{ currency }} {{ t.amount }}</td>
              <td class="px-4 py-2">{{ t.method }}</td>
              <td class="px-4 py-2">
                <span
                  :class="[
                    'px-2 py-1 rounded text-xs font-semibold',
                    t.status === 'Paid' ? 'bg-green-100 text-green-600' : 'bg-yellow-100 text-yellow-600'
                  ]"
                >
                  {{ t.status }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="flex justify-center mt-2">
        <ViewAllBtn>View All</ViewAllBtn>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import LineChart from '@/Components/LineChart.vue'
import ViewAllBtn from '@/Components/ViewAllBtn.vue'

const currency = 'EGP' // Changeable for future use
const totalRevenue = 42300
const completedRequests = 93
const topCategory = 'Valet Pickup' // Could be passed as prop

const dateRange = ref([])

const exportData = ref({
  pdf: false,
  excel: false
})

const exportReport = (format) => {
  exportData.value[format] = true
  console.log(`Exporting ${format.toUpperCase()}...`)
}

// Line chart data
const lineChartData = {
  labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
  datasets: [
    {
      label: 'Revenue',
      data: [12000, 15000, 8000, 7300],
      borderColor: '#10b981',
      tension: 0.4,
      fill: false
    }
  ]
}

const chartOptions = {
  responsive: true,
  plugins: {
    legend: { display: false }
  }
}

const transactions = ref([
  { id: 1, date: '2024-08-01', amount: 15000, method: 'Bank Transfer', status: 'Paid' },
  { id: 2, date: '2024-08-10', amount: 8700, method: 'Wallet', status: 'Pending' },
  { id: 3, date: '2024-08-18', amount: 10300, method: 'Bank Transfer', status: 'Paid' }
])
</script>

<style scoped>
.btn-blue {
  @apply bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700;
}
.btn-green {
  @apply bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700;
}
</style>
