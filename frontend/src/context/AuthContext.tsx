import React, { createContext, useContext, useEffect, useState } from 'react'
import { getMe } from '../api/api'

interface Me {
  id: number;
  username: string;
  email: string;
  is_admin: boolean;
}

interface AuthContextType {
  token: string | null;
  me: Me | null;
  login: (token: string) => void;
  logout: () => void;
}

const AuthContext = createContext<AuthContextType | null>(null)

export function AuthProvider({children}: {children: React.ReactNode}) {
  const[token, setToken] = useState<string | null>(localStorage.getItem('token'));
  const [me, setMe] = useState<Me | null>(null)

  useEffect(() => {
    if (!token) { setMe(null); return }
    getMe().then(setMe).catch(() => setMe(null))
  }, [token])

  const login = (token: string) => {
    setToken(token);
    localStorage.setItem('token', token);
  }

  const logout = () => {
    setToken(null);
    setMe(null);
    localStorage.removeItem('token')
  }

  return (
    <AuthContext.Provider value={{ token, me, login, logout }}>
      {children}
    </AuthContext.Provider>
  )
}

export function useAuth() {
  return useContext(AuthContext);
}