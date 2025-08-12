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
export function buildCreateBannerFD({ image, is_enabled = true }) {
  const fd = new FormData();
  if (image) fd.append("image", image);
  fd.append("is_enabled", (is_enabled ? 1 : 0).toString());
  return fd;
}

export function buildUpdateBannerFD({ image = null, is_enabled = null }) {
  const fd = new FormData();
  if (image) fd.append("image", image);
  if (is_enabled !== null) fd.append("is_enabled", (is_enabled ? 1 : 0).toString());
  return fd;
}
