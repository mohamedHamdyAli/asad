<template>
    <div class="w-full h-80">
      <Line :data="data" :options="options" />
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
  
  ChartJS.register(Title, Tooltip, Legend, LineElement, PointElement, CategoryScale, LinearScale)
  
  defineProps({
    data: Object,
    options: Object
  })
  </script>
  