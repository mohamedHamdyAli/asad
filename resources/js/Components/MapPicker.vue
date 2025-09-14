<template>
  <div class="space-y-2">
    <label v-if="label" class="block text-xs text-gray-500">{{ label }}</label>

    <!-- search -->
    <div class="flex gap-2">
      <input ref="searchEl" type="text" class="form-input flex-1" :placeholder="searchPlaceholder" />
      <button type="button" class="px-3 py-2 border rounded" @click="useMyLocation">📍</button>
    </div>

    <!-- map -->
    <div ref="mapEl" class="rounded border" style="height: 320px;"></div>

    <!-- preview -->
    <div class="text-xs text-gray-600">
      <div>Lat: <span class="font-mono">{{ fmtLat }}</span></div>
      <div>Lng: <span class="font-mono">{{ fmtLng }}</span></div>
      <div>Address: <span class="font-mono break-all">{{ address || '—' }}</span></div>
    </div>
  </div>
</template>

<script setup>
import { Loader } from '@googlemaps/js-api-loader'
import { ref, watch, onMounted, nextTick, computed } from 'vue'

/* -------- Props & emits -------- */
const props = defineProps({
  label: { type: String, default: 'Select location' },
  searchPlaceholder: { type: String, default: 'Search place…' },
  lat: { type: [Number, String], default: null },
  lng: { type: [Number, String], default: null },
  address: { type: String, default: '' },
  fallbackCenter: { type: Object, default: () => ({ lat: 30.0444, lng: 31.2357 }) },
  zoom: { type: Number, default: 13 },
  apiKey: { type: String, default: import.meta.env.VITE_GOOGLE_MAPS_KEY },
})
const emit = defineEmits(['update:lat', 'update:lng', 'update:address'])

/* -------- Coercion helpers & formatted preview -------- */
const toNum = (v) => {
  if (typeof v === 'number') return Number.isFinite(v) ? v : null
  if (v === '' || v == null) return null
  const n = Number(v)
  return Number.isFinite(n) ? n : null
}
const fmtLat = computed(() => {
  const n = toNum(props.lat)
  return n == null ? '-' : n.toFixed(6)
})
const fmtLng = computed(() => {
  const n = toNum(props.lng)
  return n == null ? '-' : n.toFixed(6)
})

/* -------- Refs -------- */
const mapEl = ref(null)
const searchEl = ref(null)
let map, marker, autocomplete, geocoder, googleRef = null

function setOutputs({ lat, lng, address }) {
  if (lat != null) emit('update:lat', lat)
  if (lng != null) emit('update:lng', lng)
  if (address !== undefined) emit('update:address', address)
}

/* -------- Reverse geocode (callback style) -------- */
function reverseGeocodeCb(lat, lng) {
  return new Promise((resolve) => {
    if (!geocoder) return resolve('')
    geocoder.geocode({ location: { lat, lng } }, (results, status) => {
      if (status === 'OK' && results && results.length) {
        resolve(results[0].formatted_address || '')
      } else {
        resolve('')
      }
    })
  })
}

/* -------- Init map -------- */
async function init() {
  try {
    const loader = new Loader({
      apiKey: props.apiKey,
      version: 'weekly',
      libraries: ['places'],
    })
    googleRef = await loader.load()
  } catch (e) {
    console.error('Google Maps failed to load:', e)
    return
  }

  geocoder = new googleRef.maps.Geocoder()

  const la = toNum(props.lat)
  const ln = toNum(props.lng)
  const center = (la != null && ln != null) ? { lat: la, lng: ln } : props.fallbackCenter

  map = new googleRef.maps.Map(mapEl.value, {
    center,
    zoom: props.zoom,
    mapTypeControl: false,
    streetViewControl: false,
  })

  marker = new googleRef.maps.Marker({
    position: center,
    map,
    draggable: true,
  })

  map.addListener('click', (e) => {
    const pos = e.latLng
    placeMarker(pos.lat(), pos.lng(), true)
  })

  marker.addListener('dragend', () => {
    const pos = marker.getPosition()
    placeMarker(pos.lat(), pos.lng(), true)
  })

  try {
    autocomplete = new googleRef.maps.places.Autocomplete(searchEl.value, {
      fields: ['geometry', 'formatted_address', 'name'],
    })
    autocomplete.addListener('place_changed', () => {
      const place = autocomplete.getPlace()
      if (!place?.geometry?.location) return
      const loc = place.geometry.location
      map.panTo(loc)
      map.setZoom(15)
      const addr = place.formatted_address || place.name || ''
      placeMarker(loc.lat(), loc.lng(), false, addr)
    })
  } catch {
    // Places not available; ignore
  }

  if (la != null && ln != null && !props.address) {
    const addr = await reverseGeocodeCb(la, ln)
    setOutputs({ address: addr || '' })
  }
}

async function placeMarker(lat, lng, doReverse = true, presetAddress = '') {
  if (!marker || !map) return
  marker.setPosition({ lat, lng })
  map.panTo({ lat, lng })
  setOutputs({ lat, lng })
  if (presetAddress) {
    setOutputs({ address: presetAddress })
  } else if (doReverse) {
    const addr = await reverseGeocodeCb(lat, lng)
    setOutputs({ address: addr || '' })
  }
}

/* -------- Watch external changes -------- */
watch(() => [props.lat, props.lng], ([lat, lng]) => {
  if (!map) return
  const la = toNum(lat), ln = toNum(lng)
  if (la == null || ln == null) return
  marker.setPosition({ lat: la, lng: ln })
  map.panTo({ lat: la, lng: ln })
})

/* -------- Geolocation button -------- */
function useMyLocation() {
  if (!navigator.geolocation) return
  navigator.geolocation.getCurrentPosition(
    (pos) => {
      const { latitude, longitude } = pos.coords
      placeMarker(latitude, longitude, true)
      if (map) map.setZoom(15)
    },
    () => {},
    { enableHighAccuracy: true, timeout: 8000 }
  )
}

/* -------- Mount -------- */
onMounted(async () => {
  await nextTick()
  await init()
})
</script>

<style scoped>
.form-input { @apply w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500; }
</style>
