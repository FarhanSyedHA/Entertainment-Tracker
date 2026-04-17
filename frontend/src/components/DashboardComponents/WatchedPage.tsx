import { ShowListPage } from './ShowListPage'
import { getWatchedShows } from '../../api/api'

export const WatchedPage: React.FC = () => (
  <ShowListPage
    title="Watched"
    emptyMessage="Nothing watched yet. Mark shows as watched from the Home page."
    fetcher={getWatchedShows}
    requireWatchedCheck
    showWatchedBadge={false}
  />
)
