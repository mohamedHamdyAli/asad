<template>
    <div class="w-full h-60">
      <Pie :data="data" :options="options" />
    </div>
  </template>
  
  <script setup>
  import { Pie } from 'vue-chartjs'
  import {
    Chart as ChartJS,
    ArcElement,
    Tooltip,
    Legend
  } from 'chart.js'
  
  ChartJS.register(ArcElement, Tooltip, Legend)
  
  defineProps({
    data: Object,
    options: Object
  })
  </script>
  