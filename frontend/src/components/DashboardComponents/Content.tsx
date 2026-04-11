import { useState } from "react"
import { Search } from "./Search"
import { ShowsGrid } from "./ShowsGrid"
import type { Show } from '../../interface/Show'
import type { FilterTypes } from "../../interface/Types"

export const Content: React.FC = () => {
  const[showMovies, setShowMovies] = useState<Show[]>([{ id: 12 }, { id: 33 }]);
  const[showTvshows, setShowTVshows] = useState<Show[]>([{ id: 56 }, { id: 2 }, { id: 35 }]);
  const[showAnimes, setShowAnime] = useState<Show[]>([]);
  const[activeFilter, setActiveFilter] = useState<FilterTypes>('all');

  const sections = [
    { filter: 'movies', title: 'Movies', shows: showMovies},
    { filter: 'tvshows', title: 'TV shows', shows: showTvshows},
    { filter: 'anime', title: 'Anime', shows: showAnimes},
  ]

  return (
     <>
     <div className="search">
      <Search activeFilter={activeFilter} onFilterChange={setActiveFilter}/>
    </div>
      {
        sections
        .filter(sec => activeFilter === 'all' || activeFilter === sec.filter)
        .filter(sec => sec.shows.length > 0)
        .map(sec => <ShowsGrid key={sec.filter} tile={sec.title} shows={sec.shows} />)
      }
     </>
  ) 
}