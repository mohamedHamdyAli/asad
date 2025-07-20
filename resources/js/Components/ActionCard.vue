<template>
    <div
      class="bg-blue-50 hover:bg-blue-100 cursor-pointer p-4 rounded-lg flex items-center space-x-4 shadow-sm transition"
    >
      <div class="bg-blue-100 text-blue-600 rounded-full w-10 h-10 flex items-center justify-center">
        <Icon :icon="icon" class="w-5 h-5" />
      </div>
      <div class="text-sm font-medium text-blue-800">
        {{ label }}
      </div>
    </div>
  </template>
  
  <script setup>
  import { Icon } from '@iconify/vue'
  
  defineProps({
    icon: String,
    label: String
  })
  </script>
  