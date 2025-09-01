// resources/js/Services/units.js
import axios from "axios";

/* ---------- helpers ---------- */
function tryJson(v) {
  try { return typeof v === "string" ? JSON.parse(v) : v; } catch { return v; }
}
function extractLang(v) {
  const o = tryJson(v);
  if (o && typeof o === "object") return { en: o.en ?? "", ar: o.ar ?? "" };
  return { en: o ?? "", ar: o ?? "" };
}
function unwrapList(payload) {
  // Accepts: {status, data:[...]}, {message:[...]}, plain []
  if (Array.isArray(payload)) return payload;
  if (Array.isArray(payload?.data)) return payload.data;
  if (Array.isArray(payload?.message)) return payload.message;
  if (Array.isArray(payload?.data?.data)) return payload.data.data;
  return [];
}
function unwrapOne(payload) {
  return payload?.data ?? payload?.message ?? payload;
}
function toUrl(path) {
  return path ? `/storage/${path}` : "";
}
function dateOnly(s) {
  if (!s) return "";
  // handle "YYYY-MM-DD" or "YYYY-MM-DDTHH:mm:ss"
  return String(s).slice(0, 10);
}
function normalize(u = {}) {
  const name = extractLang(u.name);
  const description = extractLang(u.description);
  const gallery = Array.isArray(u.home_unit_gallery)
    ? u.home_unit_gallery.map(g => ({
        id: g.id ?? undefined,
        image: g.image,
        image_url: toUrl(g.image),
      }))
    : Array.isArray(u.gallery)
    ? u.gallery.map(g => ({
        id: g.id ?? undefined,
        image: g.image,
        image_url: toUrl(g.image),
      }))
    : [];

  return {
    ...u,
    name_en: name.en,
    name_ar: name.ar,
    description_en: description.en,
    description_ar: description.ar,
    location: u.location ?? "",
    lat: u.lat ?? null,
    long: u.long ?? null,
    start_date: dateOnly(u.start_date),
    end_date: dateOnly(u.end_date),
    status: u.status ?? "",
    user_id: u.user_id ?? null,
    vendor_id: u.vendor_id ?? null,
    cover_image_url: toUrl(u.cover_image),
    gallery,
  };
}

/* ---------- API ---------- */
export const UnitsApi = {
  list: async (params = {}) => {
    const { data } = await axios.get("/api/units", { params });
    return unwrapList(data).map(normalize);
  },
  listByUser: async (userId, params = {}) => {
    const { data } = await axios.get(`/api/units/user/${userId}`, { params });
    return unwrapList(data).map(normalize);
  },
  listByVendor: async (vendorId, params = {}) => {
    const { data } = await axios.get(`/api/units/vendor/${vendorId}`, { params });
    return unwrapList(data).map(normalize);
  },
  show: async (id) => {
    const { data } = await axios.get(`/api/units/show/${id}`);
    return normalize(unwrapOne(data));
  },
  create: async (formData) => {
    const { data } = await axios.post("/api/units/create", formData, {
      headers: { "Content-Type": "multipart/form-data" },
    });
    return data;
  },
  update: async (id, formData) => {
    const { data } = await axios.post(`/api/units/update/${id}`, formData, {
      headers: { "Content-Type": "multipart/form-data" },
    });
    return data;
  },
  remove: async (id) => {
    const { data } = await axios.delete(`/api/units/delete/${id}`);
    return data;
  },
};

/* ---------- FormData builders ---------- */
export function buildUnitCreateFD({
  name = { en: "", ar: "" },
  description = { en: "", ar: "" },
  location,
  lat,
  long,
  start_date,
  end_date,
  cover_image,
  gallery = [],
  user_id,
  vendor_id,
  // optional on create
  status,
  ...rest
}) {
  const fd = new FormData();
  // required bilingual fields
  fd.append("name[en]", name.en ?? "");
  fd.append("name[ar]", name.ar ?? "");
  fd.append("description[en]", description.en ?? "");
  fd.append("description[ar]", description.ar ?? "");
  // required scalar fields
  fd.append("location", location ?? "");
  fd.append("lat", String(lat ?? ""));
  fd.append("long", String(long ?? ""));
  fd.append("start_date", start_date ?? "");
  fd.append("end_date", end_date ?? "");
  // required files
  if (cover_image) fd.append("cover_image", cover_image);
  (gallery || []).forEach((file) => file && fd.append("gallery[]", file));
  // required associations
  fd.append("user_id", String(user_id ?? ""));
  fd.append("vendor_id", String(vendor_id ?? ""));
  // optional
  if (status) fd.append("status", status);
  Object.entries(rest).forEach(([k, v]) => {
    if (v !== undefined && v !== null && v !== "") fd.append(k, v);
  });
  return fd;
}

export function buildUnitUpdateFD(partial = {}) {
  const {
    name,
    description,
    location,
    lat,
    long,
    start_date,
    end_date,
    cover_image,
    gallery,
    user_id,
    vendor_id,
    status,
    ...rest
  } = partial;

  const fd = new FormData();
  if (name) {
    if (name.en !== undefined) fd.append("name[en]", name.en ?? "");
    if (name.ar !== undefined) fd.append("name[ar]", name.ar ?? "");
  }
  if (description) {
    if (description.en !== undefined) fd.append("description[en]", description.en ?? "");
    if (description.ar !== undefined) fd.append("description[ar]", description.ar ?? "");
  }
  if (location !== undefined) fd.append("location", location ?? "");
  if (lat !== undefined) fd.append("lat", String(lat ?? ""));
  if (long !== undefined) fd.append("long", String(long ?? ""));
  if (start_date !== undefined) fd.append("start_date", start_date ?? "");
  if (end_date !== undefined) fd.append("end_date", end_date ?? "");
  if (cover_image) fd.append("cover_image", cover_image);
  if (Array.isArray(gallery) && gallery.length) {
    gallery.forEach((file) => file && fd.append("gallery[]", file));
  }
  if (user_id !== undefined) fd.append("user_id", String(user_id ?? ""));
  if (vendor_id !== undefined) fd.append("vendor_id", String(vendor_id ?? ""));
  if (status !== undefined && status !== "") fd.append("status", status);
  Object.entries(rest).forEach(([k, v]) => {
    if (v !== undefined && v !== null && v !== "") fd.append(k, v);
  });
  return fd;
}
