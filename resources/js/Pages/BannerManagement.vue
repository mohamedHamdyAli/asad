<template>

  <Head title="Banner Management" />

  <AuthenticatedLayout>
    <div class="p-6 space-y-6">
      <!-- Header -->
      <div class="flex items-start justify-between gap-4">
        <div>
          <h2 class="text-2xl font-semibold text-dash-title">Banners Gallery</h2>
        </div>
        <!-- 
        <button
          @click="fetchBanners"
          class="px-3 py-2 text-sm border rounded-xl hover:bg-gray-50 inline-flex items-center gap-2"
        >
          <span class="text-base">↻</span>
          Refresh
        </button> -->
      </div>

      <!-- Create Banner -->
      <Form v-if="can('banners.create')" :validation-schema="createSchema" :initial-values="createInitial" @submit="createBanner"
        @invalid-submit="onInvalidSubmit" v-slot="{ setFieldValue, setFieldError, submitCount, errors, values }"
        class="bg-white rounded-2xl shadow-sm ring-1 ring-black/[0.05] overflow-hidden">
        <div class="p-5 border-b bg-gradient-to-r from-gray-50 to-white">
          <h3 class="text-lg font-bold">Add Banner</h3>
        </div>

        <div class="p-5 grid grid-cols-1 lg:grid-cols-12 gap-5">
          <!-- Left: Fields -->
          <div class="lg:col-span-8 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <!-- Page -->
              <div>
                <label class="label">Page *</label>
                <Field as="select" name="page" class="form-input">
                  <option v-for="p in PAGES" :key="p" :value="p">{{ p }}</option>
                </Field>
                <ErrorMessage name="page" class="err" />
              </div>

              <!-- Banner Type -->
              <div>
                <label class="label">Banner Type *</label>
                <Field as="select" name="banner_type" class="form-input">
                  <option value="guest">Guest</option>
                  <option value="user">User</option>
                </Field>
                <ErrorMessage name="banner_type" class="err" />
              </div>

              <!-- Enabled -->
              <div class="flex items-end md:col-span-2">
                <label class="flex items-center gap-2 bg-gray-50 border rounded-xl px-4 py-3 w-full">
                  <Field name="is_enabled" type="checkbox" :value="true" :unchecked-value="false" />
                  <div>
                    <div class="text-sm font-medium">Enabled</div>
                    <div class="text-xs text-gray-500">Controls visibility</div>
                  </div>
                </label>
              </div>
              <div class="md:col-span-2 -mt-2">
                <ErrorMessage name="is_enabled" class="err" />
              </div>
            </div>

            <!-- Names -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="label">Name (EN)</label>
                <Field name="name.en" class="form-input" placeholder="En name" />
                <ErrorMessage name="name.en" class="err" />
              </div>

              <div>
                <label class="label">Name (AR)</label>
                <Field name="name.ar" class="form-input" placeholder="Ar name" />
                <ErrorMessage name="name.ar" class="err" />
              </div>
            </div>

            <!--Descriptions -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="label">Description (EN)</label>
                <Field name="description.en" class="form-input" placeholder="En description" />
                <ErrorMessage name="description.en" class="err" />
              </div>

              <div>
                <label class="label">Description (AR)</label>
                <Field name="description.ar" class="form-input" placeholder="Ar description" />
                <ErrorMessage name="description.ar" class="err" />
              </div>
            </div>

            <!-- Hidden image field to register in VeeValidate -->
            <Field name="image" type="hidden" />

            <!-- Submit -->
            <div class="flex items-center justify-between pt-2">
              <div class="text-xs text-gray-500">
                {{ errors.image && submitCount > 0 ? 'Please fix image error to continue.' : ' ' }}
              </div>

              <button type="submit" :disabled="creating" class="btn-primary">
                {{ creating ? 'Uploading…' : 'Upload Banner' }}
              </button>
            </div>

            <div v-if="createErr" class="text-sm text-red-600">{{ createErr }}</div>
          </div>

          <!-- Right: Image picker + preview -->
          <div class="lg:col-span-4 space-y-2">
            <label class="label">Image *</label>

            <div class="border rounded-2xl p-4 bg-gray-50">
              <input type="file" accept="image/*" class="file-input"
                @change="e => onCreateFileChange(e, setFieldValue, setFieldError)" />

              <ErrorMessage name="image" class="err mt-2" />

              <div class="mt-4">
                <div class="preview-frame">
                  <img v-if="newPreview" :src="newPreview" class="preview-img" alt="Preview" />
                  <div v-else class="preview-empty">
                    <div class="text-sm text-gray-500">No preview</div>
                    <div class="text-xs text-gray-400">Choose an image to preview</div>
                  </div>
                </div>
                <div v-if="newFileMeta" class="text-xs text-gray-500 mt-2">
                  {{ newFileMeta }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </Form>

      <!-- List -->
      <div class="bg-white p-5 rounded-2xl shadow-sm ring-1 ring-black/[0.05]">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-gray-800">All Banners</h3>
          <div class="text-xs text-gray-500">
            {{ banners.length }} banner(s)
          </div>
        </div>

        <div v-if="listLoading" class="text-sm text-gray-500">Loading…</div>

        <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
          <div v-for="b in banners" :key="b.id"
            class="group rounded-2xl overflow-hidden bg-white border border-gray-200/70 shadow hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
            <!-- Image -->
            <div class="relative aspect-[4/3] bg-gray-50">
              <img v-if="b.image_url" :src="b.image_url" class="absolute inset-0 w-full h-full object-cover" />
              <div v-else class="absolute inset-0 flex items-center justify-center text-gray-400 text-sm">
                No Image
              </div>

              <!-- Status badge -->
              <div class="absolute top-2 left-2">
                <span :class="[
                  'px-2 py-0.5 rounded-full text-xs font-medium border',
                  b.is_enabled
                    ? 'bg-green-100 text-green-700 border-green-200'
                    : 'bg-red-100 text-red-700 border-red-200'
                ]">
                  {{ b.is_enabled ? 'Enabled' : 'Disabled' }}
                </span>
              </div>
            </div>

            <!-- Body -->
            <div class="p-3.5 space-y-3">
              <!-- Page -->
              <div>
                <label class="block text-[11px] text-gray-500 mb-1">Page</label>
                <select v-model="editDraft[b.id].page" class="w-full border rounded-lg px-2 py-1 text-xs">
                  <option v-for="p in PAGES" :key="p" :value="p">{{ p }}</option>
                </select>
              </div>

              <!-- Banner Type -->
              <div>
                <label class="block text-[11px] text-gray-500 mb-1">Banner Type</label>
                <select v-model="editDraft[b.id].banner_type" class="w-full border rounded-lg px-2 py-1 text-xs">
                  <option value="guest">Guest</option>
                  <option value="user">User</option>
                </select>
              </div>

              <!-- Names -->
              <div class="grid grid-cols-2 gap-2">
                <div>
                  <label class="block text-[11px] text-gray-500 mb-1">Name (EN)</label>
                  <input v-model="editDraft[b.id].name.en" class="w-full border rounded-lg px-2 py-1 text-xs"
                    placeholder="EN name" />
                </div>
                <div>
                  <label class="block text-[11px] text-gray-500 mb-1">Name (AR)</label>
                  <input v-model="editDraft[b.id].name.ar" class="w-full border rounded-lg px-2 py-1 text-xs"
                    placeholder="AR name" />
                </div>
              </div>

              <!-- Description -->
              <div class="grid grid-cols-2 gap-2">
                <div>
                  <label class="block text-[11px] text-gray-500 mb-1">Description (EN)</label>
                  <textarea v-model="editDraft[b.id].description.en" class="w-full border rounded-lg px-2 py-1 text-xs"
                    placeholder="EN description"></textarea>
                </div>
                <div>
                  <label class="block text-[11px] text-gray-500 mb-1">Description (AR)</label>
                  <textarea v-model="editDraft[b.id].description.ar" class="w-full border rounded-lg px-2 py-1 text-xs"
                    placeholder="AR description"></textarea>
                </div>
              </div>

              <!-- Toggle -->
              <label class="flex items-center justify-between gap-2 text-sm bg-gray-50 border rounded-xl px-3 py-2">
                <span class="text-xs">{{ editDraft[b.id].is_enabled ? 'Active' : 'Inactive' }}</span>
                <input type="checkbox" v-model="editDraft[b.id].is_enabled" />
              </label>

              <!-- Save details -->
              <button v-if="can('banners.update')" class="px-3 py-2 text-xs rounded-xl border bg-white hover:bg-gray-50 w-full"
                @click="saveBannerEdits(b.id)" :disabled="saving[b.id]">
                {{ saving[b.id] ? 'Saving…' : 'Save Details' }}
              </button>

              <!-- Replace image -->
              <div v-if="can('banners.update')" class="space-y-2">
                <input type="file" accept="image/*" @change="onReplaceFile(b.id, $event)"
                  class="block w-full text-xs text-gray-600" />
                <button class="px-2 py-2 border rounded-xl text-xs hover:bg-gray-50 w-full" @click="saveReplace(b.id)"
                  :disabled="!pendingImage[b.id] || saving[b.id]">
                  {{ saving[b.id] ? 'Saving…' : 'Save New Image' }}
                </button>
              </div>

              <!-- Delete -->
              <button v-if="can('banners.delete')"
                class="px-3 py-2 text-xs rounded-xl border border-red-200 text-red-700 bg-red-50 hover:bg-red-100 w-full"
                @click="remove(b.id)">
                Delete
              </button>
            </div>
          </div>

          <div v-if="!banners.length" class="col-span-full text-center text-gray-500 py-10">
            No banners found.
          </div>
        </div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, usePage } from '@inertiajs/vue3'
