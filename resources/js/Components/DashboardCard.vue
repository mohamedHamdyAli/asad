<template>
    <div class="bg-white shadow rounded-lg p-6 flex items-center space-x-4 hover:shadow-md transition">
        <div class="flex items-center justify-center w-12 h-12 rounded-full bg-blue-100 text-blue-600">
            <Icon :icon="icon" class="w-6 h-6" />
        </div>
        <div>
            <div class="text-gray-600 text-sm font-medium">{{ title }}</div>
            <div class="text-xl font-semibold text-gray-800">{{ value }}</div>
        </div>
    </div>
</template>

<script setup>
import { Icon } from '@iconify/vue'

defineProps({
    icon: String,
    title: String,
    value: String,
})
</script>
