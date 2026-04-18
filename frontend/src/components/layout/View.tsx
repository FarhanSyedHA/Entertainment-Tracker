import '../style/layout/View.css'
import { HomePage } from "../../pages/HomePage"
import ProfilePage from "../../pages/ProfilePage"
import { WatchedPage } from "../../pages/WatchedPage"
import { InProgressPage } from "../../pages/InProgressPage"
import { TasksPage } from "../../pages/TasksPage"
import type { PageTypes } from '../../interface/Types'

interface ViewProps {
  selectedPage: PageTypes
}

export const View: React.FC<ViewProps> = ({selectedPage}) => {
  return (
    <div className="view">
      {selectedPage === 'profile' && <ProfilePage />}
      {selectedPage === 'watched' && <WatchedPage />}
      {selectedPage === 'inprogress' && <InProgressPage />}
      {selectedPage === 'tasks' && <TasksPage />}
      {selectedPage === 'home' && <HomePage/>}
    </div>
  )
}