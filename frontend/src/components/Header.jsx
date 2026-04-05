import { useAuth } from '../context/AuthContext';
import { useTheme } from '../context/ThemeContext';

export default function Header({ onSettings }) {
  const { user, logout } = useAuth();
  const { theme, toggleTheme } = useTheme();

  return (
    <header className="app-header">
      <h1 className="app-title">Entertainment Tracker</h1>
      <div className="header-actions">
        <button className="theme-toggle" onClick={toggleTheme} title="Toggle theme">
          {theme === 'dark' ? '\u2600' : '\u263E'}
        </button>
        {user && (
          <>
            <button className="settings-btn" onClick={onSettings} title="Settings">
              &#9881;
            </button>
            <span className="user-name">{user.display_name}</span>
            <button className="logout-btn" onClick={logout}>Logout</button>
          </>
        )}
      </div>
    </header>
  );
}
