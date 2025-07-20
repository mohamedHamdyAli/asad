<template>
    <AuthenticatedLayout>
      <div class="p-6 space-y-6">
        <!-- Page Header -->
        <div class="flex items-center justify-between">
          <h2 class="text-2xl font-semibold text-gray-800">Financial Reports</h2>
        </div>
  
        <!-- Filters -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <input
            type="month"
            v-model="filters.month"
            class="form-input"
          />
          <select v-model="filters.type" class="form-input">
            <option disabled value="">Select Report Type</option>
            <option value="revenue-expenses">Revenue vs Expenses</option>
            <option value="earnings">Earnings by SPC</option>
            <option value="services">Top Services</option>
          </select>
          <div class="flex gap-2">
            <button @click="fetchReport" class="btn-primary">View</button>
            <button @click="exportReport('pdf')" class="btn-red">PDF</button>
            <button @click="exportReport('excel')" class="btn-green">Excel</button>
          </div>
        </div>
  
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <SummaryCard label="Total Revenue" value="EGP 235,000" color="text-green-600" />
          <SummaryCard label="Total Expenses" value="EGP 70,000" color="text-red-600" />
          <SummaryCard label="Net Profit" value="EGP 165,000" color="text-blue-600" />
          <SummaryCard label="SPCs Active" value="18" color="text-indigo-600" />
        </div>
  
        <!-- Chart Panels -->
        <div class="space-y-8">
          <!-- Revenue vs Expenses Chart -->
          <div v-if="filters.type === 'revenue-expenses'" class="chart-card">
            <h3 class="chart-title">Revenue vs Expenses</h3>
            <LineChart :data="lineChartData" :options="chartOptions" />
          </div>
  
          <!-- Earnings by SPC -->
          <div v-if="filters.type === 'earnings'" class="chart-card">
            <h3 class="chart-title">Earnings by SPC</h3>
            <BarChart :data="barChartData" :options="chartOptions" />
          </div>
  
          <!-- Top Services Pie -->
          <div v-if="filters.type === 'services'" class="chart-card h-[350px]">
            <h3 class="chart-title">Top Services (Revenue Share)</h3>
            <div class="h-full w-full px-4 py-2">
            <PieChart :data="pieChartData" :options="{ responsive: true, maintainAspectRatio: false }" />
            </div>
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  </template>
  
  <script setup>
  import { ref } from 'vue'
  import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
  import LineChart from '@/Components/LineChart.vue'
  import BarChart from '@/Components/BarChart.vue'
  import PieChart from '@/Components/PieChart.vue'
  import SummaryCard from '@/Components/SummaryCard.vue'
  
  const filters = ref({
    month: '',
    type: 'revenue-expenses'
  })
  
  const fetchReport = () => {
    console.log('Fetching report:', filters.value)
    // Future: use API to get fresh data
  }
  
  const exportReport = (format) => {
    console.log(`Exporting as ${format}`)
  }
  
  // Dummy Data
  const lineChartData = {
    labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
    datasets: [
      {
        label: 'Revenue',
        data: [60000, 55000, 58000, 62000],
        borderColor: '#10b981',
        tension: 0.4
      },
      {
        label: 'Expenses',
        data: [18000, 16000, 19000, 17000],
        borderColor: '#ef4444',
        tension: 0.4
      }
    ]
  }
  
  const barChartData = {
    labels: ['ShinePro', 'AutoCare', 'SmartWash', 'Elite Valet'],
    datasets: [
      {
        label: 'Earnings (SAR)',
        backgroundColor: '#3b82f6',
        data: [56000, 47000, 65000, 43000]
      }
    ]
  }
  
  const pieChartData = {
    labels: ['Valet', 'Washing', 'Cover', 'Other'],
    datasets: [
      {
        backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#f87171'],
        data: [45, 30, 15, 10]
      }
    ]
  }
  
  const chartOptions = {
    responsive: true,
    plugins: {
      legend: { position: 'top' }
    }
  }
  </script>
  
  <style scoped>
  .form-input {
    @apply w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500;
  }
  .btn-primary {
    @apply bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2 rounded-md;
  }
  .btn-red {
    @apply bg-red-600 hover:bg-red-700 text-white font-medium px-4 py-2 rounded-md;
  }
  .btn-green {
    @apply bg-green-600 hover:bg-green-700 text-white font-medium px-4 py-2 rounded-md;
  }
  .chart-card {
    @apply bg-white p-6 rounded-lg shadow;
  }
  .chart-title {
    @apply text-lg font-semibold text-gray-700 mb-4;
  }
  </style>
  