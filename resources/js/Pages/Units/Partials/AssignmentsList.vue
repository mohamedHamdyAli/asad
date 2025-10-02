<template>
  <div>
    <!-- Header -->
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-lg font-semibold">Assigned {{ typeLabel }}</h3>
      <button
        @click="openAssign"
        class="px-3 py-1 border rounded bg-green-600 text-white hover:bg-green-700"
      >
        + Assign {{ typeLabel.slice(0, -1) }}
      </button>
    </div>

    <!-- List -->
    <ul v-if="items.length" class="divide-y divide-gray-200">
      <li v-for="it in items" :key="it.id" class="flex items-center justify-between py-3">
        <div class="flex items-center gap-3">
          <img
            v-if="it[type]?.image"
            :src="`/storage/${it[type].image}`"
            class="w-10 h-10 rounded-full border object-cover"
          />
          <div>
            <div class="font-medium text-gray-800">{{ it[type]?.title?.en || '—' }}</div>
            <div class="text-xs text-gray-500">{{ it[type]?.email }}</div>
          </div>
        </div>
        <button
          class="text-sm text-red-600 hover:underline"
          @click="remove(it.id)"
        >
          Remove
        </button>
      </li>
    </ul>
    <div v-else class="text-center text-gray-500 py-6">
      No {{ typeLabel.toLowerCase() }} assigned.
    </div>

    <!-- Assign Modal -->
    <div v-if="showModal" class="fixed inset-0 z-50 bg-black/40 flex items-center justify-center">
      <div class="bg-white rounded-xl shadow-lg p-5 w-full max-w-md">
        <h3 class="text-lg font-semibold mb-3">Assign {{ typeLabel.slice(0, -1) }}</h3>
<select v-model="selected" class="form-input w-full mb-4">
  <option value="" disabled>Select {{ typeLabel.slice(0, -1) }}</option>
  <option v-for="c in allOptions" :key="c.id" :value="c.id">
    {{ c.title_en || c.title_ar || '(No title)' }} — {{ c.email }}
  </option>
</select>



        <div class="flex justify-end gap-2">
          <button class="px-3 py-1 border rounded" @click="closeModal">Cancel</button>
          <button
            class="px-3 py-1 bg-green-600 text-white rounded disabled:opacity-60"
            :disabled="!selected"
            @click="assign"
          >
            Assign
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { UnitContractorsApi } from '@/Services/unitContractors'
import { UnitConsultantsApi } from '@/Services/unitConsultants'

const props = defineProps({
  items: { type: Array, default: () => [] },
  allOptions: { type: Array, default: () => [] },
  unitId: { type: [Number, String], required: true },
  type: { type: String, required: true } // "contractor" | "consultant"
})

const emit = defineEmits(['refresh'])

const showModal = ref(false)
const selected = ref(null)

const typeLabel = props.type === 'contractor' ? 'Contractors' : 'Consultants'

function openAssign() {
  selected.value = null
  showModal.value = true
}

function closeModal() {
  showModal.value = false
}

async function assign() {
  const payload = {
    unit_id: props.unitId,
    data: [{ [`${props.type}_id`]: selected.value }]
  }
  if (props.type === 'contractor') {
    await UnitContractorsApi.create(payload)
  } else {
    await UnitConsultantsApi.create(payload)
  }
  emit('refresh')
  closeModal()
}

async function remove(id) {
  if (!confirm(`Remove this ${props.type}?`)) return
  if (props.type === 'contractor') {
    await UnitContractorsApi.remove(id)
  } else {
    await UnitConsultantsApi.remove(id)
  }
  emit('refresh')
}

const normalizedOptions = computed(() =>
  props.allOptions.map(o => {
    const inner = o.consultant || o.contractor || o
    let title = inner.title

    if (typeof title === 'string') {
      try { title = JSON.parse(title) } catch { title = {} }
    }

    return {
      id: inner.id,
      name: title?.en || title?.ar || '(No title)',
      email: inner.email || ''
    }
  })
)


</script>
