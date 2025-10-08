import axios from "axios";

/* ---------------- helpers ---------------- */
function tryJson(v) {
  try {
    return typeof v === "string" ? JSON.parse(v) : v;
  } catch {
    return v;
  }
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

/* ---------------- normalizer ---------------- */
function normalize(row = {}) {
  const t = tryJson(row.title);
  const d = tryJson(row.description);
  const title = t && typeof t === "object" ? t : { en: t ?? "", ar: t ?? "" };
  const desc = d && typeof d === "object" ? d : { en: d ?? "", ar: d ?? "" };

  return {
    id: row.id,
    unit_payment_id: row.unit_payment_id,
    title_en: title.en ?? "",
    title_ar: title.ar ?? "",
    description_en: desc.en ?? "",
    description_ar: desc.ar ?? "",
    percentage: Number(row.percentage) || null,
    amount: Number(row.amount) || 0,
    status: row.status || "pending",
    milestone_date: row.milestone_date || "",
    submission_date: row.submission_date || "",
    consultant_approval_date: row.consultant_approval_date || "",
    due_date: row.due_date || "",
    payment_date: row.payment_date || "",
    created_at: row.created_at,
    updated_at: row.updated_at,
  };
}

/* ---------------- API ---------------- */
export const UnitInstallmentsApi = {
  // GET installments by Unit Payment ID
  list: async (unitPaymentId) => {
    const { data } = await axios.get(
      `/api/unit-payments/installments/show/${unitPaymentId}`
    );
    return unwrapList(data).map(normalize);
  },

  // CREATE new installment
  create: async (formData) => {
    const { data } = await axios.post(
      `/api/unit-payments/installments/create`,
      formData,
      {
        headers: { "Content-Type": "multipart/form-data" },
      }
    );
    return unwrapOne(data);
  },

  // UPDATE existing installment
  update: async (id, formData) => {
    const { data } = await axios.post(
      `/api/unit-payments/installments/update/${id}`,
      formData,
      {
        headers: { "Content-Type": "multipart/form-data" },
      }
    );
    return unwrapOne(data);
  },

  // DELETE installment
  remove: async (id) => {
    const { data } = await axios.delete(
      `/api/unit-payments/installments/delete/${id}`
    );
    return data;
  },
};

/* ---------------- FormData builders ---------------- */

// CREATE
// buildInstallmentCreateFD(unitPaymentId, data)
export function buildInstallmentCreateFD(unitPaymentId, data = {}) {
  const fd = new FormData();
  fd.append("unit_payment_id", unitPaymentId);

  fd.append("data[title][en]", data?.title?.en ?? "");
  fd.append("data[title][ar]", data?.title?.ar ?? "");
  fd.append("data[description][en]", data?.description?.en ?? "");
  fd.append("data[description][ar]", data?.description?.ar ?? "");

  if (data?.percentage !== undefined)
    fd.append("data[percentage]", data.percentage);
  if (data?.amount !== undefined) fd.append("data[amount]", data.amount);
  if (data?.status) fd.append("data[status]", data.status);

  if (data?.milestone_date)
    fd.append("data[milestone_date]", data.milestone_date);
  if (data?.submission_date)
    fd.append("data[submission_date]", data.submission_date);
  if (data?.consultant_approval_date)
    fd.append("data[consultant_approval_date]", data.consultant_approval_date);
  if (data?.due_date) fd.append("data[due_date]", data.due_date);
  if (data?.payment_date)
    fd.append("data[payment_date]", data.payment_date);

  return fd;
}

// UPDATE
// buildInstallmentUpdateFD(data)
export function buildInstallmentUpdateFD(data = {}) {
  const fd = new FormData();

  if (data?.unit_payment_id)
    fd.append("data[unit_payment_id]", data.unit_payment_id);

  if (data?.title) {
    if (data.title.en !== undefined)
      fd.append("data[title][en]", data.title.en ?? "");
    if (data.title.ar !== undefined)
      fd.append("data[title][ar]", data.title.ar ?? "");
  }

  if (data?.description) {
    if (data.description.en !== undefined)
      fd.append("data[description][en]", data.description.en ?? "");
    if (data.description.ar !== undefined)
      fd.append("data[description][ar]", data.description.ar ?? "");
  }

  if (data?.percentage !== undefined)
    fd.append("data[percentage]", data.percentage);
  if (data?.amount !== undefined) fd.append("data[amount]", data.amount);
  if (data?.status) fd.append("data[status]", data.status);

  if (data?.milestone_date)
    fd.append("data[milestone_date]", data.milestone_date);
  if (data?.submission_date)
    fd.append("data[submission_date]", data.submission_date);
  if (data?.consultant_approval_date)
    fd.append("data[consultant_approval_date]", data.consultant_approval_date);
  if (data?.due_date) fd.append("data[due_date]", data.due_date);
  if (data?.payment_date)
    fd.append("data[payment_date]", data.payment_date);

  return fd;
}
