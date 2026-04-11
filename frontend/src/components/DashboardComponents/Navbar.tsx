import '../style/Navbar.css'
import type { PageTypes } from '../../interface/Types'

interface NavbarProps {
  selectedPage: PageTypes;
  onPageChange: (page: PageTypes) => void
}

export const Navbar: React.FC<NavbarProps> = ({selectedPage,onPageChange}) => {
  return (
    <div className="NavOptions">
      <div>
        <div className="logo" onClick={() => new Audio("./faaa.mp3").play()}>
          <span>F Tracker</span>
        </div>
        <div>
          <button onClick={() => onPageChange('home')}>Home</button>
          <button onClick={() => onPageChange('inprogress')}>In Progress</button>
          <button onClick={() => onPageChange('watched')}>Watched</button>
          <button onClick={() => onPageChange('profile')}>Profile</button>
        </div>
      </div>
      <div>
        <button>Logout</button>
      </div>
    </div>
  )
}