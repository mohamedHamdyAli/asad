import axios from 'axios'

export const UnitIssuesApi = {
  list: async () => {
    const { data } = await axios.get('/api/unit-issues')
    return data.data ?? []
  },

  show: async (id) => {
    const { data } = await axios.get(`/api/unit-issues/show/${id}`)
    return data.data
  },

  create: async (payload) => {
    const { data } = await axios.post('/api/unit-issues/create', {
      data: payload,
    })
    return data
  },

  update: async (id, payload) => {
    const { data } = await axios.post(`/api/unit-issues/update/${id}`, {
      data: payload,
    })
    return data
  },

  remove: async (id) => {
    const { data } = await axios.delete(`/api/unit-issues/delete/${id}`)
    return data
  },
}
