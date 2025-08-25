import http from "@/lib/http";
// edit while implement apis
const base = "/admin/api/spcs";

export const VendorsApi = {
  list:   (params = {})          => http.get(base, { params }),
  show:   (id)                   => http.get(`${base}/${id}`),
  create: (payload)              => http.post(base, payload),
  update: (id, payload)          => http.put(`${base}/${id}`, payload),
  toggle: (id)                   => http.patch(`${base}/${id}/toggle`),
  remove: (id)                   => http.delete(`${base}/${id}`),
};