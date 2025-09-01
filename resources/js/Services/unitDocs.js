// resources/js/Services/unitDocs.js
import axios from "axios";

/* ---------- helpers ---------- */
function tryJson(v) {
  try { return typeof v === "string" ? JSON.parse(v) : v; } catch { return v; }
}
function unwrapList(payload) {
  if (Array.isArray(payload)) return payload;
  if (Array.isArray(payload?.data)) return payload.data;
  if (Array.isArray(payload?.message)) return payload.message;
  if (Array.isArray(payload?.data?.data)) return payload.data.data;
  return [];
}
function toUrl(p) { return p ? `/storage/${p}` : ""; }
function fileNameFromPath(p) { return (p || "").split("/").pop() || ""; }
function isPdf(p) { return /\.pdf$/i.test(p || ""); }

function normalize(row = {}) {
  const t = tryJson(row.title);
  const title = t && typeof t === "object" ? t : { en: t ?? "", ar: t ?? "" };
  const url = toUrl(row.file);
  return {
    id: row.id,
    unit_id: row.unit_id,
    folder_id: row.folder_id,
    file: row.file ?? "",
    file_url: url,
    file_name: fileNameFromPath(row.file),
    is_pdf: isPdf(row.file),
    title_en: title.en ?? "",
    title_ar: title.ar ?? "",
    created_at: row.created_at,
    updated_at: row.updated_at,
  };
}

/* ---------- API ---------- */
export const UnitDocsApi = {
  list: async (unitId) => {
    const { data } = await axios.get(`/api/unit-docs/${unitId}`);
    return unwrapList(data).map(normalize);
  },
  create: async (formData) => {
    const { data } = await axios.post(`/api/unit-docs/create`, formData, {
      headers: { "Content-Type": "multipart/form-data" },
    });
    return data;
  },
  update: async (formData) => {
    const { data } = await axios.post(`/api/unit-docs/update`, formData, {
      headers: { "Content-Type": "multipart/form-data" },
    });
    return data;
  },
  remove: async (id) => {
    const { data } = await axios.delete(`/api/unit-docs/delete/${id}`);
    return data;
  },
};

/* ---------- FormData builders (align with UnitDocsRequest) ---------- */
/** CREATE: items = [{ folder_id, file:File, title:{en,ar} }] */
export function buildDocsCreateFD(unit_id, items = []) {
  const fd = new FormData();
  fd.append("unit_id", String(unit_id));
  items.forEach((it, i) => {
    // required per rules
    fd.append(`data[${i}][folder_id]`, String(it.folder_id ?? ""));
    fd.append(`data[${i}][title][en]`, it?.title?.en ?? "");
    fd.append(`data[${i}][title][ar]`, it?.title?.ar ?? "");
    if (it.file) fd.append(`data[${i}][file]`, it.file); // required; UI ensures it’s present
  });
  return fd;
}

/** UPDATE: items = [{ id, folder_id, file?:File, title?:{en,ar} }] */
export function buildDocsUpdateFD(unit_id, items = []) {
  const fd = new FormData();
  fd.append("unit_id", String(unit_id));
  items.forEach((it, i) => {
    // id required by rules
    fd.append(`data[${i}][id]`, String(it.id ?? ""));
    // folder_id required even on update
    fd.append(`data[${i}][folder_id]`, String(it.folder_id ?? ""));
    // optionals
    if (it?.title?.en !== undefined) fd.append(`data[${i}][title][en]`, it.title.en ?? "");
    if (it?.title?.ar !== undefined) fd.append(`data[${i}][title][ar]`, it.title.ar ?? "");
    if (it.file) fd.append(`data[${i}][file]`, it.file);
  });
  return fd;
}
