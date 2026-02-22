<template>
  <Head title="Intro Management" />

  <AuthenticatedLayout>
    <div class="p-6 space-y-6">
      <!-- Header -->
      <div class="flex items-start justify-between gap-4">
        <div>
          <h2 class="text-2xl font-semibold text-dash-title">Intro Management</h2>
        </div>

        <div class="flex gap-2">
          <!-- <button
            @click="fetchIntros"
            class="px-3 py-2 text-sm border rounded-xl hover:bg-gray-50 inline-flex items-center gap-2"
          >
            <span class="text-base">↻</span>
            Refresh
          </button> -->

          <button
            v-if="can('intro.create')"
            @click="openCreate"
            class="px-3 py-2 rounded-xl bg-black text-white hover:bg-gray-800 inline-flex items-center gap-2"
          >
            <span class="text-base">+</span>
            Add Intro
          </button>
        </div>
      </div>

      <!-- List -->
      <div class="bg-white p-5 rounded-2xl shadow-sm ring-1 ring-black/[0.05]">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-gray-800">Intro Screens</h3>
          <div class="text-xs text-gray-500">{{ intros.length }} intro(s)</div>
        </div>

        <div v-if="listLoading" class="text-sm text-gray-500">Loading…</div>

        <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-5">
          <div
            v-for="item in intros"
            :key="item.id"
            class="group rounded-2xl overflow-hidden bg-white border border-gray-200/70 shadow hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200"
          >
            <!-- Image -->
            <div class="relative aspect-[4/3] bg-gray-50">
              <img
                v-if="item.image_url"
                :src="item.image_url"
                class="absolute inset-0 w-full h-full object-cover"
              />
              <div v-else class="absolute inset-0 flex items-center justify-center text-gray-400 text-sm">
                No Image
              </div>

              <div class="absolute top-2 left-2 flex flex-wrap gap-2">
                <span class="inline-flex items-center rounded-full bg-white/90 px-2 py-0.5 text-[11px] border">
                  Order: {{ item.order ?? '—' }}
                </span>
                <span
                  :class="[
                    'inline-flex items-center rounded-full px-2 py-0.5 text-[11px] border',
                    item.is_enabled
                      ? 'bg-green-100 text-green-700 border-green-200'
                      : 'bg-red-100 text-red-700 border-red-200'
                  ]"
                >
                  {{ item.is_enabled ? 'Enabled' : 'Disabled' }}
                </span>
              </div>
            </div>

            <!-- Body -->
            <div class="p-3.5 space-y-3">
              <div class="text-sm font-semibold text-gray-900">
                {{ item.name_en || '—' }}
                <span v-if="item.name_ar" class="text-gray-400"> / </span>
                <span dir="rtl">{{ item.name_ar }}</span>
              </div>

              <div class="text-xs text-gray-600 space-y-1">
                <p class="line-clamp-2">{{ item.description_en || '—' }}</p>
                <p dir="rtl" class="text-gray-500 line-clamp-2">{{ item.description_ar || '—' }}</p>
              </div>

              <div class="flex gap-2 pt-2">
                <button
                  v-if="can('intro.update')"
                  class="px-3 py-2 text-xs rounded-xl border bg-white hover:bg-gray-50"
                  @click="openEdit(item)"
                >
                  Edit
                </button>
                <button
                  v-if="can('intro.delete')"
                  class="px-3 py-2 text-xs rounded-xl border border-red-200 text-red-700 bg-red-50 hover:bg-red-100"
                  @click="remove(item.id)"
                >
                  Delete
                </button>
              </div>
            </div>
          </div>

          <div v-if="!intros.length" class="col-span-full text-center text-gray-500 py-10">
            No intros found.
          </div>
        </div>
      </div>

      <!-- Modal -->
      <div v-if="showModal" class="fixed inset-0 z-50 back-drop" @keydown.esc="closeModal">
        <div class="absolute inset-0 bg-black/40" @click="closeModal"></div>

        <div class="relative mx-auto w-full max-w-3xl p-4 sm:p-6 h-full flex items-center justify-center">
          <div class="bg-white rounded-2xl shadow-lg w-full max-h-[90vh] overflow-hidden">
            <!-- Header -->
            <div class="sticky top-0 z-10 bg-white border-b px-5 py-3 flex justify-between items-center">
              <div>
                <h3 class="text-lg font-bold">{{ editingId ? 'Edit Intro' : 'Add Intro' }}</h3>
                <p class="text-xs text-gray-500">
                  {{ editingId ? 'Update details or replace the image.' : 'Fill all required fields and upload image.' }}
                </p>
              </div>
              <button @click="closeModal" class="text-gray-400 hover:text-gray-600">✕</button>
            </div>

            <!-- Body -->
            <Form
              :key="formKey"
              :validation-schema="editingId ? editSchema : createSchema"
              :initial-values="initialValues"
              @submit="submitForm"
              @invalid-submit="onInvalidSubmit"
              v-slot="{ setFieldValue, submitCount, errors, values }"
              class="px-5 py-4 overflow-y-auto max-h-[70vh] space-y-4"
            >
              <!-- Top alert -->
              <div
                v-if="errorMsg"
                class="rounded-xl border border-red-200 bg-red-50 text-red-700 px-3 py-2 text-sm"
              >
                {{ errorMsg }}
              </div>

              <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
                <!-- Left fields -->
                <div class="lg:col-span-8 space-y-4">
                  <!-- Names -->
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                      <label class="label">Name (EN) *</label>
                      <Field name="name.en" class="form-input" placeholder="Name (EN)" />
                      <ErrorMessage name="name.en" class="err" />
                    </div>

                    <div>
                      <label class="label">Name (AR) *</label>
                      <Field name="name.ar" class="form-input" placeholder="الاسم (AR)" />
                      <ErrorMessage name="name.ar" class="err" />
                    </div>
                  </div>

                  <!-- Descriptions -->
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                      <label class="label">Description (EN) *</label>
                      <Field as="textarea" name="description.en" class="form-input h-28" placeholder="Description (EN)" />
                      <ErrorMessage name="description.en" class="err" />
                    </div>

                    <div>
                      <label class="label">Description (AR) *</label>
                      <Field as="textarea" name="description.ar" class="form-input h-28" placeholder="الوصف (AR)" />
                      <ErrorMessage name="description.ar" class="err" />
                    </div>
                  </div>

                  <!-- Order + Enabled -->
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-end">
                    <div>
                      <label class="label">Order</label>
                      <Field name="order" type="number" class="form-input" placeholder="Order" />
                      <ErrorMessage name="order" class="err" />
                    </div>

                    <div>
                      <label class="flex items-center gap-2 bg-gray-50 border rounded-xl px-4 py-3 w-full">
                        <Field
                          name="is_enabled"
                          type="checkbox"
                          :value="true"
                          :unchecked-value="false"
                        />
                        <div>
                          <div class="text-sm font-medium">Enabled</div>
                          <div class="text-xs text-gray-500">Controls visibility</div>
                        </div>
                      </label>
                      <ErrorMessage name="is_enabled" class="err" />
                    </div>
                  </div>
                </div>

                <!-- Right: Image -->
                <div class="lg:col-span-4 space-y-2">
                  <label class="label">
                    Image
                    <span class="text-red-600" v-if="!editingId">*</span>
                    <span class="text-gray-400" v-else>(optional)</span>
                  </label>

                  <!-- register image field -->
                  <Field name="image" type="hidden" />

                  <div class="border rounded-2xl p-4 bg-gray-50">
                    <input
                      type="file"
                      accept="image/*"
                      class="file-input"
                      @change="e => onModalFileChange(e, setFieldValue)"
                    />

                    <ErrorMessage v-if="submitCount > 0" name="image" class="err mt-2" />

                    <div class="mt-4">
                      <div class="preview-frame">
                        <img
                          v-if="imagePreview"
                          :src="imagePreview"
                          class="preview-img"
                          alt="Preview"
                        />
                        <div v-else class="preview-empty">
                          <div class="text-sm text-gray-500">No preview</div>
                          <div class="text-xs text-gray-400">
                            {{ editingId ? 'Keep existing image if you don’t upload a new one.' : 'Choose an image to preview' }}
                          </div>
                        </div>
                      </div>

                      <div v-if="fileMeta" class="text-xs text-gray-500 mt-2">
                        {{ fileMeta }}
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Footer -->
              <div class="sticky bottom-0 bg-white border-t -mx-5 px-5 py-3 flex gap-3 justify-end">
                <button
                  type="button"
                  class="px-3 py-2 border rounded-xl"
                  @click="closeModal"
                >
                  Cancel
                </button>

                <button
                  type="submit"
                  :disabled="loading"
                  class="px-4 py-2 rounded-xl bg-green-600 text-white disabled:opacity-60 hover:bg-green-700"
                >
                  {{ loading ? 'Saving…' : (editingId ? 'Update' : 'Create') }}
                </button>
              </div>
            </Form>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, usePage } from '@inertiajs/vue3'
