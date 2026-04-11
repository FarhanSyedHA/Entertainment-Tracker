import { useState } from "react"
import '../style/View.css'
import { Content } from "./Content"
import Profile from "./Profile"

export const View: React.FC = () => {
  const [selectedPage, setSelectedPage] = useState<'home' | 'watched' | 'profile'> ('home')
  return (
    <div className="view">
      {selectedPage === 'profile' ? <Profile /> : <Content/>}
    </div>
  )
}