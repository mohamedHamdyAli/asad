import axios from 'axios'

export const TypeOfBuildingsApi = {
  list: async () => {
    const { data } = await axios.get('/api/type-of-buildings')
    // Adjust if your backend returns { status: "success", data: [...] }
    return data.data || data
  },
}
