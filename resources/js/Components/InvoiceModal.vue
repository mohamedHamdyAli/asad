<template>
  <Teleport to="body">
    <div
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
    >
      <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl p-6 relative animate-fadeIn">
        <button
          @click="$emit('close')"
          class="absolute top-3 right-4 text-gray-500 hover:text-black"
        >
          ✕
        </button>

        <h3 class="text-xl font-semibold mb-4">Invoices</h3>

        <div v-if="loading" class="text-sm text-gray-500">Loading...</div>
        <div v-else>
          <div
            v-for="inv in invoices"
            :key="inv.id"
            class="flex items-center justify-between p-3 border rounded-lg mb-2"
          >
            <div>
              <p class="text-sm font-medium text-gray-800">
                #{{ inv.id }} — {{ inv.amount }} EGP
              </p>
              <p class="text-xs text-gray-500">Status: {{ inv.status }}</p>
              <img :src="toUrl(inv.invoice_file)" class="max-h-40 object-contain mt-2" />
            </div>
            <div class="flex gap-2">
              <button
                @click="updateStatus(inv, 'confirm')"
                class="px-3 py-1 bg-green-600 text-white rounded text-xs hover:bg-green-700"
              >
                Approve
              </button>
              <button
                @click="updateStatus(inv, 'reject')"
                class="px-3 py-1 bg-red-600 text-white rounded text-xs hover:bg-red-700"
              >
                Reject
              </button>
            </div>
          </div>

          <div v-if="!invoices.length" class="text-center text-gray-500 text-sm">
            No invoices found.
          </div>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { UnitInvoicesApi } from '@/Services/unitInvoices'

const props = defineProps({
  installment: Object,
})
const emit = defineEmits(['close'])

const invoices = ref([])
const loading = ref(false)

onMounted(async () => {
  loading.value = true
  invoices.value = await UnitInvoicesApi.list(props.installment.id)
  loading.value = false
})

async function updateStatus(inv, status) {
  if (!confirm(`Mark invoice #${inv.id} as ${status}?`)) return
  await UnitInvoicesApi.confirmInvoice(inv.id, status)
  invoices.value = await UnitInvoicesApi.list(props.installment.id)
}

function toUrl(path) {
  return path ? `/storage/${path}` : "";
}

</script>
