import axios from "axios";

function tryJson(v) {
  try { return typeof v === "string" ? JSON.parse(v) : v; } catch { return v; }
}

function extractLang(objOrStr) {
  const o = tryJson(objOrStr);
  if (o && typeof o === "object") {
    return { en: o.en ?? "", ar: o.ar ?? "" };
  }
  return { en: o ?? "", ar: o ?? "" };
}

function normalized(item) {
  const name = extractLang(item?.name);
  const description = extractLang(item?.description);
  return {
    ...item,
    name_en: name.en,
    name_ar: name.ar,
    description_en: description.en,
    description_ar: description.ar,
    image_url: item?.image ? `/storage/${item.image}` : "",
    is_enabled: Number(item?.is_enabled) === 1 || item?.is_enabled === true
  };
}

export const IntroApi = {
  index: async (params = {}) => {
    const { data } = await axios.get("/api/intro", { params });
    const rows = Array.isArray(data) ? data : (data?.data ?? data ?? []);
    return rows.map(normalized);
  },
  edit: async (id) => {
    const { data } = await axios.get(`/api/intro/edit/${id}`);
    const row = data?.data ?? data;
    return normalized(row);
  },
  create: async (fd) => {
    const { data } = await axios.post("/api/intro/create", fd, {
      headers: { "Content-Type": "multipart/form-data" },
    });
    return data;
  },
  update: async (id, fd) => {
    const { data } = await axios.post(`/api/intro/update/${id}`, fd, {
      headers: { "Content-Type": "multipart/form-data" },
    });
    return data;
  },
  destroy: async (id) => {
    const { data } = await axios.delete(`/api/intro/delete/${id}`);
    return data;
  },
  publicGetIntro: async () => {
    const { data } = await axios.get("/api/user/get-intro");
    const rows = Array.isArray(data) ? data : (data?.data ?? data ?? []);
    return rows.map(normalized);
  },
};

export function buildIntroFormData({
  name = { en: "", ar: "" },
  description = { en: "", ar: "" },
  image = null,
  order = "",
  is_enabled = true,
}) {
  const fd = new FormData();

  fd.append("name[en]", name.en ?? "");
  fd.append("name[ar]", name.ar ?? "");

  fd.append("description[en]", description.en ?? "");
  fd.append("description[ar]", description.ar ?? "");

  if (image) fd.append("image", image);
  if (order !== "" && order !== null && order !== undefined) fd.append("order", String(order));

  fd.append("is_enabled", (is_enabled ? 1 : 0).toString());

  return fd;
}
