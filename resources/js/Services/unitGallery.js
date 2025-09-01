// resources/js/Services/unitGallery.js
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
function unwrapOne(payload) {
  return payload?.data ?? payload?.message ?? payload;
}
function toUrl(p) {
  return p ? `/storage/${p}` : "";
}
function normalize(row = {}) {
  const t = tryJson(row.title);
  const title = t && typeof t === "object" ? t : { en: t ?? "", ar: t ?? "" };
  return {
    id: row.id,
    unit_id: row.unit_id,
    folder_id: row.folder_id,
    date: row.date ?? "",
    image: row.image ?? "",
    image_url: toUrl(row.image),
    title_en: title.en ?? "",
    title_ar: title.ar ?? "",
    created_at: row.created_at,
    updated_at: row.updated_at,
  };
}

/* ---------- API ---------- */
export const UnitGalleryApi = {
  list: async (unitId) => {
    const { data } = await axios.get(`/api/unit-gallery/${unitId}`);
    return unwrapList(data).map(normalize);
  },

  // POST /api/unit-gallery/create
  create: async (formData) => {
    const { data } = await axios.post(`/api/unit-gallery/create`, formData, {
      headers: { "Content-Type": "multipart/form-data" },
    });
    return data;
  },

  // POST /api/unit-gallery/update
  update: async (formData) => {
    const { data } = await axios.post(`/api/unit-gallery/update`, formData, {
      headers: { "Content-Type": "multipart/form-data" },
    });
    return data;
  },

  // DELETE /api/unit-gallery/delete/{id}
  remove: async (id) => {
    const { data } = await axios.delete(`/api/unit-gallery/delete/${id}`);
    return data;
  },
};

/* ---------- FormData builders ---------- */
/**
 * Create items payload (batch).
 * items: [{ folder_id, date, image(File), title?: {en,ar} }]
 */
// ... keep previous imports & helpers

export function buildGalleryCreateFD(unit_id, items = []) {
  const fd = new FormData();
  fd.append("unit_id", String(unit_id));
  items.forEach((it, i) => {
    // REQUIRED by rules:
    fd.append(`data[${i}][folder_id]`, String(it.folder_id ?? ""));
    fd.append(`data[${i}][date]`, it.date ?? "");
    fd.append(`data[${i}][title][en]`, it?.title?.en ?? "");
    fd.append(`data[${i}][title][ar]`, it?.title?.ar ?? "");
    if (it.image) fd.append(`data[${i}][image]`, it.image); // required; UI ensures provided
  });
  return fd;
}

export function buildGalleryUpdateFD(unit_id, items = []) {
  const fd = new FormData();
  fd.append("unit_id", String(unit_id));
  items.forEach((it, i) => {
    // id (rule says nullable, but we always send for clarity)
    fd.append(`data[${i}][id]`, String(it.id ?? ""));
    // REQUIRED by rules even on update:
    fd.append(`data[${i}][folder_id]`, String(it.folder_id ?? ""));
    // Optional fields:
    if (it.date !== undefined) fd.append(`data[${i}][date]`, it.date ?? "");
    if (it?.title?.en !== undefined) fd.append(`data[${i}][title][en]`, it.title.en ?? "");
    if (it?.title?.ar !== undefined) fd.append(`data[${i}][title][ar]`, it.title.ar ?? "");
    if (it.image) fd.append(`data[${i}][image]`, it.image);
  });
  return fd;
}
