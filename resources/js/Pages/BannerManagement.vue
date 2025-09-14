<template>
      <Head title="Banner Management" />

  <AuthenticatedLayout>
    <div class="p-6 space-y-6">
      <h2 class="text-2xl font-semibold text-gray-800">Banners Gallery</h2>

      <div class="bg-white p-4 rounded shadow">
        <h3 class="text-lg font-bold mb-3">Add Banner</h3>
        <div class="grid md:grid-cols-[1fr_auto_auto] items-end gap-4">
          <div>
            <input type="file" accept="image/*" @change="onNewFile" class="form-input" />
            <div v-if="newPreview" class="mt-2">
              <img :src="newPreview" class="w-48 h-28 object-cover rounded border" />
            </div>
          </div>
          <label class="flex items-center gap-2">
            <input type="checkbox" v-model="newEnabled" />
            <span>Enabled</span>
          </label>
          <button
            :disabled="creating || !newFile"
            class="px-4 py-2 bg-green-600 text-white rounded disabled:opacity-60"
            @click="createBanner"
          >
            {{ creating ? "Uploading…" : "Upload" }}
          </button>
        </div>
        <div v-if="createErr" class="text-sm text-red-600 mt-2">{{ createErr }}</div>
      </div>

      <!-- Gallery -->
      <div class="bg-white p-4 rounded shadow">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-bold">All Banners</h3>
          <button @click="fetchBanners" class="px-3 py-1 border rounded">Refresh</button>
        </div>

        <div v-if="listLoading" class="text-sm text-gray-500">Loading…</div>

        <div v-else class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
          <div
            v-for="b in banners"
            :key="b.id"
            class="rounded border overflow-hidden bg-gray-50"
          >
            <div class="aspect-[4/3] bg-white flex items-center justify-center">
              <img v-if="b.image_url" :src="b.image_url" class="w-full h-full object-cover" />
              <div v-else class="text-gray-400 text-sm">No Image</div>
            </div>

            <div class="p-2 space-y-2">
              <div class="flex items-center justify-between text-xs text-gray-600">
                <span>#{{ b.id }}</span>
                <span :class="b.is_enabled ? 'text-green-600' : 'text-gray-400'">
                  {{ b.is_enabled ? 'Enabled' : 'Disabled' }}
                </span>
              </div>

              <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" :checked="b.is_enabled" @change="toggleEnabled(b)" />
                <span>Enable</span>
              </label>

              <div class="text-xs">
                <input type="file" accept="image/*" @change="onReplaceFile(b.id, $event)" />
              </div>

              <div class="flex gap-2 pt-1">
                <button class="px-2 py-1 border rounded text-sm" @click="saveReplace(b.id)" :disabled="!pendingImage[b.id] || saving[b.id]">
                  {{ saving[b.id] ? 'Saving…' : 'Save' }}
                </button>
                <button class="px-2 py-1 border rounded text-sm text-red-600" @click="remove(b.id)">
                  Delete
                </button>
              </div>
            </div>
          </div>

          <div v-if="!banners.length" class="col-span-full text-center text-gray-500 py-8">
            No banners found.
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { ref, onMounted, reactive } from 'vue'
import { BannerApi, buildCreateBannerFD, buildUpdateBannerFD } from '@/Services/banner'
import { Head } from '@inertiajs/vue3'

const banners = ref([])
const listLoading = ref(false)

/* Create */
const newFile = ref(null)
const newPreview = ref(null)
const newEnabled = ref(true)
const creating = ref(false)
const createErr = ref('')

function onNewFile(e) {
  const f = e.target.files?.[0] || null
  newFile.value = f
  newPreview.value = f ? URL.createObjectURL(f) : null
}
async function createBanner() {
  if (!newFile.value) return
  creating.value = true
  createErr.value = ''
  try {
    const fd = buildCreateBannerFD({ image: newFile.value, is_enabled: newEnabled.value })
    await BannerApi.create(fd)
    await fetchBanners()
    newFile.value = null
    newPreview.value = null
    newEnabled.value = true
  } catch (e) {
    createErr.value = e?.response?.data?.message || e?.message || 'Upload failed'
  } finally {
    creating.value = false
  }
}

/* List */
async function fetchBanners() {
  try {
    listLoading.value = true
    banners.value = await BannerApi.index()
  } finally {
    listLoading.value = false
  }
}

/* Toggle enabled */
async function toggleEnabled(b) {
  // optimistic UI
  const old = b.is_enabled
  b.is_enabled = !old
  try {
    const fd = buildUpdateBannerFD({ is_enabled: b.is_enabled })
    await BannerApi.update(b.id, fd)
  } catch (e) {
    b.is_enabled = old // rollback
    console.error('Toggle failed:', e)
  }
}

/* Replace image */
const pendingImage = reactive({})
const saving = reactive({})

function onReplaceFile(id, e) {
  const f = e.target.files?.[0] || null
  if (f) pendingImage[id] = f
}
async function saveReplace(id) {
  const file = pendingImage[id]
  if (!file) return
  saving[id] = true
  try {
    const fd = buildUpdateBannerFD({ image: file })
    await BannerApi.update(id, fd)
    delete pendingImage[id]
    await fetchBanners()
  } catch (e) {
    console.error('Replace failed:', e)
  } finally {
    saving[id] = false
  }
}

/* Delete */
async function remove(id) {
  if (!confirm('Delete this banner?')) return
  await BannerApi.destroy(id)
  await fetchBanners()
}

onMounted(fetchBanners)
</script>

<style scoped>
.form-input {
  @apply w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500;
}
.aspect-\[4\/3\] { aspect-ratio: 4 / 3; }
</style>
