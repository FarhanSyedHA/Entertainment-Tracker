import '../style/Navbar.css'
import type { PageTypes } from '../../interface/Types'
import { useAuth } from '../../context/AuthContext';

interface NavbarProps {
  selectedPage: PageTypes;
  onPageChange: (page: PageTypes) => void
}

export const Navbar: React.FC<NavbarProps> = ({selectedPage,onPageChange}) => {

  const auth = useAuth()

  const handleLogout = () => {
    auth?.logout()
  }

  return (
    <div className="NavOptions">
      <div>
        <div className="logo" onClick={() => new Audio("./faaa.mp3").play()}>
          <span>F Tracker</span>
        </div>
        <div>
          <button className={selectedPage === 'home' ? 'active' : ''} onClick={() => onPageChange('home')}>Home</button>
          <button className={selectedPage === 'inprogress' ? 'active' : ''} onClick={() => onPageChange('inprogress')}>In Progress</button>
          <button className={selectedPage === 'watched' ? 'active' : ''} onClick={() => onPageChange('watched')}>Watched</button>
          <button className={selectedPage === 'profile' ? 'active' : ''} onClick={() => onPageChange('profile')}>Profile</button>
        </div>
      </div>
      <div>
        <button onClick={handleLogout}>Logout</button>
      </div>
    </div>
  )
}