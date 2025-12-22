import axios from 'axios'

export const RolesApi = {
  // GET all roles with permissions
  list: async () => {
    const { data } = await axios.get('/api/roles')
    return data.data ?? data
  },

  // CREATE role
  create: async (payload) => {
    const { data } = await axios.post('/api/roles/create', payload)
    return data
  },

  // UPDATE role name
  update: async (id, payload) => {
    const { data } = await axios.post(`/api/roles/update/${id}`, payload)
    return data
  },

  // DELETE role
  remove: async (id) => {
    const { data } = await axios.delete(`/api/roles/delete/${id}`)
    return data
  },

  // ✅ THIS IS THE CRITICAL ONE
  syncPermissions: async (roleId, payload) => {
    // payload: { permissions: ['edit-users', 'view-users'] }
    const { data } = await axios.post(
      `/api/roles/${roleId}/sync-permissions`,
      payload
    )
    return data
  },
}
