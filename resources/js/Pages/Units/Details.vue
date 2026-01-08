<template>
  <AuthenticatedLayout>
    <div class="p-6 space-y-6">

      <!-- Back Button -->
      <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800">Project Details</h2>

        <Link
          href="/projects-management"
          class="px-3 py-2 rounded border hover:bg-gray-50 text-gray-700"
        >
          ← Back to Projects
        </Link>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="text-gray-500">Loading…</div>

      <!-- Content -->
      <div v-else class="space-y-6">

        <!-- Cover Image -->
        <div class="relative rounded-xl overflow-hidden shadow-lg h-72 md:h-80">
          <img
            v-if="d.cover_image_url"
            :src="d.cover_image_url"
            class="absolute inset-0 w-full h-full object-cover"
            alt="Project Cover"
          />

          <div
            v-else
            class="absolute inset-0 bg-gray-100 flex items-center justify-center text-gray-400 text-sm"
          >
            No Cover Image
          </div>

          <!-- Title overlay -->
          <div
            class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent p-6 flex flex-col justify-end"
          >
            <h1 class="text-3xl font-bold text-white">
              {{ d.name_en || 'Unnamed Project' }}
            </h1>
            <div v-if="d.name_ar" class="text-gray-200 text-sm" dir="rtl">
              {{ d.name_ar }}
            </div>
          </div>
        </div>

        <!-- Basic Info -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
          <div class="bg-gray-50 rounded-lg p-4">
            <span class="block text-xs uppercase text-gray-400">Status</span>
            <span class="font-semibold text-gray-800">
              {{ d.status || '—' }}
            </span>
          </div>

          <div class="bg-gray-50 rounded-lg p-4">
            <span class="block text-xs uppercase text-gray-400">Location</span>
            <span class="font-semibold text-gray-800">
              {{ d.location || '—' }}
            </span>
            <div v-if="d.lat && d.long" class="text-xs text-gray-500">
              ({{ d.lat }}, {{ d.long }})
            </div>
          </div>

          <div class="bg-gray-50 rounded-lg p-4">
            <span class="block text-xs uppercase text-gray-400">Duration</span>
            <span class="font-semibold text-gray-800">
              {{ d.start_date || '—' }} → {{ d.end_date || '—' }}
            </span>
          </div>
        </div>

        <!-- Description -->
        <div class="bg-white rounded-xl border p-6 shadow-sm">
          <div class="font-semibold text-gray-800 mb-2">Description (EN)</div>
          <p class="text-gray-700 text-sm whitespace-pre-wrap mb-4">
            {{ d.description_en || '—' }}
          </p>

          <div class="font-semibold text-gray-800 mb-2">Description (AR)</div>
          <p class="text-gray-700 text-sm whitespace-pre-wrap" dir="rtl">
            {{ d.description_ar || '—' }}
          </p>
        </div>

        <!-- Gallery -->
        <div>
          <h3 class="font-semibold text-gray-800 mb-3">Gallery</h3>

          <div
            v-if="d.gallery && d.gallery.length"
            class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3"
          >
            <div
              v-for="g in d.gallery"
              :key="g.id"
              class="aspect-[4/3] rounded-lg overflow-hidden bg-gray-100 border shadow-sm"
            >
              <img :src="g.image_url" class="w-full h-full object-cover" />
            </div>
          </div>

          <div v-else class="text-sm text-gray-500">
            No images available.
          </div>
        </div>

        <!-- Navigation Buttons -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 gap-3 pt-4 border-t">
          <Link :href="docsPath(d.id)" class="nav-btn">📄 Docs</Link>
          <Link :href="galleryPath(d.id)" class="nav-btn">🖼️ Gallery</Link>
          <Link :href="drawingPath(d.id)" class="nav-btn">✏️ Drawings</Link>
          <Link :href="reportsPath(d.id)" class="nav-btn">📊 Reports</Link>
          <Link :href="phasesPath(d.id)" class="nav-btn">🧩 Phases</Link>
          <Link :href="timelinePath(d.id)" class="nav-btn">🕒 Timeline</Link>
          <Link :href="unitContractorsPath(d.id)" class="nav-btn">👷 Assignments</Link>
          <Link :href="unitPaymentsPath(d.id)" class="nav-btn">💰 Payments & Installment</Link>
        </div>

      </div>

    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { UnitsApi } from '@/Services/units'
import { Link, usePage } from '@inertiajs/vue3'

const props = defineProps({
  id: Number, // coming from the route
})

const loading = ref(true)
const data = ref(null)

const d = computed(() => data.value || {})

onMounted(async () => {
  loading.value = true
  data.value = await UnitsApi.show(props.id)
  loading.value = false
})

function docsPath(id) {
  return `/projects-management/${id}/docs`
}
function galleryPath(id) {
  return `/projects-management/${id}/gallery`
}
function drawingPath(id) {
  return `/projects-management/${id}/drawing`
}
function reportsPath(id) {
  return `/projects-management/${id}/reports`
}
function phasesPath(id) {
  return `/projects-management/${id}/phases`
}
function timelinePath(id) {
  return `/projects-management/${id}/timeline`
}
function unitContractorsPath(id) {
  return `/projects-management/${id}/contractors`
}
function unitPaymentsPath(id) {
  return `/projects-management/${id}/payments`
}
</script>

<style scoped>
.nav-btn {
  @apply flex items-center justify-center gap-1 px-3 py-2 border rounded-lg text-sm text-gray-700 bg-gray-50 hover:bg-gray-100 hover:shadow transition;
}
</style>
