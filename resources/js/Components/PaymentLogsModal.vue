<template>
  <Teleport to="body">
    <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
      <div class="bg-white w-full max-w-2xl rounded-lg p-6 relative shadow-xl animate-fadeIn">
        <button @click="$emit('close')" class="absolute right-4 top-3 text-gray-500 hover:text-black">✕</button>

        <h3 class="text-lg font-semibold mb-4 text-gray-800">Payment Logs</h3>

        <div v-if="!logs.length" class="text-center text-gray-500 py-8">No logs found.</div>

        <ul v-else class="divide-y divide-gray-200 max-h-96 overflow-y-auto">
          <li v-for="log in logs" :key="log.id" class="py-3">
            <div class="flex justify-between items-center">
              <div>
                <p class="font-medium text-gray-800">{{ log.action || 'Action' }}</p>
                <p class="text-sm text-gray-600 mt-1">{{ log.description || 'Description' }}</p>
                <p class="text-sm text-gray-500">By: {{ log.created_by_name || 'System Admin' }}</p>
              </div>
              <span class="text-xs text-gray-400">{{ new Date(log.created_at).toLocaleString() }}</span>
            </div>
            <p v-if="log.notes" class="text-sm text-gray-600 mt-1">{{ log.notes }}</p>
          </li>
        </ul>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
const props = defineProps({
  logs: { type: Array, default: () => [] },
})
</script>
