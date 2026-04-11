import type { FilterTypes } from '../../interface/Types'
import '../style/Filter.css'

interface FilterProps {
  activeFilter: FilterTypes;
  onFilterChange: (filter: FilterTypes) => void
}

export const Filter: React.FC<FilterProps> = ({activeFilter, onFilterChange}) => {
  return (
    <div className="filter">
    <button onClick={() => onFilterChange('all')}>All</button>
    <button onClick={() => onFilterChange('movies')}>Movies</button>
    <button onClick={() => onFilterChange('tvshows')}>Tv shows</button>
    <button onClick={() => onFilterChange('anime')}>Anime</button>
    </div>
  )
}