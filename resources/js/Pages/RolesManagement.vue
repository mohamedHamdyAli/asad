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

        <!-- ROLES ACCORDION -->
        <div v-for="role in roles" :key="role.id" class="border rounded-lg overflow-hidden">
          <!-- ROLE HEADER -->
          <div  v-if="role.name !== 'guest' && role.name !== 'user'" class="flex items-center justify-between bg-gray-100 px-4 py-3 cursor-pointer"
            @click="toggleRole(role.id)">
            <span  class="font-semibold">{{ role.name }}</span>

            <div class="flex items-center gap-3">
            <button
  v-if="role.name !== 'admin'"
  class="btn-danger"
  @click="deleteRole(role.id)"
>
  Delete
</button>


              <span class="text-sm">
                {{ openRoles[role.id] ? '▲' : '▼' }}
              </span>
            </div>
          </div>

          <!-- ROLE BODY -->
          <transition name="slide">
            <div v-if="openRoles[role.id]" class="p-4">
              <table class="w-full text-sm border">
                <thead class="bg-yellow-600 text-white">
                  <tr>
                    <th class="p-3 text-left">Module</th>
                    <th class="p-3 text-center">View</th>
                    <th class="p-3 text-center">Create</th>
                    <th class="p-3 text-center">Edit</th>
                    <th class="p-3 text-center">Delete</th>
                    <th class="p-3 text-center"></th>
                  </tr>
                </thead>

                <tbody>
                  <template v-for="(group, module) in groupedPermissions" :key="module">
                    <!-- MODULE ROW -->
                    <tr class="border-t">
                      <td class="p-3 font-medium capitalize">
                        {{ module }}
                      </td>

                      <td v-for="action in ['view', 'create', 'update', 'delete']" :key="action" class="text-center">
                        <input v-if="group.crud[action]" type="checkbox"
                          :checked="role.permissions?.some(p => p.name === group.crud[action])"
                          @change="onPermissionToggle(role.id, group.crud[action])" />
                      </td>

                      <td class="text-center">
                        <button v-if="group.extra.length" class="px-3 py-1 text-xs bg-yellow-600 text-white rounded"
                          @click.stop="toggleModule(role.id, module)">
                          {{ isModuleOpen(role.id, module) ? 'Less' : 'More' }}
                        </button>
                      </td>
                    </tr>

                    <!-- EXTRA PERMISSIONS -->
                    <tr v-if="group.extra.length && isModuleOpen(role.id, module)">
                      <td colspan="6" class="bg-gray-50 p-4">
                        <div class="grid grid-cols-4 gap-4 text-xs">
                          <label v-for="perm in group.extra" :key="perm" class="flex items-center gap-2">
                            <input type="checkbox" :checked="role.permissions?.some(p => p.name === perm)"
                              @change="onPermissionToggle(role.id, perm)" />
                            {{ perm.split('.').slice(1).join(' ') }}
                          </label>
                        </div>
                      </td>
                    </tr>
                  </template>
                </tbody>
              </table>
            </div>
          </transition>
        </div>
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
              <th class="px-4 py-3">Current Role</th>
              <th class="px-4 py-3">Change Role</th>
              <th class="px-4 py-3 text-right">Action</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="user in paginatedUsers" :key="user.id" class="border-t">
              <td class="px-4 py-3">{{ user.name }}</td>
              <td class="px-4 py-3">{{ user.email }}</td>

              <!-- CURRENT ROLE -->
              <td class="px-4 py-3 font-medium">
                {{ user.roles?.[0]?.name ?? '—' }}
              </td>

              <!-- CHANGE ROLE -->
              <td class="px-4 py-3">
                <select class="form-input" :value="selectedRoles[user.id]"
                  @change="onRoleChange(user.id, $event.target.value)">
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

const openRoles = ref({})
const openModules = ref({})

/* ================= FETCH ================= */
async function fetchAll() {
  const rolesRes = await RolesApi.list()
  const permsRes = await PermissionsApi.list()
  const usersRes = await UsersApi.list()

  roles.value = rolesRes.data ?? rolesRes
  permissions.value = permsRes.data ?? permsRes
  users.value = usersRes.data ?? usersRes

  users.value.forEach(user => {
    selectedRoles.value[user.id] = user.roles?.[0]?.name ?? ''
    saving.value[user.id] = false
  })
}

//  grouped permissions

const groupedPermissions = computed(() => {
  const map = {}

  permissions.value.forEach(p => {
    const [module, action] = p.name.split('.')

    if (!map[module]) {
      map[module] = {
        crud: {},
        extra: [],
      }
    }

    if (['view', 'create', 'update', 'delete'].includes(action)) {
      map[module].crud[action] = p.name
    } else {
      map[module].extra.push(p.name)
    }
  })

  return map
})


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
function onPermissionToggle(roleId, permissionName, event) {

  toggleRolePermission(roleId, permissionName)
}

async function toggleRolePermission(roleId, permissionName) {
  const role = roles.value.find(r => r.id === roleId)
  if (!role) return

  const current = role.permissions?.map(p => p.name) ?? []

  const updatedPermissions = current.includes(permissionName)
    ? current.filter(p => p !== permissionName)
    : [...current, permissionName]

  await RolesApi.syncPermissions(roleId, {
    permissions: updatedPermissions,
  })

  role.permissions = updatedPermissions.map(p => ({ name: p }))
}



/* ================= USERS ================= */
function onRoleChange(userId, value) {
  selectedRoles.value[userId] = value
}

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

// helper 


function toggleRole(roleId) {
  openRoles.value[roleId] = !openRoles.value[roleId]
}

function toggleModule(roleId, module) {
  if (!openModules.value[roleId]) {
    openModules.value[roleId] = {}
  }
  openModules.value[roleId][module] =
    !openModules.value[roleId][module]
}

function isModuleOpen(roleId, module) {
  return !!openModules.value[roleId]?.[module]
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

.slide-enter-active,
.slide-leave-active {
  transition: all 0.25s ease;
}

.slide-enter-from,
.slide-leave-to {
  opacity: 0;
  transform: translateY(-6px);
}

.slide-enter-to,
.slide-leave-from {
  opacity: 1;
  transform: translateY(0);
}
</style>
