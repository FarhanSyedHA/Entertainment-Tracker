import { Filter } from "./Filter"
import '../style/shows/Search.css'
import type { FilterTypes } from "../../interface/Types"
import { useEffect, useRef, useState } from "react";
import type { Show } from '../../interface/Show'
import { search } from "../../api/api";

interface SearchProps {
  activeFilter: FilterTypes;
  onFilterChange: (filter: FilterTypes) => void;
  onSearch: (searchQuery: string) => void;
}

export const Search: React.FC<SearchProps> = ({ activeFilter, onFilterChange, onSearch }) => {
  const [searchQuery, setSearchQuery] = useState<string>('');
  const [suggestions, setSuggestions] = useState<Show[] | null>(null);
  const skipSearchSuggestions = useRef(false);
  const containerRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    if(!searchQuery.trim()) { setSuggestions(null); return;}
    if(skipSearchSuggestions.current) {skipSearchSuggestions.current = false; return; }
    const timer = setTimeout(() => {
      search(searchQuery).then((res) => {
        const suggestionOptions = [0,1].flatMap(i => [res.movies[i], res.tvshows[i], res.anime[i]]).filter(Boolean).slice(0,10); //show content types in round robin
        setSuggestions(suggestionOptions);
      }).catch(e => console.error(e));
    }, 400);
    return () => clearTimeout(timer);
  }, [searchQuery]);

  useEffect(() => {
    const handler = (e: MouseEvent) => {
      if (containerRef.current && !containerRef.current.contains(e.target as Node)) {
        setSuggestions(null);
      }
    };
    document.addEventListener('mousedown', handler);
    return () => document.removeEventListener('mousedown', handler);
  }, []);


  const handleSelect = (show: Show) => {
    skipSearchSuggestions.current = true;
    setSuggestions(null);
    setSearchQuery(show.title);
    onSearch(show.title);
  }

  return (
    <div className="search-container" ref={containerRef}>
      <input
        type="text"
        placeholder="Search movies, shows, anime..."
        className="search-input"
        value={searchQuery}
        onChange={(e) => setSearchQuery(e.target.value)}
        onKeyDown={(e) => {
          if (e.key === 'Enter') {
            setSuggestions(null);
            onSearch(searchQuery);
          }
          if (e.key === 'Escape') setSuggestions(null);

        }}
      />
      {suggestions && suggestions.length > 0 &&(
        <ul className="search-suggestions">
          {suggestions.map((show) => (
            <li
              key={`${show.type}-${show.id}`}
              className="search-suggestion-item"
              onClick={() => {handleSelect(show)}}
            >
              {show.poster && (<img src={show.poster} alt={show.title} className="suggestion-thumb" />)}
              <span className="suggestion-title">{show.title}</span>
              <span className="suggestion-badge">{show.type}</span>
            </li>
          ))}
        </ul>
      )}
      <Filter activeFilter={activeFilter} onFilterChange={onFilterChange} />
    </div>
  )
}