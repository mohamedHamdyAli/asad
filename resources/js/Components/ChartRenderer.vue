<template>
    <div>
      <LineChart v-if="type === 'revenue-expenses'" />
      <BarChart v-else-if="type === 'top-services'" />
      <PieChart v-else-if="type === 'spc-earnings'" />
    </div>
  </template>
  
  <script setup>
  import { defineProps } from 'vue'
  import LineChart from './LineChart.vue'
  import BarChart from './BarChart.vue'
  import PieChart from './PieChart.vue'
  
  defineProps({ type: String })
  </script>
  