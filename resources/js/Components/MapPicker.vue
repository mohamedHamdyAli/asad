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
      <div>Lat: <span class="font-mono">{{ lat?.toFixed(6) ?? '-' }}</span></div>
      <div>Lng: <span class="font-mono">{{ lng?.toFixed(6) ?? '-' }}</span></div>
      <div>Address: <span class="font-mono break-all">{{ address || '—' }}</span></div>
    </div>
  </div>
</template>

<script setup>
import { Loader } from '@googlemaps/js-api-loader'
import { ref, watch, onMounted, nextTick } from 'vue'

const props = defineProps({
  label: { type: String, default: 'Select location' },
  searchPlaceholder: { type: String, default: 'Search place…' },
  lat: { type: Number, default: null },
  lng: { type: Number, default: null },
  address: { type: String, default: '' },
  fallbackCenter: { type: Object, default: () => ({ lat: 30.0444, lng: 31.2357 }) },
  zoom: { type: Number, default: 13 },
  apiKey: { type: String, default: import.meta.env.VITE_GOOGLE_MAPS_KEY },
})

const emit = defineEmits(['update:lat', 'update:lng', 'update:address'])

const mapEl = ref(null)
const searchEl = ref(null)

let map, marker, autocomplete, geocoder

function setOutputs({ lat, lng, address }) {
  if (lat != null) emit('update:lat', lat)
  if (lng != null) emit('update:lng', lng)
  if (address !== undefined) emit('update:address', address)
}

async function init() {
  const loader = new Loader({
    apiKey: props.apiKey,
    version: 'weekly',
    libraries: ['places'],
  })
  const google = await loader.load()

  geocoder = new google.maps.Geocoder()

  const center = (props.lat != null && props.lng != null)
    ? { lat: props.lat, lng: props.lng }
    : props.fallbackCenter

  map = new google.maps.Map(mapEl.value, {
    center,
    zoom: props.zoom,
    mapTypeControl: false,
    streetViewControl: false,
  })

  marker = new google.maps.Marker({
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

  autocomplete = new google.maps.places.Autocomplete(searchEl.value, {
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

  if (props.lat != null && props.lng != null && !props.address) {
    reverseGeocode(props.lat, props.lng).then(addr => setOutputs({ address: addr || '' }))
  }
}

async function reverseGeocode(lat, lng) {
  if (!geocoder) return ''
  const { results } = await geocoder.geocode({ location: { lat, lng } }).catch(() => ({ results: [] }))
  return results?.[0]?.formatted_address || ''
}

function placeMarker(lat, lng, doReverse = true, presetAddress = '') {
  marker.setPosition({ lat, lng })
  map.panTo({ lat, lng })
  setOutputs({ lat, lng })
  if (presetAddress) {
    setOutputs({ address: presetAddress })
  } else if (doReverse) {
    reverseGeocode(lat, lng).then(addr => setOutputs({ address: addr || '' }))
  }
}

// Allow parent to type lat/lng and sync the marker
watch(() => [props.lat, props.lng], ([lat, lng]) => {
  if (!map || lat == null || lng == null) return
  marker.setPosition({ lat, lng })
  map.panTo({ lat, lng })
})

function useMyLocation() {
  if (!navigator.geolocation) return
  navigator.geolocation.getCurrentPosition(
    (pos) => {
      const { latitude, longitude } = pos.coords
      placeMarker(latitude, longitude, true)
      map.setZoom(15)
    },
    () => {},
    { enableHighAccuracy: true, timeout: 8000 }
  )
}

onMounted(async () => {
  await nextTick()
  await init()
})
</script>

<style scoped>
.form-input { @apply w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500; }
</style>
