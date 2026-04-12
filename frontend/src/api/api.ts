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