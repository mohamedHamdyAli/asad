<template>
  <div>
    <!-- Add New Offering -->
    <div class="flex justify-end mb-4">
      <button
        @click="openModalForNew()"
        class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"
      >
        + Add Offering
      </button>
    </div>

    <!-- Offering Cards -->
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="offering in offerings"
        :key="offering.id"
        class="bg-white shadow rounded-lg p-4 flex flex-col justify-between"
      >
        <div>
          <h3 class="text-lg font-semibold text-gray-800">{{ offering.name }}</h3>
          <p class="text-sm text-gray-500 mb-2">{{ offering.type }}</p>
          <p class="text-sm text-gray-600">Price: {{ currency }} {{ offering.price }}</p>
          <p class="text-sm text-gray-600">Duration: {{ offering.duration }} days</p>
          <p class="text-sm mt-2">
            <span
              :class="[
                'px-2 py-1 rounded text-xs font-semibold',
                offering.active ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600'
              ]"
            >
              {{ offering.active ? 'Active' : 'Disabled' }}
            </span>
          </p>

          <!-- Features -->
          <div class="mt-4">
            <p class="text-sm font-medium text-gray-700 mb-1">Included Features:</p>
            <ul class="list-disc list-inside text-sm text-gray-600 space-y-1">
              <li v-for="(item, index) in offering.features" :key="index">
                {{ item }}
              </li>
            </ul>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-end gap-2 mt-4">
          <button @click="editOffering(offering)" class="text-blue-600 hover:text-blue-800 text-sm">
            <Icon icon="mdi:pencil-outline" class="w-5 h-5" />
          </button>
          <button
            @click="toggleStatus(offering)"
            :class="offering.active ? 'text-yellow-600 hover:text-yellow-800' : 'text-green-600 hover:text-green-800'"
            class="text-sm"
          >
            <Icon
              :icon="offering.active ? 'mdi:block-helper' : 'mdi:check-circle-outline'"
              class="w-5 h-5"
            />
          </button>
        </div>
      </div>
    </div>

    <!-- Modal for Add/Edit -->
    <div
      v-if="showModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50"
    >
      <div class="bg-white rounded-lg shadow-lg w-full max-w-xl p-6 relative">
        <button
          @click="closeModal"
          class="absolute top-4 right-4 text-gray-400 hover:text-gray-600"
        >
          <Icon icon="mdi:close" class="w-6 h-6" />
        </button>

        <h3 class="text-lg font-semibold mb-4">
          {{ editingOffering.id ? 'Edit Offering' : 'Add New Offering' }}
        </h3>

        <input v-model="editingOffering.name" placeholder="Offering Name" class="form-input mb-3" />
        <input v-model="editingOffering.description" placeholder="Description" class="form-input mb-3" />
        <select v-model="editingOffering.type" class="form-input mb-3">
          <option disabled value="">Select Type</option>
          <option value="Type A">Type A</option>
          <option value="Type B">Type B</option>
        </select>
        <input v-model="editingOffering.price" placeholder="Price" class="form-input mb-3" />
        <input v-model="editingOffering.duration" placeholder="Duration (days)" class="form-input mb-3" />

        <!-- Feature Items -->
        <div class="space-y-2 mb-4">
          <label class="text-sm font-medium text-gray-700">Features:</label>
          <div
            v-for="(item, index) in editingOffering.features"
            :key="index"
            class="flex gap-2 items-center"
          >
            <input v-model="editingOffering.features[index]" class="form-input flex-1" />
            <button @click="editingOffering.features.splice(index, 1)" class="text-red-500 hover:text-red-700 text-sm">
              <Icon icon="mdi:close-circle" class="w-5 h-5" />
            </button>
          </div>
          <button @click="editingOffering.features.push('')" class="text-blue-600 hover:underline text-sm mt-1">
            + Add Feature
          </button>
        </div>

        <div class="text-right">
          <button @click="saveOffering" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Save Offering
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { Icon } from '@iconify/vue'

const props = defineProps({
  entity: Object,
  offerings: Array
})

const currency = 'EGP' // customizable per use case
const showModal = ref(false)

const offerings = ref([...props.offerings])

const editingOffering = ref({
  id: null,
  name: '',
  description: '',
  type: '',
  price: 0,
  duration: 0,
  active: true,
  features: []
})

const openModalForNew = () => {
  editingOffering.value = {
    id: null,
    name: '',
    description: '',
    type: '',
    price: 0,
    duration: 0,
    active: true,
    features: []
  }
  showModal.value = true
}

const editOffering = (pkg) => {
  editingOffering.value = JSON.parse(JSON.stringify(pkg))
  showModal.value = true
}

const saveOffering = () => {
  const routeName = editingOffering.value.id ? 'entity.offerings.update' : 'entity.offerings.store'
  const method = editingOffering.value.id ? 'put' : 'post'

  const payload = {
    ...(editingOffering.value.id ? {} : { entity_id: props.entity.id }),
    ...editingOffering.value
  }

  router[method](route(routeName, editingOffering.value.id), payload, {
    onSuccess: () => {
      closeModal()
      refresh()
    }
  })
}

const toggleStatus = (offering) => {
  router.put(route('entity.offerings.toggle', offering.id), {}, {
    onSuccess: () => refresh()
  })
}

const closeModal = () => {
  showModal.value = false
}

const refresh = () => {
  router.reload({ only: ['offerings'] })
}
</script>

<style scoped>
.form-input {
  @apply w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500;
}
</style>
