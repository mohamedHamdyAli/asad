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
  const fd = new FormData()

  items.forEach((item, i) => {
    fd.append(`data[${i}][email]`, item.email)

    fd.append(`data[${i}][title][en]`, item.title.en)
    fd.append(`data[${i}][title][ar]`, item.title.ar)

    fd.append(`data[${i}][description][en]`, item.description.en)
    fd.append(`data[${i}][description][ar]`, item.description.ar)

    if (item.company_phone)
      fd.append(`data[${i}][company_phone]`, item.company_phone)

    if (item.representative_phone)
      fd.append(`data[${i}][representative_phone]`, item.representative_phone)

    if (item.company_address?.en)
      fd.append(`data[${i}][company_address][en]`, item.company_address.en)

    if (item.company_address?.ar)
      fd.append(`data[${i}][company_address][ar]`, item.company_address.ar)

    if (item.representative_name?.en)
      fd.append(`data[${i}][representative_name][en]`, item.representative_name.en)

    if (item.representative_name?.ar)
      fd.append(`data[${i}][representative_name][ar]`, item.representative_name.ar)

    if (item.image)
      fd.append(`data[${i}][image]`, item.image)
  })

  return fd
}


// UPDATE expects a single object nested under 'data'
// data = { title?:{en,ar}, description?:{en,ar}, email?:string, image?:File }
export function buildContractorsUpdateFD(item = {}) {
  const fd = new FormData()

  if (item.email)
    fd.append('data[email]', item.email)

  if (item.title?.en)
    fd.append('data[title][en]', item.title.en)

  if (item.title?.ar)
    fd.append('data[title][ar]', item.title.ar)

  if (item.description?.en)
    fd.append('data[description][en]', item.description.en)

  if (item.description?.ar)
    fd.append('data[description][ar]', item.description.ar)

  if (item.company_phone)
    fd.append('data[company_phone]', item.company_phone)

  if (item.representative_phone)
    fd.append('data[representative_phone]', item.representative_phone)

  if (item.company_address?.en)
    fd.append('data[company_address][en]', item.company_address.en)

  if (item.company_address?.ar)
    fd.append('data[company_address][ar]', item.company_address.ar)

  if (item.representative_name?.en)
    fd.append('data[representative_name][en]', item.representative_name.en)

  if (item.representative_name?.ar)
    fd.append('data[representative_name][ar]', item.representative_name.ar)

  if (item.image)
    fd.append('data[image]', item.image)

  return fd
}

