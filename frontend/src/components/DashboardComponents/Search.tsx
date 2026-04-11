import { Filter } from "./Filter"
import '../style/Search.css'

export const Search: React.FC = () => {
  return (
    <>
    <div className="search-container">
      <input
        type="text"
        placeholder="Search movies, shows, anime..."
        className="search-input"
      />
    <Filter />
    </div>
    </>
  )
}