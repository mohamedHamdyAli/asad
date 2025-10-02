// services/unitConsultants.js
import axios from 'axios'

export const UnitConsultantsApi = {
  list: async (unitId) => {
    const { data } = await axios.get(`/api/unit-consultants/${unitId}`)
    return data?.data || []
  },
  create: async (payload) => axios.post(`/api/unit-consultants/create`, payload),
  update: async (payload) => axios.post(`/api/unit-consultants/update`, payload),
  remove: async (id) => axios.delete(`/api/unit-consultants/delete/${id}`)
}
