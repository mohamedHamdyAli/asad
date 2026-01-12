<template>
    <AuthenticatedLayout>
        <div class="p-6 space-y-10">

            <!-- ========================================= -->
            <!--              SEARCH + FILTERS            -->
            <!-- ========================================= -->
            <div class="bg-white p-4 rounded-xl shadow flex flex-wrap gap-4 items-center">

                <input v-model="search" placeholder="Search by EN/AR title…" class="form-input w-64" />

                <select v-model="filterType" class="form-input w-40">
                    <option value="">All Types</option>
                    <option value="building">Building</option>
                    <option value="price">Price</option>
                </select>

                <select v-model="priceFilter" class="form-input w-40" v-if="filterType === 'building'">
                    <option value="">All</option>
                    <option value="has_price">With Price</option>
                    <option value="no_price">No Price</option>
                </select>

                <!-- Bulk Delete -->
                <button v-if="selected.length" @click="bulkDelete" class="px-4 py-2 bg-red-600 text-white rounded">
                    Delete Selected ({{ selected.length }})
                </button>

                <!-- Add Buttons -->
                <div class="ml-auto flex gap-3">
                    <button @click="openCreate('building')" class="btn-green">+ Building</button>
                    <button @click="openCreate('price')" class="btn-blue">+ Price</button>
                </div>

            </div>

            <!-- ========================================= -->
            <!--              DATA TABLES                  -->
            <!-- ========================================= -->
            <div class="bg-white rounded-xl shadow p-6">

                <table class="min-w-full border text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="table-header w-10 text-center">
                                <input type="checkbox" @change="toggleAll" :checked="allSelected" />
                            </th>
                            <th class="table-header w-16  text-center">
                                #
                            </th>
                            <th class="table-header cursor-pointer" @click="sortBy('title')">
                                Title
                            </th>
                            <th v-if="filterType !== 'price'" class="table-header w-24 cursor-pointer"
                                @click="sortBy('price')">
                                Price
                            </th>
                            <th class="table-header w-24 text-center">Type</th>
                            <th class="table-header w-32 text-center">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr v-for="(item, index) in paginatedItems" :key="item.uid" class="border-b hover:bg-gray-50 transition">
                            <!-- Checkbox -->
                            <td class="table-cell text-center">
                                <input type="checkbox" :value="item.uid" v-model="selected" />
                            </td>

                            <!-- Order -->
                            <td class="table-cell font-medium text-gray-700">
                                {{ (page - 1) * perPage + index + 1 }}
                            </td>

                            <!-- Title -->
                            <td class="table-cell">
                                <div class="font-semibold text-gray-900">
                                    {{ parseTitle(item.title).en || "—" }}
                                </div>
                                <div class="text-gray-500 text-xs">
                                    {{ parseTitle(item.title).ar || "—" }}
                                </div>
                            </td>

                            <!-- Price (only for buildings) -->
                            <td v-if="item.type === 'building'" class="table-cell">
                                {{ item.price ?? "—" }}
                            </td>
                            <td v-else-if="filterType !== 'price'" class="table-cell text-gray-400">—</td>

                            <!-- Type -->
                            <td class="table-cell text-center">
                                <span class="px-2 py-1 rounded text-xs font-medium" :class="item.type === 'building'
                                    ? 'bg-blue-100 text-blue-700'
                                    : 'bg-green-100 text-green-700'
                                    ">
                                    {{ item.type }}
                                </span>
                            </td>

                            <!-- Actions -->
                            <td class="p-3 text-center">
                                <div class="action-buttons">
                                    <button @click="openEdit(item)" class="btn-edit">Edit</button>
                                    <button @click="deleteOne(item)" class="btn-delete">Delete</button>
                                </div>
                            </td>

                        </tr>
                    </tbody>
                </table>


                <!-- PAGINATION -->
                <div class="flex justify-between items-center mt-4">
                    <select v-model="perPage" class="form-input w-24">
                        <option v-for="n in [10, 25, 50]" :key="n">{{ n }}</option>
                    </select>

                    <div class="space-x-2">
                        <button @click="prevPage" class="btn-gray" :disabled="page === 1">Prev</button>
                        <button @click="nextPage" class="btn-gray" :disabled="page === totalPages">Next</button>
                    </div>
                </div>
            </div>

            <!-- MODAL -->
            <QuoteTypeModal v-if="modal.open" :mode="modal.mode" :type="modal.type" :item="modal.item"
                @close="modal.open = false" @saved="fetchAll" />

        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, onMounted , watch} from "vue"
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import { TypeOfBuildingApi } from "@/Services/typeOfBuilding"
import { TypeOfPriceApi } from "@/Services/typeOfPrice"
import QuoteTypeModal from "@/Components/QuoteTypeModal.vue"

const buildings = ref([])
const prices = ref([])

const search = ref("")
const filterType = ref("")
const priceFilter = ref("")
const page = ref(1)
const perPage = ref(10)

const modal = ref({
    open: false,
    mode: "add",
    type: null,
    item: null,
})

const selected = ref([])
const sort = ref({ key: "id", dir: "desc" })

function parseTitle(val) {
    if (!val) return { en: "", ar: "" }

    if (typeof val === "string") {
        try {
            const parsed = JSON.parse(val)
            return {
                en: parsed?.en ?? "",
                ar: parsed?.ar ?? "",
            }
        } catch {
            return { en: "", ar: "" }
        }
    }

    return {
        en: val?.en ?? "",
        ar: val?.ar ?? "",
    }
}

