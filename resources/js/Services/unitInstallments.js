import axios from "axios";

/* Helpers */
function unwrapList(payload) {
  if (Array.isArray(payload)) return payload;
  if (Array.isArray(payload?.data)) return payload.data;
  if (Array.isArray(payload?.data?.data)) return payload.data.data;
  return [];
}

/* API */
export const UnitInstallmentsApi = {
  list: async (unitPaymentId) => {
    const { data } = await axios.get(`/api/unit-payments/installments/show/${unitPaymentId}`);
    return unwrapList(data);
  },
  create: async (formData) => {
    const { data } = await axios.post(`/api/unit-payments/installments/create`, formData, {
      headers: { "Content-Type": "multipart/form-data" },
    });
    return data;
  },
  update: async (id, formData) => {
    const { data } = await axios.post(`/api/unit-payments/installments/update/${id}`, formData, {
      headers: { "Content-Type": "multipart/form-data" },
    });
    return data;
  },
  remove: async (id) => {
    const { data } = await axios.delete(`/api/unit-payments/installments/delete/${id}`);
    return data;
  },
};

/* FormData Builders */
export function buildInstallmentCreateFD(unit_payment_id, data = {}) {
  const fd = new FormData();
  fd.append("unit_payment_id", unit_payment_id);
  fd.append("data[title][en]", data.title?.en ?? "");
  fd.append("data[title][ar]", data.title?.ar ?? "");
  fd.append("data[description][en]", data.description?.en ?? "");
  fd.append("data[description][ar]", data.description?.ar ?? "");
  if (data.percentage) fd.append("data[percentage]", data.percentage);
  if (data.amount) fd.append("data[amount]", data.amount);
  if (data.status) fd.append("data[status]", data.status);
  if (data.milestone_date) fd.append("data[milestone_date]", data.milestone_date);
  if (data.submission_date) fd.append("data[submission_date]", data.submission_date);
  if (data.consultant_approval_date) fd.append("data[consultant_approval_date]", data.consultant_approval_date);
  if (data.due_date) fd.append("data[due_date]", data.due_date);
  if (data.payment_date) fd.append("data[payment_date]", data.payment_date);
  return fd;
}

export function buildInstallmentUpdateFD(data = {}) {
  const fd = new FormData();
  if (data.unit_payment_id) fd.append("data[unit_payment_id]", data.unit_payment_id);
  fd.append("data[title][en]", data.title?.en ?? "");
  fd.append("data[title][ar]", data.title?.ar ?? "");
  fd.append("data[description][en]", data.description?.en ?? "");
  fd.append("data[description][ar]", data.description?.ar ?? "");
  if (data.percentage) fd.append("data[percentage]", data.percentage);
  if (data.amount) fd.append("data[amount]", data.amount);
  if (data.status) fd.append("data[status]", data.status);
  if (data.milestone_date) fd.append("data[milestone_date]", data.milestone_date);
  if (data.submission_date) fd.append("data[submission_date]", data.submission_date);
  if (data.consultant_approval_date) fd.append("data[consultant_approval_date]", data.consultant_approval_date);
  if (data.due_date) fd.append("data[due_date]", data.due_date);
  if (data.payment_date) fd.append("data[payment_date]", data.payment_date);
  return fd;
}
