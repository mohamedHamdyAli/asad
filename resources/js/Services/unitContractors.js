// services/unitContractors.js
import axios from 'axios'

export const UnitContractorsApi = {
  list: async (unitId) => {
    const { data } = await axios.get(`/api/unit-contractors/${unitId}`)
    return data?.data || []
  },
  create: async (payload) => {
    return axios.post(`/api/unit-contractors/create`, payload)
  },
  update: async (payload) => {
    return axios.post(`/api/unit-contractors/update`, payload)
  },
  remove: async (id) => {
    return axios.delete(`/api/unit-contractors/delete/${id}`)
  }
}
