<template>
    <component
        :is="tag"
        :href="href"
        :to="to"
        :method="method"
        class="flex items-center px-3 py-2 rounded-md hover:bg-gray-800 transition-all text-white"
    >
        <Icon :icon="icon" class="text-xl me-3 shrink-0" />
        <span class="text-sm font-medium">{{ label }}</span>
    </component>
</template>

<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import { Icon } from '@iconify/vue'

const props = defineProps({
    icon: String,
    label: String,
    to: String,
    href: String,
    method: String,
    external: Boolean
})

const tag = computed(() => {
    if (props.external) return 'a'
    if (props.to || props.method) return Link
    return 'div'
})
</script>
