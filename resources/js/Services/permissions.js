import axios from 'axios'

export const PermissionsApi = {
  list: async () => {
    const { data } = await axios.get('/api/permissions')
    return data.data
  }
}
