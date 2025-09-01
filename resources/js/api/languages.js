import http from '@/lib/http'

export const languagesApi = {
  list:   (params={})                     => http.get('/api/language/list', { params }),
  create: (formData)                      => http.post('api/language/create', formData),
  show:   (id)                            => http.get(`api/language/${id}`),
  update: (id, payload)                   => http.post(`api/language/${id}`, payload),
  remove: (id)                            => http.delete(`api/language/delete/${id}`),
  setUI:  (code)                          => http.post(`api/set-language/${code}`),

  getFile: (id, type)                     => http.get(`api/language/languageedit/${id}/${type}`),
  saveFile:(id, type, values)             => http.post(`api/language/updatelanguageValues/${id}/${type}`, { values }),
};