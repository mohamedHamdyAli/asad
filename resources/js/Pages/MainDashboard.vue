<template>

  <Head title="Admin Dashboard" />

  <AuthenticatedLayout>
    <div class="p-6 space-y-8">
      <!-- ================= HEADER ================= -->
      <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
        </div>


      </div>

      <!-- ================= TOP ALERT ================= -->
      <div v-if="pageError"
        class="rounded-2xl border border-red-200 bg-red-50 text-red-700 px-4 py-3 flex items-start gap-3">
        <Icon icon="mdi:alert-circle-outline" class="text-xl mt-0.5" />
        <div class="text-sm">
          <div class="font-semibold">Some data couldn’t be loaded</div>
          <div class="text-red-700/90">{{ pageError }}</div>
        </div>
      </div>

      <!-- ================= KPI CARDS ================= -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <SmartCard v-if="can('units.view')" title="Units" :value="stats.units" icon="mdi:domain" color="blue" hint="All units"
          :loading="loadingStats" @click="go('unit-management')" />

        <SmartCard v-if="can('users.view')" title="Users" :value="stats.users" icon="mdi:account-group-outline" color="indigo"
          hint="Registered users" :loading="loadingStats" @click="go('roles-management')" />

        <!-- <SmartCard v-if="can('unit_issues.view')" title="Open Issues" :value="stats.openIssues" icon="mdi:alert-circle-outline" color="red"
          hint="Requires attention" pulse :loading="loadingStats" @click="go('unit-issues')" /> -->

        <SmartCard v-if="can('unit_quotes.view')" title="Quotations" :value="stats.quotations" icon="mdi:file-document-outline" color="emerald"
          hint="Client requests" :loading="loadingStats" @click="go('unit-quotes-responses')" />
      </div>

      <!-- ================= MANAGEMENT (replaces quick actions) ================= -->
      <div class="bg-white rounded-2xl shadow-sm ring-1 ring-black/[0.05] p-6">
        <div class="flex items-center justify-between mb-4">
          <div>
            <h3 class="text-lg font-semibold text-gray-900">Management</h3>
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
          <button v-for="m in management" :key="m.route" @click="go(m.route)"
            class="text-left rounded-2xl border bg-white hover:bg-gray-50 p-4 transition shadow-sm hover:shadow flex items-start gap-3">
            <span class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center">
              <Icon :icon="m.icon" class="text-xl text-gray-800" />
            </span>

            <span class="min-w-0">
              <span class="block font-semibold text-gray-900 truncate">{{ m.title }}</span>
              <span class="block text-xs text-gray-500 mt-0.5 truncate">{{ m.subtitle }}</span>
            </span>
          </button>
        </div>
      </div>

      <!-- ================= LATEST QUOTATIONS (BOTTOM) ================= -->
      <div v-if="can('unit_quotes.view')" class="bg-white rounded-2xl shadow-sm ring-1 ring-black/[0.05] p-6">
        <div class="flex items-center justify-between mb-4">
          <div>
            <h3 class="text-lg font-semibold text-gray-900">Latest Quotations</h3>
            <p class="text-sm text-gray-500">Most recent quotation requests.</p>
          </div>

          <button class="text-sm text-blue-600 hover:underline" @click="go('unit-quotes-responses')">
            View all
          </button>
        </div>

        <div v-if="loadingQuotes" class="text-sm text-gray-500">Loading…</div>

        <div v-else-if="!latestQuotations.length" class="text-sm text-gray-500">
          No quotations yet.
        </div>

        <div v-else class="space-y-3">
          <div v-for="q in latestQuotations" :key="q.id"
            class="flex items-start justify-between gap-4 border rounded-2xl p-4 hover:bg-gray-50 transition">
            <div class="flex gap-3">
              <div class="mt-0.5">
                <Icon icon="mdi:file-document-outline" class="text-xl text-emerald-600" />
              </div>

              <div class="space-y-1">
                <div class="font-semibold text-gray-900 leading-5">
                  {{ q.title || `Quotation #${q.id}` }}
                </div>

                <div class="text-xs text-gray-500">
                  <span v-if="q.unit_name">{{ q.unit_name }}</span>
                  <span v-else-if="q.unit_id">Unit #{{ q.unit_id }}</span>
                  <span v-else>—</span>

                  <span class="mx-2">•</span>
                  <span>{{ formatDate(q.created_at) }}</span>
                </div>

                <div v-if="q.client_name || q.client_phone" class="text-xs text-gray-600">
                  <span v-if="q.client_name">{{ q.client_name }}</span>
                  <span v-if="q.client_name && q.client_phone" class="mx-2">•</span>
                  <span v-if="q.client_phone">{{ q.client_phone }}</span>
                </div>
              </div>
            </div>

            <span
              class="shrink-0 inline-flex items-center px-2 py-1 text-[11px] rounded-full border bg-emerald-50 text-emerald-700">
              NEW
            </span>
          </div>
        </div>

        <div v-if="quotesError" class="mt-4 text-sm text-red-600">
          {{ quotesError }}
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import { Head, router, usePage } from '@inertiajs/vue3'
import { Icon } from '@iconify/vue'

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import SmartCard from '@/Components/SmartCard.vue'
import { useServerError } from '@/composables/useServerError'

