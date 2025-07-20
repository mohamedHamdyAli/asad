<template>
  <div>
    <!-- Add Member Button -->
    <div class="flex justify-end mb-4">
      <button
        @click="showAddModal = true"
        class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"
      >
        + Add Member
      </button>
    </div>

    <!-- Members Table -->
    <div class="bg-white rounded-lg shadow overflow-x-auto">
      <table class="w-full text-sm text-left text-gray-700">
        <thead class="bg-gray-100">
          <tr>
            <th class="px-4 py-2">Name</th>
            <th class="px-4 py-2">Email</th>
            <th class="px-4 py-2">Role</th>
            <th class="px-4 py-2">Status</th>
            <th class="px-4 py-2 text-right">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="member in members"
            :key="member.id"
            class="border-b hover:bg-gray-50"
          >
            <td class="px-4 py-2 font-medium">{{ member.name }}</td>
            <td class="px-4 py-2">{{ member.email }}</td>
            <td class="px-4 py-2 capitalize">{{ member.role }}</td>
            <td class="px-4 py-2">
              <span
                :class="member.active
                  ? 'text-green-600 font-semibold'
                  : 'text-red-500 font-semibold'">
                {{ member.active ? 'Active' : 'Inactive' }}
              </span>
            </td>
            <td class="px-4 py-2 text-right space-x-2">
              <button
                @click="toggleStatus(member)"
                :class="member.active ? 'text-yellow-600' : 'text-green-600'"
                title="Toggle Status"
              >
                <Icon
                  :icon="member.active ? 'mdi:block-helper' : 'mdi:check-circle-outline'"
                  class="w-5 h-5"
                />
              </button>
              <button
                @click="edit(member)"
                class="text-blue-600"
                title="Edit"
              >
                <Icon icon="mdi:pencil-outline" class="w-5 h-5" />
              </button>
              <button
                @click="remove(member.id)"
                class="text-red-600"
                title="Remove"
              >
                <Icon icon="mdi:trash-can-outline" class="w-5 h-5" />
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Add Member Modal -->
    <div
      v-if="showAddModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50"
    >
      <div class="bg-white rounded-lg shadow-lg w-full max-w-xl p-6 relative">
        <button
          @click="showAddModal = false"
          class="absolute top-4 right-4 text-gray-400 hover:text-gray-600"
        >
          <Icon icon="mdi:close" class="w-6 h-6" />
        </button>

        <h3 class="text-lg font-semibold mb-4">Add New Member</h3>

        <input v-model="newMember.name" placeholder="Name" class="form-input mb-3" />
        <input v-model="newMember.email" placeholder="Email" class="form-input mb-3" />
        <input v-model="newMember.phone" placeholder="Phone" class="form-input mb-3" />
        <select v-model="newMember.role" class="form-input mb-3">
          <option disabled value="">Select Role</option>
          <option value="member">Member</option>
          <option value="manager">Manager</option>
        </select>

        <!-- Permissions -->
        <div class="mb-4 space-y-2">
          <div class="text-sm font-semibold text-gray-600 mb-2">Permissions</div>
          <label class="flex items-center space-x-2 text-sm">
            <input type="checkbox" v-model="newMember.permissions.manageRequests" />
            <span>Can manage requests</span>
          </label>
          <label class="flex items-center space-x-2 text-sm">
            <input type="checkbox" v-model="newMember.permissions.editOfferings" />
            <span>Can edit offerings</span>
          </label>
          <label class="flex items-center space-x-2 text-sm">
            <input type="checkbox" v-model="newMember.permissions.viewAnalytics" />
            <span>Can view analytics</span>
          </label>
        </div>

        <div class="text-right">
          <button
            @click="addMember"
            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
          >
            Add
          </button>
        </div>
      </div>
    </div>

    <!-- Edit Member Modal -->
    <div
      v-if="showEditModal"
      class="fixed inset-0 bg-black bg-opacity-40 z-50 flex items-center justify-center"
    >
      <div class="bg-white rounded-lg shadow p-6 w-full max-w-md relative">
        <button
          @click="showEditModal = false"
          class="absolute top-4 right-4 text-gray-400 hover:text-gray-600"
        >
          <Icon icon="mdi:close" class="w-6 h-6" />
        </button>

        <h3 class="text-lg font-semibold mb-4">Edit Member</h3>

        <input v-model="editingMember.name" placeholder="Name" class="form-input mb-3" />
        <input v-model="editingMember.email" placeholder="Email" class="form-input mb-3" />
        <select v-model="editingMember.role" class="form-input mb-3">
          <option value="member">Member</option>
          <option value="manager">Manager</option>
        </select>

        <!-- Permissions -->
        <div class="mb-4 space-y-2">
          <div class="text-sm font-semibold text-gray-600 mb-2">Permissions</div>
          <label class="flex items-center space-x-2 text-sm">
            <input type="checkbox" v-model="editingMember.permissions.manageRequests" />
            <span>Can manage requests</span>
          </label>
          <label class="flex items-center space-x-2 text-sm">
            <input type="checkbox" v-model="editingMember.permissions.editOfferings" />
            <span>Can edit offerings</span>
          </label>
          <label class="flex items-center space-x-2 text-sm">
            <input type="checkbox" v-model="editingMember.permissions.viewAnalytics" />
            <span>Can view analytics</span>
          </label>
        </div>

        <button
          @click="saveMember"
          class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
        >
          Save Changes
        </button>
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
  members: Array
})

const entity = ref(props.entity)
const members = ref(props.members)

const showAddModal = ref(false)
const showEditModal = ref(false)

const newMember = ref({
  name: '',
  email: '',
  phone: '',
  role: '',
  permissions: {
    manageRequests: false,
    editOfferings: false,
    viewAnalytics: false
  }
})

const editingMember = ref(null)

const addMember = () => {
  router.post(route('entity.team.store'), {
    entity_id: props.entity.id,
    ...newMember.value
  }, {
    onSuccess: () => {
      showAddModal.value = false
      newMember.value = {
        name: '',
        email: '',
        phone: '',
        role: '',
        permissions: {
          manageRequests: false,
          editOfferings: false,
          viewAnalytics: false
        }
      }
    }
  })
}

const edit = (member) => {
  editingMember.value = JSON.parse(JSON.stringify(member))
  showEditModal.value = true
}

const saveMember = () => {
  router.put(route('entity.team.update', editingMember.value.id), editingMember.value, {
    onSuccess: () => {
      showEditModal.value = false
    }
  })
}

const toggleStatus = (member) => {
  router.put(route('entity.team.update', member.id), {
    active: !member.active
  })
}

const remove = (id) => {
  if (confirm('Are you sure you want to delete this member?')) {
    router.delete(route('entity.team.destroy', id))
  }
}
</script>

<style scoped>
.form-input {
  @apply w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500;
}
</style>