import { ref, reactive, onMounted, defineComponent, computed } from 'vue'

const inertiaPage = usePage()
const role = computed(() => inertiaPage.props.auth?.role)
const userPermissions = computed(() => inertiaPage.props.auth?.permissions ?? [])
function can(permission) {
  if (role.value === 'admin') return true
  return userPermissions.value.includes(permission)
}
import { Form, Field, ErrorMessage } from 'vee-validate'
import * as yup from 'yup'
import { BannerApi, buildCreateBannerFD, buildUpdateBannerFD } from '@/Services/banner'
import { useServerError } from '@/composables/useServerError'

const { show } = useServerError()

/* ---------- Consts ---------- */
const PAGES = [
  'home', 'about', 'contactUs', 'project', 'application', 'logos', 'our-services', 'qhse-policy'
]

/* ---------- Create Schema ---------- */
const createSchema = yup.object({
  page: yup.string().required('Page is required').oneOf(PAGES, 'Invalid page'),

  banner_type: yup.string().required('Banner type is required').oneOf(['guest', 'user'], 'Invalid banner type'),

  name: yup.object({
    en: yup.string().nullable(),
    ar: yup.string().nullable(),
  }).nullable(),

  description: yup.object({
    en: yup.string().nullable(),
    ar: yup.string().nullable(),
  }).nullable(),

  image: yup
    .mixed()
    .nullable()
    .required('Image is required')
    .test('fileType', 'Invalid image type', file =>
      !file || ['image/jpeg', 'image/png', 'image/jpg', 'image/svg+xml'].includes(file.type)
    )
    .test('fileSize', 'Max size is 6MB', file => !file || file.size <= 6 * 1024 * 1024),

  is_enabled: yup.boolean().oneOf([true, false], 'Status is required'),
})

