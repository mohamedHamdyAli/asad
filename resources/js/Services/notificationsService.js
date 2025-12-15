import axios from "axios"

const NotificationsService = {

  async getUserNotifications() {
    const res = await axios.get("/api/notifications")
    return res.data?.data || []
  },

  /**
   * @param {Number} id
   */
  async markAsSeen(id) {
    return axios.post("/api/notifications/seen", { notification_id: id })
  },

  /**
   * @param {Array} ids
   */
  async markAllAsSeen(ids) {
    return Promise.all(ids.map((id) => this.markAsSeen(id)))
  },

  /**
   * @param {Number} id
   */
  async deleteNotification(id) {
    return axios.delete("/api/notifications/delete", {
      data: { notification_id: id },
    })
  },
}

export default NotificationsService
