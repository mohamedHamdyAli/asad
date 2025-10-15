import axios from "axios";

/* Helpers */
function unwrapList(payload) {
  if (Array.isArray(payload)) return payload;
  if (Array.isArray(payload?.data)) return payload.data;
  if (Array.isArray(payload?.data?.data)) return payload.data.data;
  return [];
}

/* API */
export const UnitPaymentsApi = {
  list: async (unitId) => {
    const { data } = await axios.get(`/api/unit-payments/${unitId}`);
    return unwrapList(data);
  },
  create: async (formData) => {
    const { data } = await axios.post(`/api/unit-payments/create`, formData, {
      headers: { "Content-Type": "multipart/form-data" },
    });
    return data;
  },
  update: async (id, formData) => {
    const { data } = await axios.post(`/api/unit-payments/update/${id}`, formData, {
      headers: { "Content-Type": "multipart/form-data" },
    });
    return data;
  },
  remove: async (id) => {
    const { data } = await axios.delete(`/api/unit-payments/delete/${id}`);
    return data;
  },
};

/* FormData Builders */
export function buildUnitPaymentCreateFD(unitId, items = []) {
  const fd = new FormData();
  fd.append("unit_id", unitId);
  items.forEach((item, i) => {
    fd.append(`data[${i}][total_price]`, item.total_price ?? 0);
    fd.append(`data[${i}][installments_count]`, item.installments_count ?? 1);
    fd.append(`data[${i}][payment_type]`, item.payment_type ?? "cash");
    fd.append(`data[${i}][overall_status]`, item.overall_status ?? "pending");
  });
  return fd;
}

export function buildUnitPaymentUpdateFD(data = {}) {
  const fd = new FormData();
  if (data.unit_id) fd.append("data[unit_id]", data.unit_id);
  if (data.total_price) fd.append("data[total_price]", data.total_price);
  if (data.installments_count) fd.append("data[installments_count]", data.installments_count);
  if (data.payment_type) fd.append("data[payment_type]", data.payment_type);
  if (data.overall_status) fd.append("data[overall_status]", data.overall_status);
  return fd;
}
