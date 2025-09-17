import axios from "axios";

function tryJson(v) {
  try { return typeof v === "string" ? JSON.parse(v) : v }
  catch { return v }
}

function normalize(row = {}) {
  const name = tryJson(row.name)
  const n = (name && typeof name === "object") ? name : { en: name ?? "", ar: name ?? "" }
  return {
    id: row.id,
    file_type: row.file_type,
    name_en: n.en ?? "",
    name_ar: n.ar ?? "",
    label: n.en || n.ar || `Folder #${row.id}`,
    folder_image: row.folder_image ? `/storage/${row.folder_image}` : null,
    unit_id: row.unit_id ?? null,
  }
}

function unwrapList(payload) {
  if (Array.isArray(payload)) return payload
  if (Array.isArray(payload?.data)) return payload.data
  if (Array.isArray(payload?.message)) return payload.message
  if (Array.isArray(payload?.data?.data)) return payload.data.data
  return []
}

export const FolderApi = {
  list: async (type, unitId = null) => {
    const { data } = await axios.get(`/api/folder/${type}`, {
      params: unitId ? { unit_id: unitId } : {},
    })
    return unwrapList(data).map(normalize)
  },

  create: async (formData) => {
    const { data } = await axios.post(`/api/folder/create`, formData, {
      headers: { "Content-Type": "multipart/form-data" },
    })
    const row = data?.data ?? data
    return normalize(row)
  },

  update: async (id, formData) => {
    const { data } = await axios.post(`/api/folder/update/${id}`, formData, {
      headers: { "Content-Type": "multipart/form-data" },
    })
    return normalize(data?.data ?? data)
  },

  remove: async (id) => {
    const { data } = await axios.delete(`/api/folder/delete/${id}`)
    return data
  },
}

export function buildFolderFD({ file_type, name = { en: "", ar: "" }, folder_image = null, unit_id = null }) {
  const fd = new FormData()
  fd.append("file_type", file_type)
  fd.append("name[en]", name.en ?? "")
  fd.append("name[ar]", name.ar ?? "")
  if (unit_id) fd.append("unit_id", unit_id) 
  if (folder_image) fd.append("folder_image", folder_image)
  return fd
}
