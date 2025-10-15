import axios from "axios";

/* ---------- helpers ---------- */
function unwrapList(payload) {
  if (Array.isArray(payload)) return payload;
  if (Array.isArray(payload?.data)) return payload.data;
  if (Array.isArray(payload?.message)) return payload.message;
  if (Array.isArray(payload?.data?.data)) return payload.data.data;
  return [];
}
function toUrl(p) {
  return p ? `/storage/${p}` : "";
}
function normalize(q = {}) {
  return {
    id: q.id,
    title: q.title ?? "",
    other_title: q.other_title ?? "",
    user_id: q.user_id,
    type_of_building_id: q.type_of_building_id,
    type_of_price_id: q.type_of_price_id,
    pay_image: q.pay_image,
    pay_image_url: toUrl(q.pay_image),
    gallery: Array.isArray(q.unit_quote_gallery)
      ? q.unit_quote_gallery.map((g) => ({
          id: g.id,
          image: g.image,
          image_url: toUrl(g.image),
        }))
      : [],
    created_at: q.created_at,
    updated_at: q.updated_at,
  };
}

/* ---------- API ---------- */
export const UnitQuotesApi = {
  list: async () => {
    const { data } = await axios.get(`/api/unit-quotes`);
    return unwrapList(data).map(normalize);
  },
  show: async (id) => {
    const { data } = await axios.get(`/api/unit-quotes/show/${id}`);
    const row = data?.data ?? data;
    return normalize(row);
  },
  create: async (formData) => {
    const { data } = await axios.post(`/api/unit-quotes/create`, formData, {
      headers: { "Content-Type": "multipart/form-data" },
    });
    return data;
  },
  update: async (id, formData) => {
    const { data } = await axios.post(`/api/unit-quotes/update/${id}`, formData, {
      headers: { "Content-Type": "multipart/form-data" },
    });
    return data;
  },
  remove: async (id) => {
    const { data } = await axios.delete(`/api/unit-quotes/delete/${id}`);
    return data;
  },
};

/* ---------- FormData builders ---------- */
export function buildQuoteCreateFD(obj = {}) {
  const fd = new FormData();
  fd.append("title", obj.title ?? "");
  fd.append("other_title", obj.other_title ?? "");
  fd.append("user_id", obj.user_id ?? "");
  fd.append("type_of_building_id", obj.type_of_building_id ?? "");
  fd.append("type_of_price_id", obj.type_of_price_id ?? "");
  if (obj.pay_image) fd.append("pay_image", obj.pay_image);
  (obj.gallery || []).forEach((file, i) => {
    if (file) fd.append(`gallery[${i}]`, file);
  });
  return fd;
}

export function buildQuoteUpdateFD(obj = {}) {
  const fd = new FormData();
  if (obj.title !== undefined) fd.append("title", obj.title);
  if (obj.other_title !== undefined) fd.append("other_title", obj.other_title);
  if (obj.user_id !== undefined) fd.append("user_id", obj.user_id);
  if (obj.type_of_building_id !== undefined)
    fd.append("type_of_building_id", obj.type_of_building_id);
  if (obj.type_of_price_id !== undefined)
    fd.append("type_of_price_id", obj.type_of_price_id);
  if (obj.pay_image) fd.append("pay_image", obj.pay_image);
  if (Array.isArray(obj.gallery)) {
    obj.gallery.forEach((f, i) => {
      if (f) fd.append(`gallery[${i}]`, f);
    });
  }
  return fd;
}
