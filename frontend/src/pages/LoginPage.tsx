import { useState } from 'react'
import { login } from '../api/api'
import { useAuth } from '../context/AuthContext'
import './LoginPage.css'

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
    <section className="hero is-fullheight login-page">
      <div className="hero-body is-justify-content-center">
        <div className="login-container">
          <div className="has-text-centered mb-6">
            <h1 className="title is-2 has-text-white login-title">
              Entertainment<span>Tracker</span>
            </h1>
            <p className="subtitle is-6 login-subtitle">Track everything you watch</p>
          </div>

          <div className="box login-card">
            <h2 className="title is-4 has-text-white mb-5">Sign in</h2>

            <form onSubmit={handleSubmit}>
              <div className="field">
                <label className="label">Email</label>
                <div className="control">
                  <input
                    className="input"
                    type="email"
                    placeholder="you@example.com"
                    value={email}
                    onChange={(e) => setEmail(e.target.value)}
                  />
                </div>
              </div>

              <div className="field">
                <label className="label">Password</label>
                <div className="control">
                  <input
                    className="input"
                    type="password"
                    placeholder="••••••••"
                    value={password}
                    onChange={(e) => setPassword(e.target.value)}
                  />
                </div>
              </div>

              {error && (
                <div className="notification login-error">{error}</div>
              )}

              <div className="field mt-5">
                <button type="submit" className="button is-fullwidth login-btn">
                  Sign in
                </button>
              </div>
            </form>

            <p className="has-text-centered mt-5 login-footer">
              Don't have an account? <span onClick={onSwitch}>Sign up</span>
            </p>
          </div>
        </div>
      </div>
    </section>
  )
}
