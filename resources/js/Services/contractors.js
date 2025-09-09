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
function normalize(row = {}){
  const t = tryJson(row.title);
  const d = tryJson(row.description);
  const title = t && typeof t === "object" ? t : { en: t ?? "", ar: t ?? "" };
  const desc  = d && typeof d === "object" ? d : { en: d ?? "", ar: d ?? "" };
  return {
    id: row.id,
    email: row.email || "",
    image: row.image || "",
    image_url: toUrl(row.image),
    title_en: title.en ?? "",
    title_ar: title.ar ?? "",
    description_en: desc.en ?? "",
    description_ar: desc.ar ?? "",
    created_at: row.created_at,
    updated_at: row.updated_at,
  };
}

/* ---------------- API ---------------- */
export const ContractorsApi = {
  list: async () => {
    const { data } = await axios.get(`/api/contractors`);
    return unwrapList(data).map(normalize);
  },
  show: async (id) => {
    const { data } = await axios.get(`/api/contractors/show/${id}`);
    const row = data?.data ?? data;
    return normalize(row);
  },
  edit: async (id) => {
    const { data } = await axios.get(`/api/contractors/edit/${id}`);
    const row = data?.data ?? data;
    return normalize(row);
  },
  create: async (formData) => {
    const { data } = await axios.post(`/api/contractors/create`, formData, {
      headers: { "Content-Type": "multipart/form-data" },
    });
    return data;
  },
  update: async (id, formData) => {
    const { data } = await axios.post(`/api/contractors/update/${id}`, formData, {
      headers: { "Content-Type": "multipart/form-data" },
    });
    return data;
  },
  remove: async (id) => {
    const { data } = await axios.delete(`/api/contractors/delete/${id}`);
    return data;
  },
};

/* ---------------- FormData builders ---------------- */
// CREATE supports batch, but UI can send one item too
// items = [{ title:{en,ar}, description:{en,ar}, email, image:File }]
export function buildContractorsCreateFD(items = []) {
  const fd = new FormData();
  items.forEach((it, i) => {
    fd.append(`data[${i}][title][en]`, it?.title?.en ?? "");
    fd.append(`data[${i}][title][ar]`, it?.title?.ar ?? "");
    fd.append(`data[${i}][description][en]`, it?.description?.en ?? "");
    fd.append(`data[${i}][description][ar]`, it?.description?.ar ?? "");
    fd.append(`data[${i}][email]`, it?.email ?? "");
    if (it?.image) fd.append(`data[${i}][image]`, it.image);
  });
  return fd;
}

// UPDATE expects a single object nested under 'data'
// data = { title?:{en,ar}, description?:{en,ar}, email?:string, image?:File }
export function buildContractorsUpdateFD(data = {}) {
  const fd = new FormData();
  if (data?.title) {
    if (data.title.en !== undefined) fd.append(`data[title][en]`, data.title.en);
    if (data.title.ar !== undefined) fd.append(`data[title][ar]`, data.title.ar);
  }
  if (data?.description) {
    if (data.description.en !== undefined) fd.append(`data[description][en]`, data.description.en);
    if (data.description.ar !== undefined) fd.append(`data[description][ar]`, data.description.ar);
  }
  if (data?.email !== undefined) fd.append(`data[email]`, data.email);
  if (data?.image) fd.append(`data[image]`, data.image);
  return fd;
}