import { ref, onMounted, computed } from 'vue'

const inertiaPage = usePage()
const role = computed(() => inertiaPage.props.auth?.role)
const userPermissions = computed(() => inertiaPage.props.auth?.permissions ?? [])
function can(permission) {
  if (role.value === 'admin') return true
  return userPermissions.value.includes(permission)
}
import { Form, Field, ErrorMessage } from 'vee-validate'
import * as yup from 'yup'
import { IntroApi, buildIntroFormData } from '@/Services/intro'
import { useServerError } from '@/composables/useServerError'

const { show } = useServerError()

/* ---------- List ---------- */
const intros = ref([])
const listLoading = ref(false)

/* ---------- Modal state ---------- */
const showModal = ref(false)
const editingId = ref(null)
const loading = ref(false)
const errorMsg = ref('')

const imagePreview = ref(null)
const fileMeta = ref('')
const formKey = ref(0)

/* ---------- Initial values ---------- */
const initialValues = ref({
  name: { en: '', ar: '' },
  description: { en: '', ar: '' },
  image: null,
  order: '',
  is_enabled: true,
})

/* ---------- Schema ---------- */
const baseSchema = {
  name: yup.object({
    en: yup.string().required('Name (EN) is required'),
    ar: yup.string().required('Name (AR) is required'),
  }),
  description: yup.object({
    en: yup.string().required('Description (EN) is required'),
    ar: yup.string().required('Description (AR) is required'),
  }),
  order: yup
    .number()
    .transform((v, o) => (o === '' || o === null || o === undefined ? undefined : v))
    .nullable()
    .min(0, 'Order must be >= 0'),
  is_enabled: yup.boolean().oneOf([true, false], 'Status is required'),
}

