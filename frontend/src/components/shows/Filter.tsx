import type { FilterTypes } from '../../interface/Types'
import '../style/shows/Filter.css'

interface FilterProps {
  activeFilter: FilterTypes;
  onFilterChange: (filter: FilterTypes) => void
}

export const Filter: React.FC<FilterProps> = ({activeFilter, onFilterChange}) => {
  return (
    <div className="filter">
    <button className={activeFilter === 'all' ? 'active' : ''} onClick={() => onFilterChange('all')}>All</button>
    <button className={activeFilter === 'movies' ? 'active' : ''} onClick={() => onFilterChange('movies')}>Movies</button>
    <button className={activeFilter === 'tvshows' ? 'active' : ''} onClick={() => onFilterChange('tvshows')}>Tv shows</button>
    <button className={activeFilter === 'anime' ? 'active' : ''} onClick={() => onFilterChange('anime')}>Anime</button>
    </div>
  )
}