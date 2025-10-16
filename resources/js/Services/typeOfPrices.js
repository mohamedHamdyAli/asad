import axios from 'axios'

export const TypeOfPricesApi = {
  list: async () => {
    const { data } = await axios.get('/api/type-of-prices')
    // Adjust if your backend returns { status: "success", data: [...] }
    return data.data || data
  },
}
