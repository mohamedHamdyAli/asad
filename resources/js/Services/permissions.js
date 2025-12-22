import axios from 'axios'

export const PermissionsApi = {
  // GET all permissions
  list: async () => {
    const { data } = await axios.get('/api/permissions')
    return data.data ?? data
  },

  // CREATE permission (uses PermissionRequest → requires name)
  create: async (payload) => {
    // payload: { name: 'edit-users' }
    const { data } = await axios.post('/api/permissions', payload)
    return data
  },

  // DELETE permission
  remove: async (id) => {
    const { data } = await axios.delete(`/api/permissions/${id}`)
    return data
  },
}
