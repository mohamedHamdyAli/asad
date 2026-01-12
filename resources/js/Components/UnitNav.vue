<template>
  <div class="w-full border-b border-gray-200">
    <div
      class="flex flex-wrap gap-1 sm:gap-2"
      role="tablist"
      aria-label="Unit sections navigation"
    >
      <Link
        v-for="item in visibleItems"
        :key="item.key"
        :href="item.href(unitId)"
        role="tab"
        :aria-selected="isActive(item)"
        class="relative px-4 py-2 flex items-center gap-2
               text-sm font-medium transition
               rounded-md sm:rounded-none
               hover:bg-gray-50
               focus:outline-none focus:ring-2 focus:ring-indigo-500
               border sm:border-0"
        :class="[
          isActive(item)
            ? 'text-indigo-600 bg-indigo-50 border-indigo-300'
            : 'text-gray-600 border-transparent'
        ]"
      >
        <component
          v-if="item.icon"
          :is="item.icon"
          class="w-4 h-4"
          :class="isActive(item) ? 'text-indigo-600' : 'text-gray-500'"
        />
        <span>{{ item.label }}</span>

        <!-- Bottom active indicator -->
        <span
          v-if="isActive(item)"
          class="absolute bottom-0 left-0 right-0 h-0.5 bg-indigo-600 sm:block hidden"
        ></span>
      </Link>
    </div>
  </div>
</template>
<script setup>
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

import {
  FileText,
  Images,
  PenTool,
  BarChart3,
  Layers,
  Clock,
  Users,
  CreditCard
} from 'lucide-vue-next'

const props = defineProps({
  unitId: { type: [Number, String], required: true },
  show: {
    type: Array,
    default: () =>
      ['docs', 'gallery', 'drawing', 'reports', 'phases', 'timeline', 'assignments', 'payments']
  },
  cols: { type: Number, default: 2 }
})

const hrefs = {
  docs: id => `/projects-management/${id}/docs`,
  gallery: id => `/projects-management/${id}/gallery`,
  drawing: id => `/projects-management/${id}/drawing`,
  reports: id => `/projects-management/${id}/reports`,
  phases: id => `/projects-management/${id}/phases`,
  timeline: id => `/projects-management/${id}/timeline`,
  assignments: id => `/projects-management/${id}/contractors`,
  payments: id => `/projects-management/${id}/payments`
}

const items = [
  { key: 'docs', label: 'Docs', href: hrefs.docs, icon: FileText },
  { key: 'gallery', label: 'Gallery', href: hrefs.gallery, icon: Images },
  { key: 'drawing', label: 'Drawing', href: hrefs.drawing, icon: PenTool },
  { key: 'reports', label: 'Reports', href: hrefs.reports, icon: BarChart3 },
  { key: 'phases', label: 'Phases', href: hrefs.phases, icon: Layers },
  { key: 'timeline', label: 'Timeline', href: hrefs.timeline, icon: Clock },
  { key: 'assignments', label: 'Assignments', href: hrefs.assignments, icon: Users },
  { key: 'payments', label: 'Payments & Installments', href: hrefs.payments, icon: CreditCard }
]

const visibleItems = computed(() => items.filter(i => props.show.includes(i.key)))

const page = usePage()
function isActive(item) {
  const url = page.url || ''
  const path = item.href(props.unitId)
  return url.startsWith(path)
}

</script>