function openCreate(type) {
    modal.value = { open: true, mode: "add", type, item: null }
}

async function openEdit(item) {
  modal.value = {
    open: true,
    mode: "edit",
    type: item.type,
    item: null,
  }

  try {
    const data =
      item.type === "building"
        ? await TypeOfBuildingApi.show(item.id)
        : await TypeOfPriceApi.show(item.id)

    modal.value.item = data
  } catch (e) {
    console.error(e)
    alert("Failed to load item data")
    modal.value.open = false
  }
}


function deleteOne(item) {
    if (!confirm("Delete this item?")) return

    if (item.type === "building") TypeOfBuildingApi.remove(item.id)
    else TypeOfPriceApi.remove(item.id)

    fetchAll()
}

function toggleAll(e) {
    if (e.target.checked) {
        selected.value = merged.value.map((i) => i.uid)
    } else {
        selected.value = []
    }
}

function bulkDelete() {
    if (!confirm("Delete selected items?")) return

    selected.value.forEach((uid) => {
        const item = merged.value.find((i) => i.uid === uid)
        deleteOne(item)
    })

    selected.value = []
}

const merged = computed(() => {
    const mappedBuildings = buildings.value.map((b) => ({ ...b, type: "building", uid: "b" + b.id }))
    const mappedPrices = prices.value.map((p) => ({ ...p, type: "price", uid: "p" + p.id }))
    return [...mappedBuildings, ...mappedPrices]
})

const filtered = computed(() => {
    let items = merged.value

    if (filterType.value) items = items.filter((i) => i.type === filterType.value)

    if (priceFilter.value === "has_price") {
        items = items.filter((i) => i.type === "building" && i.price)
    }

    if (priceFilter.value === "no_price") {
        items = items.filter((i) => i.type === "building" && !i.price)
    }

    if (search.value) {
        const s = search.value.toLowerCase()
        items = items.filter((i) => {
            const title = parseTitle(i.title)
            return title.en.toLowerCase().includes(s) || title.ar.toLowerCase().includes(s)
        })
    }

    items = [...items].sort((a, b) => {
        let valA = a[sort.value.key]
        let valB = b[sort.value.key]

        if (sort.value.key === "title") {
            valA = parseTitle(a.title).en
            valB = parseTitle(b.title).en
        }

        return sort.value.dir === "asc" ? (valA > valB ? 1 : -1) : valA < valB ? 1 : -1
    })

    return items
})

const totalPages = computed(() => Math.ceil(filtered.value.length / perPage.value))

const paginatedItems = computed(() => {
    const start = (page.value - 1) * perPage.value
    return filtered.value.slice(start, start + perPage.value)
})

function sortBy(key) {
    if (sort.value.key === key) {
        sort.value.dir = sort.value.dir === "asc" ? "desc" : "asc"
    } else {
        sort.value.key = key
        sort.value.dir = "asc"
    }
}

function nextPage() {
    if (page.value < totalPages.value) page.value++
}

function prevPage() {
    if (page.value > 1) page.value--
}

async function fetchAll() {
    buildings.value = await TypeOfBuildingApi.list()
    prices.value = await TypeOfPriceApi.list()
}
function safeTitle(data) {
    if (!data) return { en: '', ar: '' }

    if (typeof data === 'string') {
        try {
            const parsed = JSON.parse(data)
            return {
                en: parsed?.en ?? '',
                ar: parsed?.ar ?? ''
            }
        } catch {
            return { en: '', ar: '' }
        }
    }

    return {
        en: data?.en ?? '',
        ar: data?.ar ?? ''
    }
}

watch([search, filterType, priceFilter], () => {
  page.value = 1
})

const allSelected = computed(() => {
  return (
    selected.value.length > 0 &&
    selected.value.length === merged.value.length
  )
})



onMounted(fetchAll)
</script>

<style scoped>
.btn-green {
    @apply px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700;
}

.btn-blue {
    @apply px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700;
}

.btn-blue-sm {
    @apply px-2 py-1 text-xs bg-blue-600 text-white rounded;
}

.btn-red-sm {
    @apply px-2 py-1 text-xs bg-red-600 text-white rounded;
}

.btn-gray {
    @apply px-3 py-1 border rounded hover:bg-gray-100 disabled:opacity-50;
}

.form-input {
    @apply border border-gray-300 rounded px-3 py-2 text-sm;
}

.table-cell {
    @apply p-3 align-middle;
}

.table-header {
    @apply p-3 font-semibold bg-gray-100 text-left;
}

.action-buttons {
    display: flex;
    gap: 8px;
    /* مساحة ثابتة بين الزرين */
    align-items: center;
}

.btn-edit,
.btn-delete {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 6px 14px;
    font-size: 13px;
    font-weight: 600;
    color: #fff;
    border-radius: 6px;
    cursor: pointer;
    white-space: nowrap;
    transition: background 0.2s ease-in-out;
}

/* Edit Button */
.btn-edit {
    background-color: #2563eb;
    /* Blue-600 */
}

.btn-edit:hover {
    background-color: #1d4ed8;
    /* Blue-700 */
}

/* Delete Button */
.btn-delete {
    background-color: #dc2626;
    /* Red-600 */
}

.btn-delete:hover {
    background-color: #b91c1c;
    /* Red-700 */
}
</style>
