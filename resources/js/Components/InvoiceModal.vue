<template>
  <Teleport to="body">
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
      <div
        class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl p-6 relative animate-fadeIn"
      >
        <!-- Close -->
        <button
          @click="$emit('close')"
          class="absolute top-3 right-4 text-gray-400 hover:text-gray-700 text-xl"
        >
          ✕
        </button>

        <!-- Title -->
        <h3 class="text-2xl font-semibold text-gray-800 mb-6 border-b pb-2">
           Invoices
        </h3>

        <!-- Loader -->
        <div v-if="loading" class="text-sm text-gray-500">Loading...</div>

        <!-- Invoices -->
        <div v-else>
          <div
            v-for="inv in invoices"
            :key="inv.id"
            class="p-4 mb-3 border rounded-xl hover:shadow transition bg-gray-50"
          >
            <div class="flex justify-between items-start">
              <div>
                <p class="font-semibold text-gray-800">
                  Invoice #{{ inv.id }} — <span class="text-blue-600">{{ inv.paid_amount }} EGP</span>
                </p>
                <!-- <p class="text-xs text-gray-500">
                  Installment ID: {{ inv.unit_payment_installment_id }}
                </p> -->

                <div class="mt-2">
                  <span
                    :class="[
                      'inline-block px-3 py-1 text-xs rounded-full font-medium border',
                      inv.status === 'confirmed'
                        ? 'bg-green-100 text-green-700 border-green-200'
                        : inv.status === 'rejected'
                        ? 'bg-red-100 text-red-700 border-red-200'
                        : 'bg-yellow-100 text-yellow-700 border-yellow-200'
                    ]"
                  >
                    {{ inv.status ? inv.status.toUpperCase() : 'PENDING' }}
                  </span>
                </div>

                <div class="mt-3">
                  <img
                    :src="toUrl(inv.invoice_file)"
                    alt="Invoice File"
                    class="rounded-lg shadow-sm border object-contain max-h-40 cursor-pointer hover:scale-105 transition"
                    @click="openImage(inv.invoice_file)"
                  />
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="flex flex-col gap-2">
                <button
                  @click="updateStatus(inv, 'confirm')"
                  class="px-4 py-1.5 rounded-lg text-sm font-medium text-white transition"
                  :class="inv.status === 'confirmed'
                    ? 'bg-green-300 cursor-not-allowed'
                    : 'bg-green-600 hover:bg-green-700'"
                  :disabled="inv.status === 'confirmed'"
                >
                  {{ inv.status === 'confirmed' ? 'Approved' : 'Approve' }}
                </button>

                <button
                  @click="updateStatus(inv, 'reject')"
                  class="px-4 py-1.5 rounded-lg text-sm font-medium text-white bg-red-600 hover:bg-red-700 transition"
                  :disabled="inv.status === 'rejected'"
                >
                  {{ inv.status === 'rejected' ? 'Rejected' : 'Reject' }}
                </button>
              </div>
            </div>
          </div>

          <!-- Empty -->
          <div
            v-if="!invoices.length"
            class="text-center text-gray-500 text-sm mt-6"
          >
            No invoices found.
          </div>
        </div>

        <!-- Feedback Message -->
        <transition name="fade">
          <div
            v-if="feedbackMessage"
            :class="[
              'mt-5 p-3 rounded-lg text-sm font-medium border text-center',
              feedbackType === 'error'
                ? 'bg-red-50 text-red-700 border-red-200'
                : 'bg-green-50 text-green-700 border-green-200'
            ]"
          >
            {{ feedbackMessage }}
          </div>
        </transition>

        <!-- Image Modal -->
        <div
          v-if="showImageModal"
          class="fixed inset-0 bg-black/60 flex items-center justify-center z-[100]"
          @click.self="closeImage"
        >
          <div class="bg-white rounded-xl shadow-2xl p-4 w-full max-w-lg">
            <div class="flex justify-between items-center mb-2">
              <h4 class="font-semibold text-gray-700 text-lg">Invoice Preview</h4>
              <button
                @click="closeImage"
                class="text-gray-500 hover:text-gray-700 text-xl"
              >
                ✕
              </button>
            </div>
            <img
              :src="toUrl(activeImage)"
              alt="Invoice"
              class="rounded-lg object-contain max-h-[75vh] w-full"
            />
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
const feedbackMessage = ref('')
const feedbackType = ref('') // 'success' | 'error'
const showImageModal = ref(false)
const activeImage = ref(null)

onMounted(async () => {
  loading.value = true
  invoices.value = await UnitInvoicesApi.list(props.installment.id)
  loading.value = false
})

async function updateStatus(inv, status) {
  feedbackMessage.value = ''
  feedbackType.value = ''

  if (!confirm(`Mark invoice #${inv.id} as ${status}?`)) return

  try {
    const response = await UnitInvoicesApi.confirmInvoice(inv.id, status)

    if (response?.status === 'error') {
      feedbackType.value = 'error'
      feedbackMessage.value = response.message || 'Action failed.'
    } else if (response?.status === 'success') {
      feedbackType.value = 'success'
      feedbackMessage.value = response.message || 'Action successful.'
    }

    // Refresh invoice list
    invoices.value = await UnitInvoicesApi.list(props.installment.id)

    // Auto-hide message after 4s
    setTimeout(() => (feedbackMessage.value = ''), 4000)
  } catch (err) {
    feedbackType.value = 'error'
    feedbackMessage.value =
      err.response?.data?.message || 'Something went wrong.'
    setTimeout(() => (feedbackMessage.value = ''), 4000)
  }
}

function toUrl(path) {
  return path ? `/storage/${path}` : ''
}

/* Image Modal Controls */
function openImage(img) {
  activeImage.value = img
  showImageModal.value = true
}
function closeImage() {
  activeImage.value = null
  showImageModal.value = false
}
</script>

<style scoped>
.animate-fadeIn {
  animation: fadeIn 0.25s ease-in-out;
}
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: scale(0.98);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.4s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