const { show } = useServerError()

/* ---------------- permissions ---------------- */
const page = usePage()
const role = computed(() => page.props.auth?.role)
const userPermissions = computed(() => page.props.auth?.permissions ?? [])

function can(permission) {
  if (role.value === 'admin') return true
  return userPermissions.value.includes(permission)
}

/* ---------------- endpoints ---------------- */
const UNITS_URL = '/api/units'
const USERS_URL = '/api/users'
const ISSUES_URL = '/api/unit-issues'
const QUOTES_URL = '/api/unit-quotes'

/* ---------------- state ---------------- */
const loadingAll = ref(false)
const loadingStats = ref(false)
const loadingQuotes = ref(false)

const pageError = ref('')
const quotesError = ref('')

const stats = ref({
  units: 0,
  users: 0,
  openIssues: 0,
  quotations: 0,
})

const latestQuotations = ref([])

/* ---------------- management cards ---------------- */
const allManagement = [
  { icon: 'mdi:domain', title: 'Units', subtitle: 'Create & manage units', route: 'unit-management', permission: 'units.view' },
  { icon: 'mdi:account-hard-hat', title: 'Contractors', subtitle: 'Manage contractors', route: 'contractors-management', permission: 'contractors.view' },
  { icon: 'mdi:account-tie', title: 'Consultants', subtitle: 'Manage consultants', route: 'Consultants-management', permission: 'consultants.view' },
  { icon: 'mdi:file-document', title: 'Quotations', subtitle: 'View client requests', route: 'unit-quotes-responses', permission: 'unit_quote_responses.view' },
  { icon: 'mdi:account-key', title: 'Users & Roles', subtitle: 'Roles & permissions', route: 'roles-management', permission: 'roles.view' },
]

const management = computed(() => allManagement.filter(m => can(m.permission)))

/* ---------------- helpers ---------------- */
function go(routeName) {
  router.visit(route(routeName))
}

function unwrapList(payload) {
  if (Array.isArray(payload)) return payload
  if (Array.isArray(payload?.data)) return payload.data
  if (Array.isArray(payload?.message)) return payload.message
  if (Array.isArray(payload?.data?.data)) return payload.data.data
  return []
}

function formatDate(iso) {
  if (!iso) return '—'
  try {
    return new Date(iso).toLocaleString()
  } catch {
    return iso
  }
}

function normalizeQuotation(row = {}) {
  return {
    id: row.id,
    title: row.title?.en || row.title_en || row.title || '',
    unit_id: row.unit_id || row.unitId || null,
    unit_name: row.unit?.name?.en || row.unit_name || row.unitName || '',
    client_name: row.user?.name || row.client_name || row.name || '',
    client_phone: row.user?.phone || row.client_phone || row.phone || '',
    created_at: row.created_at || row.createdAt || null,
  }
}

/* ---------------- fetchers ---------------- */
async function fetchStats() {
  loadingStats.value = true
  pageError.value = ''

  try {
    const [unitsRes, usersRes, issuesRes, quotesRes] = await Promise.allSettled([
      axios.get(UNITS_URL),
      axios.get(USERS_URL),
      axios.get(ISSUES_URL),
      axios.get(QUOTES_URL),
    ])

    if (unitsRes.status === 'fulfilled') stats.value.units = unwrapList(unitsRes.value.data).length
    else pageError.value = show(unitsRes.reason)

    if (usersRes.status === 'fulfilled') stats.value.users = unwrapList(usersRes.value.data).length
    else pageError.value = pageError.value || show(usersRes.reason)

    if (issuesRes.status === 'fulfilled') {
      const issues = unwrapList(issuesRes.value.data)
      stats.value.openIssues = issues.filter(i => String(i?.status).toLowerCase() === 'open').length
    }
    else pageError.value = pageError.value || show(issuesRes.reason)

    if (quotesRes.status === 'fulfilled') stats.value.quotations = unwrapList(quotesRes.value.data).length
    else pageError.value = pageError.value || show(quotesRes.reason)
  } finally {
    loadingStats.value = false
  }
}

async function fetchLatestQuotations() {
  loadingQuotes.value = true
  quotesError.value = ''

  try {
    const { data } = await axios.get(QUOTES_URL)

    latestQuotations.value = unwrapList(data)
      .map(normalizeQuotation)
      .sort((a, b) => {
        const da = a.created_at ? new Date(a.created_at).getTime() : 0
        const db = b.created_at ? new Date(b.created_at).getTime() : 0
        return db - da
      })
      .slice(0, 6)
  } catch (e) {
    quotesError.value = show(e) || 'Failed to load quotations.'
    latestQuotations.value = []
  } finally {
    loadingQuotes.value = false
  }
}

async function refreshAll() {
  loadingAll.value = true
  pageError.value = ''
  quotesError.value = ''
  try {
    await Promise.all([fetchStats(), fetchLatestQuotations()])
  } finally {
    loadingAll.value = false
  }
}

onMounted(refreshAll)
</script>

<style scoped>
/* no extra styles needed */
</style>
