import axios from 'axios'

function toBool(v) { return v === true || v === 1 || v === '1' }

function normalizeUser(row = {}) {
  const img = row.profile_image || row.profileImage || null
  return {
    id: row.id,
    name: row.name || '',
    email: row.email || '',
    phone: row.phone || '',
    gender: row.gender || '',
    country_code: row.country_code || '',
    country_name: row.country_name || '',
    role: row.role || 'user',
     roles: row.roles || [],
    is_enabled: toBool(row.is_enabled),
    profile_image: img,
    profile_image_url: img ? `/storage/${img}` : null,
    created_at: row.created_at,
    updated_at: row.updated_at,
  }
}

function unwrapListPayload(data) {
  // API may return {status,message,data} or bare array
  if (Array.isArray(data)) return data
  if (Array.isArray(data?.data)) return data.data
  if (Array.isArray(data?.message)) return data.message
  return []
}

function unwrapRowPayload(data) {
  return data?.data ?? data
}

export const UsersApi = {
  // GET /api/users
  list: async () => {
    const { data } = await axios.get('/api/users')
    return unwrapListPayload(data).map(normalizeUser)
  },

  // GET /api/users/show/{id}
  show: async (id) => {
    const { data } = await axios.get(`/api/users/show/${id}`)
    return normalizeUser(unwrapRowPayload(data))
  },

  // GET /api/users/edit/{id}
  edit: async (id) => {
    const { data } = await axios.get(`/api/users/edit/${id}`)
    return normalizeUser(unwrapRowPayload(data))
  },

  // POST /api/users/create (multipart)
  create: async (formData) => {
    const { data } = await axios.post('/api/users/create', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
    // backend returns {status,message,data:true}; no row comes back
    return data
  },

  // POST /api/users/update/{id} (multipart or JSON)
  update: async (id, formDataOrJson) => {
    const isFD = typeof FormData !== 'undefined' && formDataOrJson instanceof FormData
    const { data } = await axios.post(`/api/users/update/${id}`, formDataOrJson, {
      headers: isFD ? { 'Content-Type': 'multipart/form-data' } : undefined,
    })
    return data
  },

  // DELETE /api/users/delete/{id}
  remove: async (id) => {
    const { data } = await axios.delete(`/api/users/delete/${id}`)
    return data
  },

  assignRole: async (id, payload) => {
    // payload example: { roles: ['admin', 'manager'] }
    const { data } = await axios.post(`/api/users/${id}/assign-role`, payload)
    return data
  },

  assignPermission: async (id, payload) => {
    // payload example: { permissions: ['create-user', 'edit-post'] }
    const { data } = await axios.post(`/api/users/${id}/assign-permission`, payload)
    return data
  },

   // Enable / Disable user
  toggleStatus: async (userId, isEnabled) => {
    const { data } = await axios.post(
      `/api/users/update/${userId}`,
      { is_enabled: isEnabled ? 1 : 0 }
    )
    return data
  },

}

// Optional helpers if you’ll build forms:
export function buildUserCreateFD(payload = {}) {
  const fd = new FormData()
  fd.append('name', payload.name ?? '')
  fd.append('email', payload.email ?? '')
  fd.append('phone', payload.phone ?? '')
  fd.append('password', payload.password ?? '')
  fd.append('country_code', payload.country_code ?? '')
  fd.append('country_name', payload.country_name ?? '')
  fd.append('gender', payload.gender ?? '')
  // Backend forces role=user & is_enabled=1 by FormRequest __construct, but we can include as well:
  fd.append('role', 'user')
  fd.append('is_enabled', '1')
  if (payload.profile_image) fd.append('profile_image', payload.profile_image)
  return fd
}

export function buildUserUpdateFD(payload = {}) {
  const fd = new FormData()
  if (payload.name) fd.append('name', payload.name)
  if (payload.email) fd.append('email', payload.email)
  if (payload.phone) fd.append('phone', payload.phone)
  if (payload.password) fd.append('password', payload.password)
  if (payload.country_code) fd.append('country_code', payload.country_code)
  if (payload.country_name) fd.append('country_name', payload.country_name)
  if (payload.gender) fd.append('gender', payload.gender)
  if (payload.profile_image) fd.append('profile_image', payload.profile_image)
  // role/is_enabled are optional on update
  if (payload.role) fd.append('role', payload.role)
  if (payload.is_enabled !== undefined) fd.append('is_enabled', payload.is_enabled ? '1' : '0')
  return fd
}
