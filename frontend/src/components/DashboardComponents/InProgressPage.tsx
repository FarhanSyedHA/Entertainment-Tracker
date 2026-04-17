import { ShowListPage } from './ShowListPage'
import { getInProgressShows } from '../../api/api'

export const InProgressPage: React.FC = () => (
  <ShowListPage
    title="In Progress"
    emptyMessage="Nothing in progress. Start watching via Stremio to see shows here."
    fetcher={getInProgressShows}
  />
)
