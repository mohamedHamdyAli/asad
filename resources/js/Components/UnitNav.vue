<template>
  <nav
    class="grid gap-3"
    :class="colsClass"
    aria-label="Unit sections navigation"
  >
    <Link
      v-for="item in visibleItems"
      :key="item.key"
      :href="item.href(unitId)"
      class="px-3 py-2 rounded-lg border text-center hover:bg-gray-50 transition
             focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500
             flex items-center justify-center gap-2"
      :class="isActive(item) ? 'bg-gray-100 border-gray-300' : 'border-gray-200'"
    >
      <component v-if="item.icon" :is="item.icon" class="w-4 h-4 text-gray-600" />
      <span class="text-sm font-medium text-gray-800">{{ item.label }}</span>
    </Link>
  </nav>
</template>

<script setup>
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

// ✅ Import icons from lucide-react (these are auto-available in shadcn setup)
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

/**
 * Props
 * - unitId: required
 * - show: array of keys to show (default: all)
 * - cols:  number of columns (2, 3, or 4). Default: 2
 */
const props = defineProps({
  unitId: { type: [Number, String], required: true },
  show:   { type: Array, default: () => ['docs','gallery','drawing','reports','phases','timeline','assignments','payments'] },
  cols:   { type: Number, default: 2 },
})

// route helpers (align with your web.php)
const hrefs = {
  docs:        id => `/units-management/${id}/docs`,
  gallery:     id => `/units-management/${id}/gallery`,
  drawing:     id => `/units-management/${id}/drawing`,
  reports:     id => `/units-management/${id}/reports`,
  phases:      id => `/units-management/${id}/phases`,
  timeline:    id => `/units-management/${id}/timeline`,
  assignments: id => `/units-management/${id}/contractors`,
  payments:    id => `/units-management/${id}/payments`,
}

// ✅ Enhanced with icons
const items = [
  { key: 'docs',        label: 'Docs',                 href: hrefs.docs,        icon: FileText },
  { key: 'gallery',     label: 'Gallery',              href: hrefs.gallery,     icon: Images },
  { key: 'drawing',     label: 'Drawing',              href: hrefs.drawing,     icon: PenTool },
  { key: 'reports',     label: 'Reports',              href: hrefs.reports,     icon: BarChart3 },
  { key: 'phases',      label: 'Phases',               href: hrefs.phases,      icon: Layers },
  { key: 'timeline',    label: 'Timeline',             href: hrefs.timeline,    icon: Clock },
  { key: 'assignments', label: 'Assignments',          href: hrefs.assignments, icon: Users },
  { key: 'payments',    label: 'Payments & Installments', href: hrefs.payments, icon: CreditCard },
]

// only show selected ones
const visibleItems = computed(() => items.filter(i => props.show.includes(i.key)))

// responsive columns
const colsClass = computed(() => {
  const c = props.cols
  if (c === 4) return 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-4'
  if (c === 3) return 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3'
  return 'grid-cols-1 sm:grid-cols-2' // default 2
})

// highlight active tab
const page = usePage()
function isActive(item) {
  const url = page.url || ''
  const path = item.href(props.unitId)
  return url.startsWith(path)
}
</script>
