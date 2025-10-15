import axios from 'axios'

export const UnitPaymentLogsApi = {
  unitLogs: async (unitId) => {
    const { data } = await axios.get(`/api/unit-payments/logs/${unitId}`)
    return data?.data ?? []
  },
  installmentLogs: async (installmentId) => {
    const { data } = await axios.get(`/api/unit-payments/installment/${installmentId}/logs`)
    return data?.data ?? []
  },
}
