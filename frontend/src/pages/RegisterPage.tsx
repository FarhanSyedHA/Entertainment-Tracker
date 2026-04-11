import { useState } from 'react'
import { register } from '../api/api'
import { useAuth } from '../context/AuthContext'
import './style/RegisterPage.css'

export default function RegisterPage({ onSwitch }: { onSwitch: () => void }) {
  const [username, setUsername] = useState('')
  const [email, setEmail] = useState('')
  const [password, setPassword] = useState('')
  const [error, setError] = useState('')
  const auth = useAuth()

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault()
    register(username, email, password)
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
              type="text"
              placeholder="Username"
              value={username}
              onChange={(e) => setUsername(e.target.value)}
            />

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

            <button type="submit" className="auth-btn">Sign up</button>
          </form>

          <p className="auth-footer">
            Already have an account? <span onClick={onSwitch}>Sign in</span>
          </p>
        </div>
      </div>
    </div>
  )
}