const ALLOWED_IMAGE_TYPES = [
  'image/jpeg',
  'image/png',
  'image/jpg',
  'image/svg+xml',
]
const MAX_SIZE = 6 * 1024 * 1024


const createInitial = {
  page: 'home',
  banner_type: 'guest',
  is_enabled: true,
  image: null,
  name: { en: '', ar: '' },
  description: { en: '', ar: '' },
}

/* ---------- State ---------- */
const banners = ref([])
const listLoading = ref(false)

const creating = ref(false)
const createErr = ref('')

const newPreview = ref(null)
const newFileMeta = ref('')

const pendingImage = reactive({})
const saving = reactive({})
const editDraft = reactive({}) // { [id]: { page, is_enabled, name: {en,ar} } }

/* ---------- Helpers ---------- */
function onInvalidSubmit(ctx) {
  console.log('INVALID SUBMIT', ctx.errors)
}

function formatFileMeta(file) {
  const kb = Math.round(file.size / 1024)
  return `${file.name} • ${kb} KB`
}

/* Create: file change */
function onCreateFileChange(e, setFieldValue, setFieldError) {
  const file = e.target.files?.[0] ?? null
  if (!file) return

  if (!ALLOWED_IMAGE_TYPES.includes(file.type)) {
    setFieldValue('image', null, false)
    setFieldError('image', 'Invalid image type. Allowed: JPG, JPEG, PNG, SVG.')
    newPreview.value = null
    newFileMeta.value = ''
    return
  }

  if (file.size > MAX_SIZE) {
    setFieldValue('image', null, false)
    setFieldError('image', 'Image size must be less than 6MB.')
    newPreview.value = null
    newFileMeta.value = ''
    return
  }

  setFieldValue('image', file, true)
  newPreview.value = URL.createObjectURL(file)
  newFileMeta.value = formatFileMeta(file)
}



