import axios from "axios";

/* ---------------- helpers ---------------- */
function unwrapList(payload) {
  if (Array.isArray(payload)) return payload;
  if (Array.isArray(payload?.data)) return payload.data;
  if (Array.isArray(payload?.message)) return payload.message;
  if (Array.isArray(payload?.data?.data)) return payload.data.data;
  return [];
}

function normalize(row = {}) {
  return {
    id: row.id,
    user_id: row.user_id,
    user_name: row.user?.name || "",
    user_email: row.user?.email || "",
    type: row.type || "all",
    title: row.title || "",
    body: row.body || "",
    is_seen: row.is_seen || false,
    seen_time: row.seen_time,
    created_at: row.created_at,
    updated_at: row.updated_at,
  };
}

/* ---------------- API ---------------- */
export const NotificationsApi = {
  list: async () => {
    const { data } = await axios.get(`/api/notifications`);
    return unwrapList(data).map(normalize);
  },

  show: async (userId) => {
    const { data } = await axios.get(`/api/notifications/show/${userId}`);
    return unwrapList(data).map(normalize);
  },

  create: async (payload) => {
    const { data } = await axios.post(`/api/notifications/create`, payload);
    return data;
  },

  remove: async (id) => {
    const { data } = await axios.delete(`/api/notifications/delete/${id}`);
    return data;
  },
};

/* ---------------- Payload builder ---------------- */
export function buildNotificationPayload(notification = {}) {
  const payload = {
    title: notification.title,
    body: notification.body,
  };

  // If user_ids array is provided, send to specific users
  if (notification.user_ids && Array.isArray(notification.user_ids) && notification.user_ids.length > 0) {
    payload.user_ids = notification.user_ids;
  }
  // Legacy support: if user_id is provided, send to specific user
  else if (notification.user_id) {
    payload.user_id = notification.user_id;
  }

  return payload;
}
