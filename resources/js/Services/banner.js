import axios from "axios";

function normalize(item = {}) {
  return {
    ...item,
    image_url: item.image ? `/storage/${item.image}` : "",
    is_enabled: Number(item.is_enabled) === 1 || item.is_enabled === true,
  };
}

function unwrapList(payload) {
  if (Array.isArray(payload)) return payload;
  if (Array.isArray(payload?.message)) return payload.message;
  if (Array.isArray(payload?.data)) return payload.data;
  if (Array.isArray(payload?.data?.data)) return payload.data.data;
  return [];
}

function unwrapOne(payload) {
  return payload?.data ?? payload?.message ?? payload;
}

/* ---------- API ---------- */
export const BannerApi = {
  index: async (params = {}) => {
    const { data } = await axios.get("/api/banners", { params });
    return unwrapList(data).map(normalize);
  },

  edit: async (id) => {
    const { data } = await axios.get(`/api/banners/edit/${id}`);
    return normalize(unwrapOne(data));
  },

  create: async (formData) => {
    const { data } = await axios.post("/api/banners/create", formData, {
      headers: { "Content-Type": "multipart/form-data" },
    });
    return data;
  },

  update: async (id, formData) => {
    const { data } = await axios.post(`/api/banners/update/${id}`, formData, {
      headers: { "Content-Type": "multipart/form-data" },
    });
    return data;
  },

  destroy: async (id) => {
    const { data } = await axios.delete(`/api/banners/delete/${id}`);
    return data;
  },

  publicGet: async () => {
    const { data } = await axios.get("/api/user/get-banner");
    return unwrapList(data).map(normalize);
  },
};

/* ---------- FormData builders ---------- */
// Services/banner.js

export function buildCreateBannerFD({ image, is_enabled = true, page, banner_type = 'guest', name = {}, description = {} }) {
  const fd = new FormData()

  if (image) fd.append("image", image)
  fd.append("is_enabled", is_enabled ? "1" : "0")
  if (page) fd.append("page", page)
  if (banner_type) fd.append("banner_type", banner_type)

  if (name?.en) fd.append("name[en]", name.en)
  if (name?.ar) fd.append("name[ar]", name.ar)

  if (description?.en) fd.append("description[en]", description.en)
  if (description?.ar) fd.append("description[ar]", description.ar)

  return fd
}

export function buildUpdateBannerFD({ image = null, is_enabled = null, page = null, banner_type = null, name = null, description = null }) {
  const fd = new FormData()

  if (image) fd.append("image", image)
  if (is_enabled !== null) fd.append("is_enabled", is_enabled ? "1" : "0")
  if (page) fd.append("page", page)
  if (banner_type) fd.append("banner_type", banner_type)

  if (name) {
    if (name?.en !== undefined) fd.append("name[en]", name.en ?? "")
    if (name?.ar !== undefined) fd.append("name[ar]", name.ar ?? "")
  }

  if (description) {
    if (description?.en !== undefined) fd.append("description[en]", description.en ?? "")
    if (description?.ar !== undefined) fd.append("description[ar]", description.ar ?? "")
  }

  return fd
}