/* ---------- CRUD ---------- */
async function fetchBanners() {
  listLoading.value = true
  try {
    const list = await BannerApi.index()
    banners.value = list

    // init draft for each banner
    list.forEach(b => {
      if (!editDraft[b.id]) {
        editDraft[b.id] = {
          page: b.page ?? 'home',
          banner_type: b.banner_type ?? 'guest',
          is_enabled: b.is_enabled === true,
          name: {
            en: b?.name?.en ?? '',
            ar: b?.name?.ar ?? '',
          },
          description: {
            en: b?.description?.en ?? '',
            ar: b?.description?.ar ?? '',
          },

        }
      }
    })
  } finally {
    listLoading.value = false
  }
}

async function createBanner(values, { resetForm }) {
  creating.value = true
  createErr.value = ''

  try {
    console.log('Creating banner with values:', values)

    const fd = buildCreateBannerFD({
      image: values.image,
      is_enabled: values.is_enabled,
      page: values.page,
      banner_type: values.banner_type,
      name: values.name,
      description: values.description,
    })

    console.log('FormData entries:')
    for (let [key, value] of fd.entries()) {
      console.log(key, value)
    }

    await BannerApi.create(fd)
    await fetchBanners()

    resetForm({ values: { ...createInitial } })
    newPreview.value = null
    newFileMeta.value = ''
  } catch (e) {
    createErr.value = show(e)
  } finally {
    creating.value = false
  }
}

/* Toggle enabled (quick) */
async function toggleEnabled(banner) {
  const old = banner.is_enabled
  banner.is_enabled = !old

  try {
    const fd = buildUpdateBannerFD({ is_enabled: banner.is_enabled })
    await BannerApi.update(banner.id, fd)

    // keep draft consistent
    if (editDraft[banner.id]) editDraft[banner.id].is_enabled = banner.is_enabled
  } catch (e) {
    banner.is_enabled = old
    console.error('Toggle failed:', e)
  }
}

/* Replace image */
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

