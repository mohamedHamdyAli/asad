import axios from "axios";

/* ---------------- helpers ---------------- */
function tryJson(v){ try { return typeof v === "string" ? JSON.parse(v) : v; } catch { return v; } }
function unwrapList(payload){
  if (Array.isArray(payload)) return payload;
  if (Array.isArray(payload?.data)) return payload.data;
  if (Array.isArray(payload?.message)) return payload.message;
  if (Array.isArray(payload?.data?.data)) return payload.data.data;
  return [];
}
function toUrl(p){ return p ? `/storage/${p}` : ""; }
function normalize(row = {}) {
  const title = tryJson(row.title) || {}
  const description = tryJson(row.description) || {}
  const company_address = tryJson(row.company_address) || {}
  const representative_name = tryJson(row.representative_name) || {}

  return {
    id: row.id,

    email: row.email || "",

    image: row.image || "",
    image_url: toUrl(row.image),

    title_en: title.en || "",
    title_ar: title.ar || "",

    description_en: description.en || "",
    description_ar: description.ar || "",

    company_phone: row.company_phone || "",
    representative_phone: row.representative_phone || "",

    company_address: {
      en: company_address.en || "",
      ar: company_address.ar || "",
    },

    representative_name: {
      en: representative_name.en || "",
      ar: representative_name.ar || "",
    },

    created_at: row.created_at,
    updated_at: row.updated_at,
  }
}


/* ---------------- API ---------------- */
export const ConsultantsApi = {
  list: async () => {
    const { data } = await axios.get(`/api/consultants`);
    return unwrapList(data).map(normalize);
  },
  show: async (id) => {
    const { data } = await axios.get(`/api/consultants/show/${id}`);
    const row = data?.data ?? data;
    return normalize(row);
  },
  edit: async (id) => {
    const { data } = await axios.get(`/api/consultants/edit/${id}`);
    const row = data?.data ?? data;
    return normalize(row);
  },
  create: async (formData) => {
    const { data } = await axios.post(`/api/consultants/create`, formData, {
      headers: { "Content-Type": "multipart/form-data" },
    });
    return data;
  },
  update: async (id, formData) => {
    const { data } = await axios.post(`/api/consultants/update/${id}`, formData, {
      headers: { "Content-Type": "multipart/form-data" },
    });
    return data;
  },
  remove: async (id) => {
    const { data } = await axios.delete(`/api/consultants/delete/${id}`);
    return data;
  },
};

/* ---------------- FormData builders ---------------- */
// CREATE supports batch, but UI can send one item too
// items = [{ title:{en,ar}, description:{en,ar}, email, image:File }]
export function buildConsultantsCreateFD(items = []) {
  const fd = new FormData();

  items.forEach((it, i) => {
    fd.append(`data[${i}][title][en]`, it?.title?.en ?? "");
    fd.append(`data[${i}][title][ar]`, it?.title?.ar ?? "");

    fd.append(`data[${i}][description][en]`, it?.description?.en ?? "");
    fd.append(`data[${i}][description][ar]`, it?.description?.ar ?? "");

    fd.append(`data[${i}][email]`, it?.email ?? "");
    fd.append(`data[${i}][password]`, it?.password ?? "");

    fd.append(`data[${i}][company_phone]`, it?.company_phone ?? "");
    fd.append(`data[${i}][representative_phone]`, it?.representative_phone ?? "");

    fd.append(`data[${i}][company_address][en]`, it?.company_address?.en ?? "");
    fd.append(`data[${i}][company_address][ar]`, it?.company_address?.ar ?? "");

    fd.append(`data[${i}][representative_name][en]`, it?.representative_name?.en ?? "");
    fd.append(`data[${i}][representative_name][ar]`, it?.representative_name?.ar ?? "");

    if (it?.image instanceof File) {
      fd.append(`data[${i}][image]`, it.image);
    }
  });

  return fd;
}


// UPDATE expects a single object nested under 'data'
// data = { title?:{en,ar}, description?:{en,ar}, email?:string, image?:File }
export function buildConsultantsUpdateFD(data = {}) {
  const fd = new FormData();

  if (data?.title) {
    if (data.title.en !== undefined) fd.append(`data[title][en]`, data.title.en);
    if (data.title.ar !== undefined) fd.append(`data[title][ar]`, data.title.ar);
  }

  if (data?.description) {
    if (data.description.en !== undefined) fd.append(`data[description][en]`, data.description.en);
    if (data.description.ar !== undefined) fd.append(`data[description][ar]`, data.description.ar);
  }

  if (data?.email !== undefined) {
    fd.append(`data[email]`, data.email);
  }

  if (data?.password !== undefined && data.password !== '') {
    fd.append(`data[password]`, data.password);
  }

  if (data?.company_phone !== undefined) {
    fd.append(`data[company_phone]`, data.company_phone);
  }

  if (data?.representative_phone !== undefined) {
    fd.append(`data[representative_phone]`, data.representative_phone);
  }

  if (data?.company_address) {
    if (data.company_address.en !== undefined)
      fd.append(`data[company_address][en]`, data.company_address.en);
    if (data.company_address.ar !== undefined)
      fd.append(`data[company_address][ar]`, data.company_address.ar);
  }

  if (data?.representative_name) {
    if (data.representative_name.en !== undefined)
      fd.append(`data[representative_name][en]`, data.representative_name.en);
    if (data.representative_name.ar !== undefined)
      fd.append(`data[representative_name][ar]`, data.representative_name.ar);
  }

  if (data?.image instanceof File) {
    fd.append(`data[image]`, data.image);
  }

  return fd;
}

