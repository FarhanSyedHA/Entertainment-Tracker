import { Filter } from "./Filter"
import '../style/Search.css'
import type { FilterTypes } from "../../interface/Types"

interface SearchProps {
  activeFilter: FilterTypes;
  onFilterChange: (filter: FilterTypes) => void
}

export const Search: React.FC<SearchProps> = ({activeFilter,onFilterChange}) => {
  return (
    <>
    <div className="search-container">
      <input
        type="text"
        placeholder="Search movies, shows, anime..."
        className="search-input"
      />
    <Filter activeFilter={activeFilter} onFilterChange={onFilterChange}/>
    </div>
    </>
  )
}