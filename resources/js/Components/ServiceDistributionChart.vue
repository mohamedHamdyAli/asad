<template>
    <div class="bg-white shadow rounded-lg p-6">
      <h2 class="text-lg font-semibold text-gray-700 mb-4">Service Distribution</h2>
      <div class="h-[250px]">
        <Pie :data="chartData" :options="chartOptions" />
      </div>
    </div>
  </template>
  
  
  <script setup>
  import { Pie } from 'vue-chartjs'
  import {
    Chart as ChartJS,
    Tooltip,
    Legend,
    ArcElement
  } from 'chart.js'
  
  ChartJS.register(Tooltip, Legend, ArcElement)
  
  const chartData = {
    labels: ['Valet', 'Washing', 'Cover'],
    datasets: [
      {
        backgroundColor: ['#3b82f6', '#10b981', '#f59e0b'],
        data: [50, 30, 20]
      }
    ]
  }
  
  const chartOptions = {
    responsive: true,
    maintainAspectRatio: false
  }
  </script>
  
  <style scoped>

  </style>
  