const fileSchema = yup
  .mixed()
  .nullable()
  .test('fileType', 'Invalid image type', file =>
    !file || ['image/jpeg','image/png','image/jpg','image/svg+xml'].includes(file.type)
  )
  .test('fileSize', 'Max size is 6MB', file => !file || file.size <= 6 * 1024 * 1024)

const createSchema = yup.object({
  ...baseSchema,
  image: fileSchema.required('Image is required'),
})

const editSchema = yup.object({
  ...baseSchema,
  image: fileSchema.notRequired(), // optional on edit
})

/* ---------- Helpers ---------- */
function onInvalidSubmit(ctx) {
  console.log('INVALID SUBMIT', ctx.errors)
  errorMsg.value = 'Please fix the highlighted fields.'
}

function formatFileMeta(file) {
  const kb = Math.round(file.size / 1024)
  return `${file.name} • ${kb} KB`
}

/* ---------- Open/Close ---------- */
function openCreate() {
  editingId.value = null
  errorMsg.value = ''
  imagePreview.value = null
  fileMeta.value = ''

  initialValues.value = {
    name: { en: '', ar: '' },
    description: { en: '', ar: '' },
    image: null,
    order: '',
    is_enabled: true,
  }

  formKey.value++
  showModal.value = true
}

function openEdit(item) {
  editingId.value = item.id
  errorMsg.value = ''
  fileMeta.value = ''

  initialValues.value = {
    name: { en: item.name_en || '', ar: item.name_ar || '' },
    description: { en: item.description_en || '', ar: item.description_ar || '' },
    image: null, // keep null unless user picks new one
    order: item.order ?? '',
    is_enabled: !!item.is_enabled,
  }

  imagePreview.value = item.image_url || null
  formKey.value++
  showModal.value = true
}

function closeModal() {
  showModal.value = false
}

/* ---------- File handling ---------- */
function onModalFileChange(e, setFieldValue) {
  const file = e.target.files?.[0] ?? null
  if (!file) return

  setFieldValue('image', file, true) // validate immediately
  imagePreview.value = URL.createObjectURL(file)
  fileMeta.value = formatFileMeta(file)
}

/* ---------- Fetch ---------- */
async function fetchIntros() {
  listLoading.value = true
  try {
    intros.value = await IntroApi.index()
  } finally {
    listLoading.value = false
  }
}

/* ---------- Submit ---------- */
async function submitForm(values) {
  loading.value = true
  errorMsg.value = ''

  try {
    const fd = buildIntroFormData(values)

    if (editingId.value) await IntroApi.update(editingId.value, fd)
    else await IntroApi.create(fd)

    await fetchIntros()
    closeModal()
  } catch (e) {
    // always use show(e)
    errorMsg.value = show(e)
  } finally {
    loading.value = false
  }
}

/* ---------- Delete ---------- */
async function remove(id) {
  if (!confirm('Are you sure you want to delete this intro?')) return
  try {
    await IntroApi.destroy(id)
    await fetchIntros()
  } catch (e) {
    alert(show(e))
  }
}

onMounted(fetchIntros)
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
.file-input {
  @apply block w-full text-sm text-gray-700 file:mr-4 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-sm file:font-medium
  file:bg-white file:text-gray-800 hover:file:bg-gray-100;
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
.back-drop {
  margin-top: -25px !important;
}
</style>
