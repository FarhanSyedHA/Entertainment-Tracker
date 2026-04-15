import { useEffect, useState } from "react"
import { Search } from "./Search"
import { ShowsGrid } from "./ShowsGrid"
import { ContentModal } from "./ContentModal"
import type { Show } from '../../interface/Show'
import type { FilterTypes } from "../../interface/Types"
import { getTrending, search } from "../../api/api"

interface ContentFormat {
  movies: Show[];
  tvshows: Show[];
  anime: Show[];
}

export const Content: React.FC = () => {
  const [showMovies, setShowMovies] = useState<Show[]>([]);
  const [showTvshows, setShowTVshows] = useState<Show[]>([]);
  const [showAnimes, setShowAnime] = useState<Show[]>([]);
  const [activeFilter, setActiveFilter] = useState<FilterTypes>('all');
  const [selectedShow, setSelectedShow] = useState<Show | null>(null);

  const setContent = (res: ContentFormat) => {
      setShowMovies(res.movies);
      setShowTVshows(res.tvshows);
      setShowAnime(res.anime);
  }

  useEffect(() => {
    getTrending().then((res) => {
      setContent(res);
    }).catch((e) => console.error('unable to fetch shows: ', e));
  }, [])

  const handleSearch = (searchQuery: string) => {
    if (searchQuery === '') {
      getTrending().then((res) => {
      setContent(res);
    }).catch((e) => console.error('unable to fetch shows: ', e));
      return;
    }
    search(searchQuery).then((res) => {
      setContent(res);
    }).catch((e) => console.error(e));
  }

  const sections = [
    { filter: 'movies', title: 'Movies', shows: showMovies },
    { filter: 'tvshows', title: 'TV shows', shows: showTvshows },
    { filter: 'anime', title: 'Anime', shows: showAnimes },
  ]

  return (
    <>
      <div className="search">
        <Search activeFilter={activeFilter} onFilterChange={setActiveFilter} onSearch={handleSearch}/>
      </div>
      {
        sections
          .filter(sec => activeFilter === 'all' || activeFilter === sec.filter)
          .filter(sec => sec.shows.length > 0)
          .map(sec => (
            <ShowsGrid
              key={sec.filter}
              title={sec.title}
              shows={sec.shows}
              onSelect={setSelectedShow}
            />
          ))
      }
      {selectedShow && (
        <ContentModal show={selectedShow} onClose={() => setSelectedShow(null)} />
      )}
    </>
  )
}
