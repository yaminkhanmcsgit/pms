import axios from "axios";
import config from "./config";

const api = axios.create({
  baseURL: config.API_URL,
});

// Request interceptor to add token
api.interceptors.request.use(
  config => {
    const token = localStorage.getItem('api_token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  error => Promise.reject(error)
);

// Response interceptor for 401
api.interceptors.response.use(
  response => response,
  error => {
    if (error.response && error.response.status === 401) {
      localStorage.removeItem('api_token');
      window.location.href = '/';
    }
    return Promise.reject(error);
  }
);

export default api;
