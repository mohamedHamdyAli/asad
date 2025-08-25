import axios from "axios";

const http = axios.create({
  baseURL: "/",
  withCredentials: true,
});

http.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
http.defaults.xsrfCookieName = "XSRF-TOKEN";
http.defaults.xsrfHeaderName = "X-XSRF-TOKEN";

export default http;
