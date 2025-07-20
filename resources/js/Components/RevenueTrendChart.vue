<template>
    <div class="bg-white shadow rounded-lg p-6">
      <h2 class="text-lg font-semibold text-gray-700 mb-4">Revenue Trend</h2>
      <Line :data="chartData" :options="chartOptions" />
    </div>
  </template>
  
  <script setup>
  import { Line } from 'vue-chartjs'
  import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    LineElement,
    CategoryScale,
    LinearScale,
    PointElement
  } from 'chart.js'
  
  ChartJS.register(Title, Tooltip, Legend, LineElement, CategoryScale, LinearScale, PointElement)
  
  const chartData = {
    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
    datasets: [
      {
        label: 'Revenue (SAR)',
        backgroundColor: '#3b82f6',
        borderColor: '#3b82f6',
        data: [30000, 45000, 40000, 60000, 75000, 68000],
        fill: false,
        tension: 0.4
      }
    ]
  }
  
  const chartOptions = {
    responsive: true,
    maintainAspectRatio: false
  }
  </script>
  
  <style scoped>
  /* give container height so Chart.js renders correctly */
  div {
    height: 350px;
  }
  </style>
  