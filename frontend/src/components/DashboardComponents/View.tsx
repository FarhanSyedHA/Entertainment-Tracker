import '../style/View.css'
import { Content } from "./Content"
import Profile from "./Profile"
import type { PageTypes } from '../../interface/Types'

interface ViewProps {
  selectedPage: PageTypes
}

export const View: React.FC<ViewProps> = ({selectedPage}) => {
  
  return (
    <div className="view">
      {selectedPage === 'profile' ? <Profile /> : <Content/>}
    </div>
  )
}