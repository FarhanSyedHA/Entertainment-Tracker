const API_BASE = import.meta.env.VITE_API_URL || 'http://localhost:8000/api';

async function request(method, path, body = null) {
  const token = localStorage.getItem('token');
  const headers = {};

  if (token) {
    headers['Authorization'] = `Bearer ${token}`;
  }

  const options = { method, headers };

  if (body) {
    headers['Content-Type'] = 'application/json';
    options.body = JSON.stringify(body);
  }

  const res = await fetch(`${API_BASE}${path}`, options);
  const data = await res.json();

  if (!res.ok) {
    throw { status: res.status, ...data };
  }

  return data;
}

export const api = {
  // Auth
  register: (body) => request('POST', '/auth/register', body),
  login: (body) => request('POST', '/auth/login', body),
  logout: () => request('POST', '/auth/logout'),
  me: () => request('GET', '/auth/me'),

  // Content
  search: (query, type = 'all') =>
    request('GET', `/content/search?q=${encodeURIComponent(query)}&type=${type}`),
  fetchContent: (contentType, externalId) =>
    request('POST', '/content/fetch', { content_type: contentType, external_id: externalId }),
  getContent: (id) => request('GET', `/content/${id}`),

  // Watch History
  getWatchHistory: (type = 'all') =>
    request('GET', `/watch-history${type !== 'all' ? `?type=${type}` : ''}`),
  addToWatchHistory: (contentId, episodeId = null, status = 'in_progress') =>
    request('POST', '/watch-history', { content_id: contentId, episode_id: episodeId, status }),
  updateWatchHistory: (id, data) =>
    request('PUT', `/watch-history/${id}`, data),
  deleteWatchHistory: (id) =>
    request('DELETE', `/watch-history/${id}`),
  getStats: () => request('GET', '/watch-history/stats'),

  // API Tokens
  getTokens: () => request('GET', '/tokens'),
  createToken: (name) => request('POST', '/tokens', { name }),
  deleteToken: (id) => request('DELETE', `/tokens/${id}`),
};
