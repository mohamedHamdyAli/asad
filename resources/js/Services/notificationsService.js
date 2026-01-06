import axios from "axios"

const NotificationsService = {

  async getUserNotifications() {
    const res = await axios.get("/api/notifications")
    return res.data?.data || []
  },

  async markAsSeen(id) {
    return axios.post("/api/notifications/seen", {
      notification_id: id
    })
  },

  async markAllAsSeen(ids) {
    return Promise.all(ids.map(id => this.markAsSeen(id)))
  },

  async deleteNotification(id) {
    return axios.delete(`/api/notifications/delete/${id}`)
  },
}

export default NotificationsService
