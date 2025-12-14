// services/typeOfPrice.js
import axios from "axios";

export const TypeOfPriceApi = {
  list: async () => {
    const { data } = await axios.get(`/api/type-of-prices`);
    return data?.data || [];
  },

  show: async (id) => {
    const { data } = await axios.get(`/api/type-of-prices/show/${id}`);
    return data?.data || null;
  },

  edit: async (id) => {
    const { data } = await axios.get(`/api/type-of-prices/edit/${id}`);
    return data || null;
  },

  create: async (payload) => {
    return axios.post(`/api/type-of-prices/create`, payload);
  },

  update: async (id, payload) => {
    return axios.post(`/api/type-of-prices/update/${id}`, payload);
  },

  remove: async (id) => {
    return axios.delete(`/api/type-of-prices/delete/${id}`);
  },
};
