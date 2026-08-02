import { useEffect, useState } from 'react'
import '../style/layout/Navbar.css'
import type { PageTypes } from '../../interface/Types'
import { useAuth } from '../../context/AuthContext';

interface NavbarProps {
  selectedPage: PageTypes;
  onPageChange: (page: PageTypes) => void
}

interface NavItem {
  id: PageTypes;
  label: string;
}

const NAV_ITEMS: NavItem[] = [
  { id: 'home',       label: 'Home' },
  { id: 'inprogress', label: 'In Progress' },
  { id: 'watched',    label: 'Watched' },
  { id: 'profile',    label: 'Profile' },
]

export const Navbar: React.FC<NavbarProps> = ({ selectedPage, onPageChange }) => {
  const auth = useAuth()
  const [open, setOpen] = useState(false)

  useEffect(() => {
    const onKey = (e: KeyboardEvent) => { if (e.key === 'Escape') setOpen(false) }
    window.addEventListener('keydown', onKey)
    return () => window.removeEventListener('keydown', onKey)
  }, [])

  const handleNav = (id: PageTypes) => {
    onPageChange(id)
    setOpen(false)
  }

  return (
    <>
      <header className="topbar">
        <button
          className="hamburger"
          aria-label="Open navigation"
          aria-expanded={open}
          onClick={() => setOpen(true)}
        >
          <span /><span /><span />
        </button>
        <div className="topbar-logo" onClick={() => {}}>FH Tracker</div>
      </header>

      {open && <div className="nav-scrim" onClick={() => setOpen(false)} />}

      <aside className={`sidebar ${open ? 'sidebar-open' : ''}`} aria-label="Main navigation">
        <div className="sidebar-header">
          <div className="sidebar-logo" onClick={() => {}}>
            <span className="sidebar-logo-mark">FH</span>
            <span className="sidebar-logo-text">Tracker</span>
          </div>
          <button className="sidebar-close" aria-label="Close navigation" onClick={() => setOpen(false)}>✕</button>
        </div>

        <nav className="sidebar-nav">
          {NAV_ITEMS.map(item => (
            <button
              key={item.id}
              className={`sidebar-item ${selectedPage === item.id ? 'sidebar-item-active' : ''}`}
              onClick={() => handleNav(item.id)}
            >
              <span className="sidebar-item-label">{item.label}</span>
            </button>
          ))}

          {auth?.me?.is_admin && (
            <button
              className={`sidebar-admin ${selectedPage === 'tasks' ? 'sidebar-admin-active' : ''}`}
              onClick={() => handleNav('tasks')}
            >
              <span className="sidebar-item-label">Admin Privillages</span>
            </button>
          )}
        </nav>

        <div className="sidebar-footer">
          <button className="sidebar-logout" onClick={() => auth?.logout()}>
            <span className="sidebar-item-label">Logout</span>
          </button>
        </div>
      </aside>
    </>
  )
}
