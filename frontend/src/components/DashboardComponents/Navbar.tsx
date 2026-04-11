import '../style/Navbar.css'
interface NavbarProps {}


export const Navbar: React.FC<NavbarProps> = () => {
  return (
    <div className="NavOptions">
      <div>
        <div className="logo" onClick={() => new Audio("./faaa.mp3").play()}>
          <span>F Tracker</span>
        </div>
        <div>
          <button>Home</button>
          <button>In Progress</button>
          <button>Watched</button>
          <button>Profile</button>
        </div>
      </div>
      <div>
        <button>Logout</button>
      </div>
    </div>
  )
}