import { useEffect, useState } from "react"
import { Search } from "./Search"
import { ShowsGrid } from "./ShowsGrid"
import type { Show } from '../../interface/Show'
import type { FilterTypes } from "../../interface/Types"
import { getTrending } from "../../api/api"

export const Content: React.FC = () => {
  const[showMovies, setShowMovies] = useState<Show[]>([]);
  const[showTvshows, setShowTVshows] = useState<Show[]>([]);
  const[showAnimes, setShowAnime] = useState<Show[]>([]);
  const[activeFilter, setActiveFilter] = useState<FilterTypes>('all');

  useEffect(() => {
    getTrending().then((res) => {
      setShowMovies(res.movies);
      setShowTVshows(res.tvshows);
      setShowAnime(res.anime);
    }).catch((e) => console.error('unable to fetch shows: ', e));
  },[])

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
        .map(sec => <ShowsGrid key={sec.filter} title={sec.title} shows={sec.shows} />)
      }
     </>
  ) 
}