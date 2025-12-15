import axios from "axios";

const STATUSES = [
  { value: "site_handover", label: "Site Handover" },
  { value: "fondation",     label: "Skeleton" },
  { value: "finishing",     label: "Finishing" },
  { value: "handover",      label: "Handover" },
];

function tryJson(v){ try { return typeof v === 'string' ? JSON.parse(v) : v; } catch { return v; } }
function unwrapList(p){
  if (Array.isArray(p)) return p;
  if (Array.isArray(p?.data)) return p.data;
  if (Array.isArray(p?.message)) return p.message;
  if (Array.isArray(p?.data?.data)) return p.data.data;
  return [];
}
function normalize(row = {}) {
  const d = tryJson(row.description);
  const desc = d && typeof d === 'object' ? d : { en: d ?? "", ar: d ?? "" };
  return {
    id: row.id,
    unit_id: row.unit_id,
    status: row.status,
    status_label: STATUSES.find(s => s.value === row.status)?.label || row.status,
    desc_en: desc.en ?? "",
    desc_ar: desc.ar ?? "",
    created_at: row.created_at,
    updated_at: row.updated_at,
  };
}

export const UnitPhasesApi = {
  statuses: () => STATUSES,

  list: async (unitId) => {
    const { data } = await axios.get(`/api/unit-phase/${unitId}`);
    return unwrapList(data).map(normalize);
  },

  // JSON payloads (no files)
  create: async (payload /* { unit_id, data:[{status, description:{en,ar}}] } */) => {
    const { data } = await axios.post(`/api/unit-phase/create`, payload);
    return data;
  },

  update: async (payload /* { unit_id, data:[{id, status?, description?}] } */) => {
    const { data } = await axios.post(`/api/unit-phase/update`, payload);
    return data;
  },

  remove: async (id) => {
    const { data } = await axios.delete(`/api/unit-phase/delete/${id}`);
    return data;
  },
};

// builders
export function buildCreatePayload(unit_id, items = []) {
  return {
    unit_id,
    data: items.map(it => ({
      status: it.status,
      description: { en: it.description?.en ?? "", ar: it.description?.ar ?? "" },
    })),
  };
}
export function buildUpdatePayload(unit_id, items = []) {
  return {
    unit_id,
    data: items.map(it => {
      const x = { id: it.id };
      if (it.status !== undefined) x.status = it.status;
      if (it.description) x.description = { en: it.description?.en ?? "", ar: it.description?.ar ?? "" };
      return x;
    }),
  };
}
