import React, { createContext, useContext, useState } from 'react'

interface AuthContextType {
  token: string | null;
  login: (token: string) => void;
  logout: () => void;
}

const AuthContext = createContext<AuthContextType | null>(null)

export function AuthProvider({children}: {children: React.ReactNode}) {
  const[token, setToken] = useState<string | null>(localStorage.getItem('token'));

  const login = (token: string) => {
    setToken(token);
    localStorage.setItem('token', token);
  }

  const logout = () => {
    setToken(null);
    localStorage.removeItem('token')
  }

  return (
    <AuthContext.Provider value={{ token, login, logout }}>
      {children}
    </AuthContext.Provider>
  ) 
}

export function useAuth() {
  return useContext(AuthContext);
}