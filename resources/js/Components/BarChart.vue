<template>
    <div class="w-full h-80">
      <Bar :data="data" :options="options" />
    </div>
  </template>
  
  <script setup>
  import { Bar } from 'vue-chartjs'
  import {
    Chart as ChartJS,
    BarElement,
    Tooltip,
    Legend,
    CategoryScale,
    LinearScale
  } from 'chart.js'
  
  ChartJS.register(BarElement, Tooltip, Legend, CategoryScale, LinearScale)
  
  defineProps({
    data: Object,
    options: Object
  })
  </script>
  