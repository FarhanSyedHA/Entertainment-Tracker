import { useState } from 'react'
import { register } from '../api/api'
import { useAuth } from '../context/AuthContext'
import './RegisterPage.css'

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
    <section className="hero is-fullheight register-page">
      <div className="hero-body is-justify-content-center">
        <div className="register-container">
          <div className="has-text-centered mb-6">
            <h1 className="title is-2 has-text-white register-title">
              Entertainment<span>Tracker</span>
            </h1>
            <p className="subtitle is-6 register-subtitle">Track everything you watch</p>
          </div>

          <div className="box register-card">
            <h2 className="title is-4 has-text-white mb-5">Create account</h2>

            <form onSubmit={handleSubmit}>
              <div className="field">
                <label className="label">Username</label>
                <div className="control">
                  <input
                    className="input"
                    type="text"
                    placeholder="johndoe"
                    value={username}
                    onChange={(e) => setUsername(e.target.value)}
                  />
                </div>
              </div>

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
                <div className="notification register-error">{error}</div>
              )}

              <div className="field mt-5">
                <button type="submit" className="button is-fullwidth register-btn">
                  Sign up
                </button>
              </div>
            </form>

            <p className="has-text-centered mt-5 register-footer">
              Already have an account? <span onClick={onSwitch}>Sign in</span>
            </p>
          </div>
        </div>
      </div>
    </section>
  )
}
