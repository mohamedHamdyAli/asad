import axios from "axios";

function unwrapList(payload) {
  if (Array.isArray(payload)) return payload;
  if (Array.isArray(payload?.message)) return payload.message;
  if (Array.isArray(payload?.data)) return payload.data;
  if (Array.isArray(payload?.data?.data)) return payload.data.data;
  return [];
}
function unwrapOne(payload) {
  return payload?.data ?? payload?.message ?? payload;
}
function normalize(u = {}) {
  return {
    id: u.id,
    name: u.name ?? "",
    email: u.email ?? "",
    phone: u.phone ?? "",
    country_code: u.country_code ?? "",
    country_name: u.country_name ?? "",
    gender: u.gender ?? "",
    role: u.role ?? "",
    is_enabled: Number(u.is_enabled) === 1 || u.is_enabled === true,
    profile_image: u.profile_image ?? null,
    profile_image_url: u.profile_image ? `/storage/${u.profile_image}` : "",
    created_at: u.created_at,
    updated_at: u.updated_at,
  };
}

export const VendorsApi = {
  list: async (params = {}) => {
    const { data } = await axios.get("/api/vendors", { params });
    return unwrapList(data).map(normalize);
  },
  show: async (id) => {
    const { data } = await axios.get(`/api/vendors/show/${id}`);
    return normalize(unwrapOne(data));
  },
  edit: async (id) => {
    const { data } = await axios.get(`/api/vendors/edit/${id}`);
    return normalize(unwrapOne(data));
  },
  create: async (fd) => {
    const { data } = await axios.post("/api/vendors/create", fd, {
      headers: { "Content-Type": "multipart/form-data" },
    });
    return data;
  },
  update: async (id, fd) => {
    const { data } = await axios.post(`/api/vendors/update/${id}`, fd, {
      headers: { "Content-Type": "multipart/form-data" },
    });
    return data;
  },
  remove: async (id) => {
    const { data } = await axios.delete(`/api/vendors/delete/${id}`);
    return data;
  },
};


export function buildVendorCreateFD({
  name,
  email,
  phone,
  password,
  country_code,
  country_name,
  gender,
  is_enabled = true,
  profile_image,
}) {
  const fd = new FormData();
  fd.append("name", name ?? "");
  fd.append("email", email ?? "");
  fd.append("phone", phone ?? "");
  fd.append("password", password ?? "");
  fd.append("country_code", country_code ?? "");
  fd.append("country_name", country_name ?? "");
  fd.append("gender", gender ?? "");
  fd.append("is_enabled", (is_enabled ? 1 : 0).toString());
  fd.append("role", "vendor");
  if (profile_image) fd.append("profile_image", profile_image);
  return fd;
}
export function buildVendorUpdateFD(partial = {}) {
  const fd = new FormData();
  for (const [k, v] of Object.entries(partial)) {
    if (v === undefined || v === null) continue;
    if (k === "profile_image" && v) {
      fd.append("profile_image", v);
    } else if (k === "is_enabled") {
      fd.append("is_enabled", (v ? 1 : 0).toString());
    } else if (typeof v === "string") {
      const trimmed = v.trim();
      if (trimmed !== "") fd.append(k, trimmed);
    } else {
      fd.append(k, v);
    }
  }
  return fd;
}
