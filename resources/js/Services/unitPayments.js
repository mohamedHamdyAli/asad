import axios from "axios";

/* ---------------- helpers ---------------- */
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

/* ---------------- normalizer ---------------- */
function normalize(row = {}) {
  return {
    id: row.id,
    unit_id: row.unit_id,
    total_price: Number(row.total_price) || 0,
    installments_count: Number(row.installments_count) || 0,
    remaining_installments: Number(row.remaining_installments) || 0,
    payment_type: row.payment_type || "",
    overall_status: row.overall_status || "pending",
    created_at: row.created_at,
    updated_at: row.updated_at,
    installments: Array.isArray(row.installments) ? row.installments : [],
  };
}

/* ---------------- API ---------------- */
export const UnitPaymentsApi = {
  // GET all payments for a unit
  list: async (unitId) => {
    const { data } = await axios.get(`/api/unit-payments/${unitId}`);
    return unwrapList(data).map(normalize);
  },

  // CREATE batch (supports single object too)
  create: async (formData) => {
    const { data } = await axios.post(`/api/unit-payments/create`, formData, {
      headers: { "Content-Type": "multipart/form-data" },
    });
    return data;
  },

  // UPDATE single payment
  update: async (id, formData) => {
    const { data } = await axios.post(`/api/unit-payments/update/${id}`, formData, {
      headers: { "Content-Type": "multipart/form-data" },
    });
    return unwrapOne(data);
  },

  // DELETE single payment
  remove: async (id) => {
    const { data } = await axios.delete(`/api/unit-payments/delete/${id}`);
    return data;
  },
};

/* ---------------- FormData builders ---------------- */

// CREATE
// items = [{ total_price, installments_count, payment_type, overall_status }]
export function buildUnitPaymentCreateFD(unitId, items = []) {
  const fd = new FormData();
  fd.append("unit_id", unitId);
  items.forEach((it, i) => {
    fd.append(`data[${i}][total_price]`, it?.total_price ?? 0);
    fd.append(`data[${i}][installments_count]`, it?.installments_count ?? 1);
    fd.append(`data[${i}][payment_type]`, it?.payment_type ?? "cash");
    if (it?.overall_status)
      fd.append(`data[${i}][overall_status]`, it.overall_status);
  });
  return fd;
}

// UPDATE
// data = { data: { total_price?, installments_count?, payment_type?, overall_status? } }
export function buildUnitPaymentUpdateFD(data = {}) {
  const fd = new FormData();
  const d = data.data ?? {};
  if (d.unit_id !== undefined) fd.append("data[unit_id]", d.unit_id);
  if (d.total_price !== undefined) fd.append("data[total_price]", d.total_price);
  if (d.installments_count !== undefined)
    fd.append("data[installments_count]", d.installments_count);
  if (d.payment_type !== undefined) fd.append("data[payment_type]", d.payment_type);
  if (d.overall_status !== undefined)
    fd.append("data[overall_status]", d.overall_status);
  return fd;
}
