<script setup>
import { onMounted, ref, watch } from "vue";
import { Icon } from "@iconify/vue";
// import { spcsApi } from "@/api/spcs";
import { VendorsApi } from "@/Services/vendors";

const props = defineProps({
  id: { type: Number, required: true },
  open: { type: Boolean, default: false },
});
const emit = defineEmits(["close"]);

const loading = ref(true);
const spc = ref(null);

async function load() {
  loading.value = true;
  try {
    const { data } = await VendorsApi.show(props.id);
    spc.value = data;
  } finally {
    loading.value = false;
  }
}

watch(() => props.open, (v) => { if (v) load(); });
onMounted(() => { if (props.open) load(); });
</script>

<template>
  <div v-if="open" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-lg w-full max-w-xl p-4 relative">
      <button @click="$emit('close')" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
        <Icon icon="mdi:close" class="w-6 h-6" />
      </button>
      <h3 class="text-xl font-bold text-gray-800 mb-4">Service Provider Details</h3>

      <div v-if="loading" class="py-8 text-center text-gray-500">Loading…</div>
      <div v-else class="space-y-3">
        <div class="grid grid-cols-3 gap-2">
          <div class="text-gray-500">Company Name</div>
          <div class="col-span-2 font-medium">{{ spc.name }}</div>

          <div class="text-gray-500">Admin Name</div>
          <div class="col-span-2">{{ spc.admin_name || '—' }}</div>

          <div class="text-gray-500">Admin Phone</div>
          <div class="col-span-2">{{ spc.admin_phone || '—' }}</div>

          <div class="text-gray-500">Admin Email</div>
          <div class="col-span-2">{{ spc.admin_email || '—' }}</div>

          <div class="text-gray-500">Status</div>
          <div class="col-span-2">
            <span :class="['px-2 py-1 rounded text-xs font-semibold', spc.status === 'Active' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600']">
              {{ spc.status }}
            </span>
          </div>
        </div>
      </div>

      <div class="flex justify-end mt-6">
        <button class="px-4 py-2 rounded border" @click="$emit('close')">Close</button>
      </div>
    </div>
  </div>
</template>
