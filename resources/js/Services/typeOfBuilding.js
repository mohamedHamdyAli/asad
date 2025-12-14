// services/typeOfBuilding.js
import axios from "axios";

export const TypeOfBuildingApi = {
  list: async () => {
    const { data } = await axios.get(`/api/type-of-buildings`);
    return data?.data || [];
  },

  show: async (id) => {
    const { data } = await axios.get(`/api/type-of-buildings/show/${id}`);
    return data?.data || null;
  },

  edit: async (id) => {
    const { data } = await axios.get(`/api/type-of-buildings/edit/${id}`);
    return data?.data || null;
  },

  create: async (payload) => {
    return axios.post(`/api/type-of-buildings/create`, payload);
  },

  update: async (id, payload) => {
    return axios.post(`/api/type-of-buildings/update/${id}`, payload);
  },

  remove: async (id) => {
    return axios.delete(`/api/type-of-buildings/delete/${id}`);
  },
};
