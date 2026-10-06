import axios from 'axios';

const api = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL || (window.location.port === '5173' ? 'http://127.0.0.1:8004/api' : '/api'),
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
});

export default api;
