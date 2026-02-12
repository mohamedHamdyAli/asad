import axios from "axios";

export const UnitInvoicesApi = {
  list: async (installmentId) => {
    const { data } = await axios.get(`/api/unit-payments/installments/${installmentId}/invoices`);
    return Array.isArray(data) ? data : data?.data ?? [];
  },
  updateStatus: async (installmentId, status) => {
    const { data } = await axios.post(`/api/unit-payments/installments/${installmentId}/status`, { status });
    return data;
  },
  confirmInvoice: async (id , status) => {
    const { data } = await axios.post(`/api/unit-payments/invoices/confirm`, { invoice_id: id, action: status });
    return data;
  },
  uploadInvoice: async (installmentId, formData) => {
    const { data } = await axios.post(
      `/api/unit-payments/installments/${installmentId}/upload-invoice`,
      formData,
      { headers: { 'Content-Type': 'multipart/form-data' } }
    );
    return data;
  },
};
