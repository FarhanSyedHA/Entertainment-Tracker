import { useEffect, useState } from 'react'
import { ShowsGrid } from './ShowsGrid'
import { ContentModal } from './ContentModal'
import type { Show } from '../../interface/Show'
import type { FilterTypes } from '../../interface/Types'
import { useWatched } from '../../context/WatchedContext'
import { Filter } from './Filter'
import '../style/Search.css'

interface Props {
  title: string;
  emptyMessage: string;
  fetcher: () => Promise<{ shows: Show[] }>;
  requireWatchedCheck?: boolean;
  showWatchedBadge?: boolean;
}

export const ShowListPage: React.FC<Props> = ({
  title,
  emptyMessage,
  fetcher,
  requireWatchedCheck = false,
  showWatchedBadge = true,
}) => {
  const [shows, setShows] = useState<Show[]>([])
  const [loading, setLoading] = useState(true)
  const [selectedShow, setSelectedShow] = useState<Show | null>(null)
  const [activeFilter, setActiveFilter] = useState<FilterTypes>('all')
  const { refreshFor, isWatched } = useWatched()

  useEffect(() => {
    setLoading(true)
    fetcher()
      .then((res) => {
        setShows(res.shows)
        refreshFor(res.shows)
      })
      .catch((e) => console.error(`failed to load ${title}`, e))
      .finally(() => setLoading(false))
  }, [fetcher, refreshFor, title])

  const visible = shows.filter(s => {
    if (requireWatchedCheck && !isWatched(s.type, s.id)) return false
    if (activeFilter === 'all') return true
    return activeFilter === s.type || (activeFilter === 'movies' && s.type === 'movie')
  })

  const sections = [
    { filter: 'movies', title: 'Movies', shows: visible.filter(s => s.type === 'movie') },
    { filter: 'tvshows', title: 'TV shows', shows: visible.filter(s => s.type === 'tvshows') },
    { filter: 'anime', title: 'Anime', shows: visible.filter(s => s.type === 'anime') },
  ]

  return (
    <>
      <div className="search">
        <div className="search-container">
          <h1 style={{ color: '#fff', margin: 0 }}>{title}</h1>
          <Filter activeFilter={activeFilter} onFilterChange={setActiveFilter} />
        </div>
      </div>

      {loading && <p style={{ color: '#888', padding: 16 }}>Loading…</p>}

      {!loading && visible.length === 0 && (
        <p style={{ color: '#888', padding: 16 }}>{emptyMessage}</p>
      )}

      {sections
        .filter(sec => sec.shows.length > 0)
        .map(sec => (
          <ShowsGrid
            key={sec.filter}
            title={sec.title}
            shows={sec.shows}
            onSelect={setSelectedShow}
            showWatchedBadge={showWatchedBadge}
          />
        ))}

      {selectedShow && (
        <ContentModal show={selectedShow} onClose={() => setSelectedShow(null)} />
      )}
    </>
  )
}
