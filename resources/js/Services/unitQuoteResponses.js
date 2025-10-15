import axios from "axios";

/* ---------------- helpers ---------------- */
function tryJson(v) {
  try {
    return typeof v === "string" ? JSON.parse(v) : v;
  } catch {
    return v;
  }
}

function unwrapList(payload) {
  if (Array.isArray(payload)) return payload;
  if (Array.isArray(payload?.data)) return payload.data;
  if (Array.isArray(payload?.data?.data)) return payload.data.data;
  return [];
}

function normalize(row = {}) {
  const title = tryJson(row.title);
  const timeline = tryJson(row.time_line);
  return {
    id: row.id,
    unit_quote_id: row.unit_quote_id,
    vendor_id: row.vendor_id,
    user_id: row.user_id,
    title_en: title?.en ?? "",
    title_ar: title?.ar ?? "",
    price: row.price ?? "",
    time_line_en: timeline?.en ?? "",
    time_line_ar: timeline?.ar ?? "",
    created_at: row.created_at,
    updated_at: row.updated_at,
  };
}

/* ---------------- API ---------------- */
export const UnitQuoteResponsesApi = {
  list: async () => {
    const { data } = await axios.get(`/api/unit-quote-responses`);
    return unwrapList(data).map(normalize);
  },

  show: async (id) => {
    const { data } = await axios.get(`/api/unit-quote-responses/show/${id}`);
    const row = data?.data ?? data;
    return normalize(row);
  },

  create: async (formData) => {
    const { data } = await axios.post(`/api/unit-quote-responses/create`, formData, {
      headers: { "Content-Type": "multipart/form-data" },
    });
    return data;
  },

  update: async (id, formData) => {
    const { data } = await axios.post(`/api/unit-quote-responses/update/${id}`, formData, {
      headers: { "Content-Type": "multipart/form-data" },
    });
    return data;
  },

  remove: async (id) => {
    const { data } = await axios.delete(`/api/unit-quote-responses/delete/${id}`);
    return data;
  },
};

/* ---------------- FormData builders ---------------- */
export function buildQuoteResponseCreateFD(obj = {}) {
  const fd = new FormData();
  fd.append("data[unit_quote_id]", obj.unit_quote_id ?? "");
  fd.append("data[vendor_id]", obj.vendor_id ?? "");
  fd.append("data[user_id]", obj.user_id ?? "");
  fd.append("data[title][en]", obj.title?.en ?? "");
  fd.append("data[title][ar]", obj.title?.ar ?? "");
  if (obj.price !== undefined) fd.append("data[price]", obj.price);
  if (obj.time_line) {
    fd.append("data[time_line][en]", obj.time_line.en ?? "");
    fd.append("data[time_line][ar]", obj.time_line.ar ?? "");
  }
  return fd;
}

export function buildQuoteResponseUpdateFD(obj = {}) {
  const fd = new FormData();
  if (obj.unit_quote_id !== undefined)
    fd.append("data[unit_quote_id]", obj.unit_quote_id);
  if (obj.vendor_id !== undefined) fd.append("data[vendor_id]", obj.vendor_id);
  if (obj.user_id !== undefined) fd.append("data[user_id]", obj.user_id);
  if (obj.title) {
    fd.append("data[title][en]", obj.title.en ?? "");
    fd.append("data[title][ar]", obj.title.ar ?? "");
  }
  if (obj.price !== undefined) fd.append("data[price]", obj.price);
  if (obj.time_line) {
    fd.append("data[time_line][en]", obj.time_line.en ?? "");
    fd.append("data[time_line][ar]", obj.time_line.ar ?? "");
  }
  return fd;
}
