import axios from "axios";

function tryJson(v) { try { return typeof v === "string" ? JSON.parse(v) : v; } catch { return v; } }
function unwrapList(payload) {
  if (Array.isArray(payload)) return payload;
  if (Array.isArray(payload?.data)) return payload.data;
  if (Array.isArray(payload?.message)) return payload.message;
  if (Array.isArray(payload?.data?.data)) return payload.data.data;
  return [];
}
function toUrl(p) { return p ? `/storage/${p}` : ""; }
function fileName(p) { return (p || "").split("/").pop() || ""; }
function extOf(p) { return (fileName(p).split(".").pop() || "").toLowerCase(); }
function iconFor(p) {
  const e = extOf(p);
  if (e === "pdf") return "📕 PDF";
  if (e === "doc" || e === "docx") return "📝 DOC";
  if (e === "xls" || e === "xlsx") return "📊 XLS";
  return "📄";
}
function normalize(row = {}) {
  const t = tryJson(row.title);
  const title = t && typeof t === "object" ? t : { en: t ?? "", ar: t ?? "" };
  const url = toUrl(row.file);
  return {
    id: row.id,
    unit_id: row.unit_id,
    file: row.file ?? "",
    file_url: url,
    file_name: fileName(row.file),
    file_icon: iconFor(row.file),
    title_en: title.en ?? "",
    title_ar: title.ar ?? "",
    created_at: row.created_at,
    updated_at: row.updated_at,
  };
}

/* ---------- API ---------- */
export const UnitReportsApi = {
  list: async (unitId) => {
    const { data } = await axios.get(`/api/unit-report/${unitId}`);
    return unwrapList(data).map(normalize);
  },
  create: async (formData) => {
    const { data } = await axios.post(`/api/unit-report/create`, formData, {
      headers: { "Content-Type": "multipart/form-data" },
    });
    return data;
  },
  update: async (formData) => {
    const { data } = await axios.post(`/api/unit-report/update`, formData, {
      headers: { "Content-Type": "multipart/form-data" },
    });
    return data;
  },
  remove: async (id) => {
    const { data } = await axios.delete(`/api/unit-report/delete/${id}`);
    return data;
  },
};

/* ---------- FormData builders (align with UnitReportRequest) ---------- */
// CREATE: items = [{ file:File, title:{en,ar} }]
export function buildReportsCreateFD(unit_id, items = []) {
  const fd = new FormData();
  fd.append("unit_id", String(unit_id));
  items.forEach((it, i) => {
    // required per rules
    fd.append(`data[${i}][title][en]`, it?.title?.en ?? "");
    fd.append(`data[${i}][title][ar]`, it?.title?.ar ?? "");
    if (it.file) fd.append(`data[${i}][file]`, it.file); // required
  });
  return fd;
}

// UPDATE: items = [{ id, file?:File, title?:{en,ar} }]
export function buildReportsUpdateFD(unit_id, items = []) {
  const fd = new FormData();
  fd.append("unit_id", String(unit_id));
  items.forEach((it, i) => {
    // id required on update
    fd.append(`data[${i}][id]`, String(it.id ?? ""));
    if (it?.title?.en !== undefined) fd.append(`data[${i}][title][en]`, it.title.en ?? "");
    if (it?.title?.ar !== undefined) fd.append(`data[${i}][title][ar]`, it.title.ar ?? "");
    if (it.file) fd.append(`data[${i}][file]`, it.file); // optional replace
  });
  return fd;
}
