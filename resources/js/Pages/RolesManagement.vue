<template>

  <Head title="Roles & Permissions" />

  <AuthenticatedLayout>
    <div class="p-6 space-y-10">

      <!-- ================= ROLES ================= -->
      <section class="bg-white p-6 rounded shadow space-y-4">
        <h2 class="text-xl font-semibold">Roles</h2>

        <!-- Create Role -->
        <div class="flex gap-2">
          <input v-model="newRole" placeholder="Role name" class="form-input w-64" />
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
            <tr v-for="role in roles" :key="role.id" class="border-t">
              <td class="p-2 font-medium">{{ role.name }}</td>

              <td class="p-2">
                <div class="grid grid-cols-3 gap-2">
                  <label v-for="p in permissions" :key="p.id" class="flex items-center gap-1 text-xs">
                    <input type="checkbox" :checked="role.permissions?.some(rp => rp.name === p.name)"
                      @change="toggleRolePermission(role, p.name)" />
                    {{ p.name }}
                  </label>
                </div>
              </td>

              <td class="p-2">
                <button class="btn-danger" @click="deleteRole(role.id)">
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

        <!-- SEARCH & FILTER -->
        <div class="flex flex-wrap gap-4 items-center">
          <input v-model="search" placeholder="Search name or email" class="form-input w-64" />

          <select v-model="roleFilter" class="form-input w-48">
            <option value="">All roles</option>
            <option v-for="r in roles" :key="r.id" :value="r.name">
              {{ r.name }}
            </option>
          </select>
        </div>

        <!-- USERS TABLE -->
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
            <tr v-for="user in paginatedUsers" :key="user.id" class="border-t">
              <td class="px-4 py-3">{{ user.name }}</td>
              <td class="px-4 py-3">{{ user.email }}</td>

              <!-- ROLE SELECT -->
              <td class="px-4 py-3">
                <select :key="`role-select-${user.id}-${roles.length}`" v-model="selectedRoles[user.id]"
                  class="form-input">

                  <option value="">— Select role —</option>
                  <option v-for="role in roles" :key="role.id" :value="role.name">
                    {{ role.name }}
                  </option>
                </select>
              </td>

              <!-- SAVE -->
              <td class="px-4 py-3 text-right">
                <button class="px-4 py-2 bg-blue-600 text-white rounded disabled:opacity-50" :disabled="saving[user.id]"
                  @click="saveRole(user.id)">
                  {{ saving[user.id] ? 'Saving…' : 'Save' }}
                </button>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- PAGINATION -->
        <div class="flex justify-between items-center mt-4">
          <span class="text-sm text-gray-600">
            Page {{ currentPage }} of {{ totalPages }}
          </span>

          <div class="flex gap-2">
            <button class="px-3 py-1 border rounded" :disabled="currentPage === 1" @click="currentPage--">
              Prev
            </button>

            <button class="px-3 py-1 border rounded" :disabled="currentPage === totalPages" @click="currentPage++">
              Next
            </button>
          </div>
        </div>

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
import { ref, onMounted, computed, watch } from 'vue'
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

const search = ref('')
const roleFilter = ref('')
const currentPage = ref(1)
const perPage = ref(10)

/* ================= FETCH ================= */
async function fetchAll() {
  const rolesRes = await RolesApi.list()
  const permsRes = await PermissionsApi.list()
  const usersRes = await UsersApi.list()

  roles.value = rolesRes.data ?? rolesRes
  permissions.value = permsRes.data ?? permsRes
  users.value = usersRes.data ?? usersRes

  // ✅ IMPORTANT: wait until roles exist
  users.value.forEach(user => {
    const roleName = user.roles?.[0]?.name || ''

    // only set if role exists in roles table
    if (roles.value.some(r => r.name === roleName)) {
      selectedRoles.value[user.id] = roleName
    } else {
      selectedRoles.value[user.id] = ''
    }

    saving.value[user.id] = false
  })
}



/* ================= FILTERING ================= */
const filteredUsers = computed(() => {
  let list = users.value

  if (search.value) {
    const q = search.value.toLowerCase()
    list = list.filter(u =>
      u.name.toLowerCase().includes(q) ||
      u.email.toLowerCase().includes(q)
    )
  }

  if (roleFilter.value) {
    list = list.filter(u =>
      u.roles?.some(r => r.name === roleFilter.value)
    )
  }

  return list
})

const totalPages = computed(() =>
  Math.ceil(filteredUsers.value.length / perPage.value)
)

const paginatedUsers = computed(() => {
  const start = (currentPage.value - 1) * perPage.value
  return filteredUsers.value.slice(start, start + perPage.value)
})

watch([search, roleFilter], () => {
  currentPage.value = 1
})

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
  await RolesApi.update(role.id, { name: permission })
  await fetchAll()
}

/* ================= USERS ================= */
async function saveRole(userId) {
  globalMsg.value = ''
  globalError.value = ''
  saving.value[userId] = true

  try {
    await UsersApi.assignRole(userId, {
      roles: [selectedRoles.value[userId]],
    })

    const user = users.value.find(u => u.id === userId)
    if (user) {
      user.roles = [{ name: selectedRoles.value[userId] }]
    }

    globalMsg.value = 'Role assigned successfully'
  } catch {
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
