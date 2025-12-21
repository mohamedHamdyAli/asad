<template>
  <Head title="Roles & Permissions" />

  <AuthenticatedLayout>
    <div class="p-6 space-y-10">

      <!-- ================= ROLES ================= -->
      <section class="bg-white p-6 rounded shadow space-y-4">
        <h2 class="text-xl font-semibold">Roles</h2>

        <!-- Create Role -->
        <div class="flex gap-2">
          <input
            v-model="newRole"
            placeholder="Role name"
            class="form-input w-64"
          />
          <button class="btn-primary" @click="createRole">
            Add Role
          </button>
        </div>

        <!-- Roles List -->
        <table class="w-full text-sm border">
          <thead class="bg-gray-100">
            <tr>
              <th class="p-2 text-left">Role</th>
              <th class="p-2">Permissions</th>
              <th class="p-2">Actions</th>
            </tr>
          </thead>

          <tbody>
            <tr
              v-for="role in roles"
              :key="role.id"
              class="border-t"
            >
              <td class="p-2 font-medium">
                {{ role.name }}
              </td>

              <td class="p-2">
                <div class="grid grid-cols-3 gap-2">
                  <label
                    v-for="p in permissions"
                    :key="p.id"
                    class="flex items-center gap-1 text-xs"
                  >
                    <input
                      type="checkbox"
                      :checked="role.permissions?.some(rp => rp.name === p.name)"
                      @change="toggleRolePermission(role, p.name)"
                    />
                    {{ p.name }}
                  </label>
                </div>
              </td>

              <td class="p-2">
                <button
                  class="btn-danger"
                  @click="deleteRole(role.id)"
                >
                  Delete
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </section>

      <!-- ================= USERS ================= -->
      <section class="bg-white p-6 rounded shadow space-y-4">
        <h2 class="text-xl font-semibold">Users</h2>

        <table class="w-full text-sm">
          <thead class="bg-gray-100 text-left">
            <tr>
              <th class="px-4 py-3">Name</th>
              <th class="px-4 py-3">Email</th>
              <th class="px-4 py-3">Role</th>
              <th class="px-4 py-3 text-right">Action</th>
            </tr>
          </thead>

          <tbody>
            <tr
              v-for="user in users"
              :key="user.id"
              class="border-t"
            >
              <td class="px-4 py-3">{{ user.name }}</td>
              <td class="px-4 py-3">{{ user.email }}</td>

              <!-- ROLE SELECT -->
              <td class="px-4 py-3">
                <select
                  v-model="selectedRoles[user.id]"
                  class="form-input"
                >
                  <option value="">— Select role —</option>
                  <option
                    v-for="role in roles"
                    :key="role.id"
                    :value="role.name"
                  >
                    {{ role.name }}
                  </option>
                </select>
              </td>

              <!-- SAVE -->
              <td class="px-4 py-3 text-right">
                <button
                  class="px-4 py-2 bg-blue-600 text-white rounded disabled:opacity-50"
                  :disabled="saving[user.id]"
                  @click="saveRole(user.id)"
                >
                  {{ saving[user.id] ? 'Saving…' : 'Save' }}
                </button>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- GLOBAL FEEDBACK -->
        <p v-if="globalMsg" class="text-green-600 text-sm">
          {{ globalMsg }}
        </p>
        <p v-if="globalError" class="text-red-600 text-sm">
          {{ globalError }}
        </p>
      </section>

    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Head } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

import { RolesApi } from '@/Services/roles'
import { PermissionsApi } from '@/Services/permissions'
import { UsersApi } from '@/Services/users'

/* ================= STATE ================= */
const roles = ref([])
const permissions = ref([])
const users = ref([])
const newRole = ref('')

const selectedRoles = ref({})
const saving = ref({})
const globalMsg = ref('')
const globalError = ref('')

/* ================= FETCH ================= */
async function fetchAll() {
  roles.value = await RolesApi.list()
  permissions.value = await PermissionsApi.list()
  users.value = await UsersApi.list()

  users.value.forEach(user => {
    if (!(user.id in selectedRoles.value)) {
      selectedRoles.value[user.id] = user.roles?.[0]?.name || ''
    }

    if (!(user.id in saving.value)) {
      saving.value[user.id] = false
    }
  })
}

/* ================= ROLES ================= */
async function createRole() {
  if (!newRole.value) return
  await RolesApi.create({ name: newRole.value })
  newRole.value = ''
  await fetchAll()
}

async function deleteRole(id) {
  if (!confirm('Delete role?')) return
  await RolesApi.remove(id)
  await fetchAll()
}

async function toggleRolePermission(role, permission) {
  await RolesApi.update(role.id, { permission })
  await fetchAll()
}

/* ================= USERS ================= */
async function saveRole(userId) {
  globalMsg.value = ''
  globalError.value = ''
  saving.value[userId] = true

  try {
    await UsersApi.assignRole(
      userId,
      selectedRoles.value[userId]
    )
    globalMsg.value = 'Role assigned successfully'
  } catch (e) {
    globalError.value = 'Failed to assign role'
  } finally {
    saving.value[userId] = false
  }
}

onMounted(fetchAll)
</script>

<style scoped>
.form-input {
  @apply border rounded px-3 py-1 text-sm;
}
.btn-primary {
  @apply bg-blue-600 text-white px-3 py-1 rounded;
}
.btn-danger {
  @apply bg-red-600 text-white px-3 py-1 rounded;
}
</style>
