import {useState} from 'react'
import { useAuth } from './context/AuthContext'
import LoginPage from './pages/LoginPage'
import RegisterPage from './pages/RegisterPage'

function App() {
  const auth = useAuth()
  const [page, setPage] = useState<'login' | 'register'>('login');

  if (!auth?.token) {
    return page === 'login' ?
      <LoginPage onSwitch={() => setPage('register')}/> :
      <RegisterPage onSwitch={() => setPage('login')}/>
  }

  return <div>Entertainment Tracker - Dashboard coming soon</div>
}

export default App
