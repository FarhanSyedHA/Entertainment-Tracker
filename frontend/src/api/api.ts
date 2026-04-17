import { callAPI } from './connection'

export const login = (email: string, password: string) => {
  return callAPI('/login', 'POST', {email, password});
}

export const register = (username: string, email: string, password: string) => {
  return callAPI('/register', 'POST', {username, email, password});
}

export const getTrending = () => {
  return callAPI('/get-trending-shows', 'GET');
}

export const getDetails = (id: number, type: string) => {
  return callAPI(`/content-details?id=${id}&type=${type}`, 'GET');
}

export const search = (searchQuery: string) => {
  return callAPI(`/search?searchQuery=${searchQuery}`, 'GET');
}

export const markAsWatched = (source: 'tmdb' | 'jikan', externalId: number, type: 'movie' | 'tv' | 'anime') => {
  return callAPI('/watch', 'POST', { source, externalId, type });
}

export const unmarkWatched = (source: 'tmdb' | 'jikan', externalId: number, type: 'movie' | 'tv' | 'anime') => {
  return callAPI('/unwatch', 'POST', { source, externalId, type });
}

export const getWatchStatus = (items: {source: 'tmdb'|'jikan'; externalId: number; type: 'movie'|'tv'|'anime'}[]) => {
  return callAPI('/watch-status', 'POST', { items });
}

export const getWatchedShows = () => {
  return callAPI('/watched-shows', 'GET');
}

export const getInProgressShows = () => {
  return callAPI('/in-progress-shows', 'GET');
}