import '../style/View.css'
import { Content } from "./Content"
import Profile from "./Profile"
import { WatchedPage } from "./WatchedPage"
import { InProgressPage } from "./InProgressPage"
import { TasksPage } from "./TasksPage"
import type { PageTypes } from '../../interface/Types'

interface ViewProps {
  selectedPage: PageTypes
}

export const View: React.FC<ViewProps> = ({selectedPage}) => {
  return (
    <div className="view">
      {selectedPage === 'profile' && <Profile />}
      {selectedPage === 'watched' && <WatchedPage />}
      {selectedPage === 'inprogress' && <InProgressPage />}
      {selectedPage === 'tasks' && <TasksPage />}
      {selectedPage === 'home' && <Content/>}
    </div>
  )
}