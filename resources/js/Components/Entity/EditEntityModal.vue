<script setup>
import { ref, watch } from "vue";
import { Icon } from "@iconify/vue";
// import { spcsApi } from "@/api/spcs";
import { VendorsApi } from "@/Services/vendors";

const props = defineProps({
  open: { type: Boolean, default: false },
  spc:  { type: Object, default: null }, // expects { id, name, admin_name, admin_phone, admin_email, status }
});
const emit = defineEmits(["close", "updated"]);

const form = ref({
  name: "",
  admin_name: "",
  admin_phone: "",
  admin_email: "",
  status: "Active",
});
const errors = ref({});
const saving = ref(false);

watch(() => props.open, (v) => {
  if (v && props.spc) {
    errors.value = {};
    form.value = {
      name: props.spc.name ?? "",
      admin_name: props.spc.admin_name ?? "",
      admin_phone: props.spc.admin_phone ?? "",
      admin_email: props.spc.admin_email ?? "",
      status: props.spc.status ?? "Active",
    };
  }
});

function err(f){ return errors.value?.[f]?.[0] || ""; }

async function submit() {
  if (!props.spc?.id) return;
  saving.value = true;
  errors.value = {};
  try {
    const payload = {
      name: form.value.name,
      contact_person: form.value.admin_name,
      phone: form.value.admin_phone,
      email: form.value.admin_email,
      status: form.value.status,
    };
    await VendorsApi.update(props.spc.id, payload);
    emit("updated");
  } catch (e) {
    const r = e?.response;
    if (r?.status === 422 && r?.data?.errors) {
      errors.value = r.data.errors;
    } else {
      alert(r?.data?.message || "Update failed");
    }
  } finally {
    saving.value = false;
  }
}
</script>

<template>
  <div v-if="open" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-lg w-full max-w-xl p-4 relative">
      <button @click="$emit('close')" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
        <Icon icon="mdi:close" class="w-6 h-6" />
      </button>
      <h3 class="text-xl font-bold text-gray-800 mb-4">Edit Service Provider</h3>

      <form class="space-y-3" @submit.prevent="submit">
        <div>
          <label class="block text-sm text-gray-600 mb-1">Company Name</label>
          <input v-model="form.name" class="w-full border rounded px-3 py-2" />
          <p v-if="err('name')" class="text-xs text-red-600">{{ err('name') }}</p>
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-sm text-gray-600 mb-1">Admin Name</label>
            <input v-model="form.admin_name" class="w-full border rounded px-3 py-2" />
            <p v-if="err('contact_person')" class="text-xs text-red-600">{{ err('contact_person') }}</p>
          </div>
          <div>
            <label class="block text-sm text-gray-600 mb-1">Admin Phone</label>
            <input v-model="form.admin_phone" class="w-full border rounded px-3 py-2" />
            <p v-if="err('phone')" class="text-xs text-red-600">{{ err('phone') }}</p>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-sm text-gray-600 mb-1">Admin Email</label>
            <input v-model="form.admin_email" class="w-full border rounded px-3 py-2" />
            <p v-if="err('email')" class="text-xs text-red-600">{{ err('email') }}</p>
          </div>
          <div>
            <label class="block text-sm text-gray-600 mb-1">Status</label>
            <select v-model="form.status" class="w-full border rounded px-3 py-2">
              <option>Active</option>
              <option>Inactive</option>
            </select>
            <p v-if="err('status')" class="text-xs text-red-600">{{ err('status') }}</p>
          </div>
        </div>

        <div class="flex justify-end gap-2 pt-2">
          <button type="button" class="px-4 py-2 rounded border" @click="$emit('close')">Cancel</button>
          <button :disabled="saving" class="px-4 py-2 rounded bg-blue-600 text-white disabled:opacity-50">
            {{ saving ? 'Saving…' : 'Save Changes' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
