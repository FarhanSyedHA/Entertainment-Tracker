import { useState } from 'react'
import { login } from '../api/api'
import { useAuth } from '../context/AuthContext'
import './style/LoginPage.css'

export default function LoginPage({onSwitch}: {onSwitch: () => void}) {
  const [email, setEmail] = useState('')
  const [password, setPassword] = useState('')
  const [error, setError] = useState('')
  const auth = useAuth()

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault()
    login(email, password)
      .then((res) => auth?.login(res.token))
      .catch((e) => setError(e.message))
  }

  return (
    <div className="auth-page">
      <div className="auth-container">
        <div className="auth-card">
          <h1 className="auth-title">Entertainment<span>Tracker</span></h1>
          <p className="auth-subtitle">Track everything you watch</p>

          <form onSubmit={handleSubmit}>
            <input
              type="email"
              placeholder="Email"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
            />

            <input
              type="password"
              placeholder="Password"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
            />

            {error && <div className="auth-error">{error}</div>}

            <button type="submit" className="auth-btn">Sign in</button>
          </form>

          <p className="auth-footer">
            Don't have an account? <span onClick={onSwitch}>Sign up</span>
          </p>
        </div>
      </div>
    </div>
  )
}