/* Save edits (name/page/is_enabled/banner_type) */
async function saveBannerEdits(id) {
  const draft = editDraft[id]
  if (!draft) return

  saving[id] = true
  try {
    const fd = buildUpdateBannerFD({
      page: draft.page,
      banner_type: draft.banner_type,
      is_enabled: draft.is_enabled,
      name: draft.name,
      description: draft.description,
    })
    await BannerApi.update(id, fd)
    await fetchBanners()
  } catch (e) {
    console.error('Save failed:', e)
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

/* ---------- Banner Card Component (inline) ---------- */
const BannerCard = defineComponent({
  name: 'BannerCard',
  props: {
    banner: { type: Object, required: true },
    pages: { type: Array, required: true },
    saving: { type: Boolean, default: false },
    pendingImage: { type: Object, default: null },
  },
  emits: ['toggle', 'replace-file', 'save', 'save-image', 'delete'],
  setup(props, { emit }) {
    // local reactive draft for card (keeps UI responsive without re-fetch on typing)
    const local = reactive({
      page: props.banner.page ?? 'home',
      is_enabled: props.banner.is_enabled === true,
      name_en: props.banner?.name?.en ?? '',
      name_ar: props.banner?.name?.ar ?? '',
    })

    // keep minimal: emit save with current local draft by attaching to banner object
    function save() {
      // attach a temp draft so parent can build FD
      props.banner.__draft = {
        page: local.page,
        is_enabled: local.is_enabled,
        name: { en: local.name_en, ar: local.name_ar },
      }
      emit('save', props.banner.id)
    }

    return { local, emit, save }
  },
  template: `
    <div class="group rounded-2xl overflow-hidden bg-white border border-gray-200/70 shadow hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
      <!-- Image -->
      <div class="relative aspect-[4/3] bg-gray-50">
        <img v-if="banner.image_url" :src="banner.image_url" class="absolute inset-0 w-full h-full object-cover" />
        <div v-else class="absolute inset-0 flex items-center justify-center text-gray-400 text-sm">No Image</div>

        <!-- Status badge -->
        <div class="absolute top-2 left-2">
          <span :class="[
            'px-2 py-0.5 rounded-full text-xs font-medium border',
            banner.is_enabled ? 'bg-green-100 text-green-700 border-green-200' : 'bg-red-100 text-red-700 border-red-200'
          ]">
            {{ banner.is_enabled ? 'Enabled' : 'Disabled' }}
          </span>
        </div>
      </div>

      <!-- Body -->
      <div class="p-3.5 space-y-3">
        <div class="grid grid-cols-2 gap-2">
          <div class="col-span-2">
            <label class="text-[11px] text-gray-500">Page</label>
            <select v-model="local.page" class="w-full border rounded-lg px-2 py-1 text-xs">
              <option v-for="p in pages" :key="p" :value="p">{{ p }}</option>
            </select>
          </div>

          <div>
            <label class="text-[11px] text-gray-500">Name (EN)</label>
            <input v-model="local.name_en" class="w-full border rounded-lg px-2 py-1 text-xs" placeholder="EN name" />
          </div>

          <div>
            <label class="text-[11px] text-gray-500">Name (AR)</label>
            <input v-model="local.name_ar" class="w-full border rounded-lg px-2 py-1 text-xs" placeholder="AR name" />
          </div>
        </div>

        <!-- Toggle -->
        <label class="flex items-center gap-2 text-sm bg-gray-50 border rounded-xl px-3 py-2">
          <input type="checkbox" :checked="banner.is_enabled" @change="$emit('toggle', banner)" />
          <span class="text-xs">{{ banner.is_enabled ? 'Active' : 'Inactive' }}</span>
        </label>

        <!-- Replace image -->
        <div class="space-y-2">
          <input type="file" accept="image/*" @change="$emit('replace-file', banner.id, $event)" class="block w-full text-xs text-gray-600" />
          <button
            class="px-2 py-1 border rounded text-xs hover:bg-gray-50 w-full"
            @click="$emit('save-image', banner.id)"
            :disabled="!pendingImage || saving"
          >
            {{ saving ? 'Saving…' : 'Save New Image' }}
          </button>
        </div>

        <!-- Save edits -->
        <button
          class="px-3 py-2 text-xs rounded-xl border bg-white hover:bg-gray-50 w-full"
          @click="save"
          :disabled="saving"
        >
          {{ saving ? 'Saving…' : 'Save Details' }}
        </button>

        <!-- Delete -->
        <button
          class="px-3 py-2 text-xs rounded-xl border border-red-200 text-red-700 bg-red-50 hover:bg-red-100 w-full"
          @click="$emit('delete', banner.id)"
        >
          Delete
        </button>
      </div>
    </div>
  `
})
</script>

<style scoped>
.form-input {
  @apply w-full border border-gray-300 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white;
}

.label {
  @apply block text-xs text-gray-500 mb-1;
}

.err {
  @apply text-xs text-red-600 mt-1;
}

.btn-primary {
  @apply px-4 py-2 rounded-xl bg-green-600 text-white disabled:opacity-60 hover:bg-green-700 transition;
}

.file-input {
  @apply block w-full text-sm text-gray-700 file:mr-4 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-sm file:font-medium file:bg-white file:text-gray-800 hover:file:bg-gray-100;
}

.preview-frame {
  @apply w-full rounded-2xl overflow-hidden border bg-white;
  aspect-ratio: 16 / 9;
}

.preview-img {
  @apply w-full h-full object-cover;
}

.preview-empty {
  @apply w-full h-full flex flex-col items-center justify-center text-center;
}
</style>
