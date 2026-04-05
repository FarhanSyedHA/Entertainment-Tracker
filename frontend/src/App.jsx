import { useState } from 'react';
import { useAuth } from './context/AuthContext';
import Header from './components/Header';
import Login from './pages/Login';
import Dashboard from './pages/Dashboard';
import Settings from './pages/Settings';

export default function App() {
  const { user, loading } = useAuth();
  const [page, setPage] = useState('dashboard');

  if (loading) {
    return <div className="loading-screen">Loading...</div>;
  }

  if (!user) {
    return <Login />;
  }

  return (
    <div className="app">
      <Header onSettings={() => setPage('settings')} />
      <main className="app-main">
        {page === 'settings' ? (
          <Settings onBack={() => setPage('dashboard')} />
        ) : (
          <Dashboard />
        )}
      </main>
    </div>
  );
}
