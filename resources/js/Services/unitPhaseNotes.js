import axios from 'axios'

function unwrap(p){
  if (Array.isArray(p?.data)) return p.data
  if (Array.isArray(p)) return p
  return []
}

export const UnitPhaseNotesApi = {
  list: async (unitId, phaseId) => {
    const { data } = await axios.get('/api/unit-phase-notes', {
      params: { unit_id: unitId, unit_phase_id: phaseId }
    })
    return unwrap(data)
  },

  create: async (payload) => {
    const { data } = await axios.post('/api/unit-phase-notes/create', {
      data: payload
    })
    return data
  },

  update: async (id, payload) => {
    const { data } = await axios.post(`/api/unit-phase-notes/update/${id}`, {
      data: payload
    })
    return data
  },

  remove: async (id) => {
    const { data } = await axios.delete(`/api/unit-phase-notes/delete/${id}`)
    return data
  },
}
