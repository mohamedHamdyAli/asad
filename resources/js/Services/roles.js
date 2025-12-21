import axios from 'axios'

export const RolesApi = {
  list: async () => {
    const { data } = await axios.get('/api/roles')
    return data.data
  },

  show: async (id) => {
    const { data } = await axios.get(`/api/roles/show/${id}`)
    return data.data
  },

  create: async (payload) => {
    return axios.post('/api/roles/create', payload)
  },

  update: async (id, payload) => {
    return axios.post(`/api/roles/update/${id}`, payload)
  },

  remove: async (id) => {
    return axios.delete(`/api/roles/delete/${id}`)
  },
}
