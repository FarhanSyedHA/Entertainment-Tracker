import { useState, useRef, useEffect } from 'react';
import { api } from '../api/client';

export default function SearchBar({ onContentAdded }) {
  const [query, setQuery] = useState('');
  const [results, setResults] = useState([]);
  const [searching, setSearching] = useState(false);
  const [showResults, setShowResults] = useState(false);
  const [adding, setAdding] = useState(null);
  const wrapperRef = useRef(null);
  const debounceRef = useRef(null);

  useEffect(() => {
    const handleClickOutside = (e) => {
      if (wrapperRef.current && !wrapperRef.current.contains(e.target)) {
        setShowResults(false);
      }
    };
    document.addEventListener('mousedown', handleClickOutside);
    return () => document.removeEventListener('mousedown', handleClickOutside);
  }, []);

  const handleSearch = (value) => {
    setQuery(value);

    if (debounceRef.current) clearTimeout(debounceRef.current);

    if (value.trim().length < 2) {
      setResults([]);
      setShowResults(false);
      return;
    }

    debounceRef.current = setTimeout(async () => {
      setSearching(true);
      try {
        const data = await api.search(value.trim());
        setResults(data.results || []);
        setShowResults(true);
      } catch {
        setResults([]);
      } finally {
        setSearching(false);
      }
    }, 400);
  };

  const handleAdd = async (item) => {
    const key = `${item.content_type}-${item.tmdb_id || item.jikan_id}`;
    setAdding(key);
    try {
      const externalId = item.content_type === 'anime' ? item.jikan_id : item.tmdb_id;
      await api.fetchContent(item.content_type, externalId);
      onContentAdded?.();
      setShowResults(false);
      setQuery('');
      setResults([]);
    } catch (err) {
      // content might already be cached, which is fine
      if (err.status !== 409) {
        console.error('Failed to add content', err);
      }
    } finally {
      setAdding(null);
    }
  };

  return (
    <div className="search-bar" ref={wrapperRef}>
      <input
        type="text"
        placeholder="Search movies, TV shows, anime..."
        value={query}
        onChange={(e) => handleSearch(e.target.value)}
        onFocus={() => results.length > 0 && setShowResults(true)}
      />
      {searching && <span className="search-spinner" />}

      {showResults && results.length > 0 && (
        <div className="search-results">
          {results.map((item) => {
            const key = `${item.content_type}-${item.tmdb_id || item.jikan_id}`;
            const year = item.release_date ? new Date(item.release_date).getFullYear() : '';
            return (
              <div key={key} className="search-result-item">
                <div className="search-result-poster">
                  {item.poster_url ? (
                    <img src={item.poster_url} alt={item.title} />
                  ) : (
                    <div className="poster-placeholder-sm">{item.title?.[0]}</div>
                  )}
                </div>
                <div className="search-result-info">
                  <p className="search-result-title">{item.title}</p>
                  <p className="search-result-meta">
                    <span className="badge">{item.content_type}</span>
                    {year && <span>{year}</span>}
                    {item.rating_tmdb && <span>{item.rating_tmdb.toFixed(1)}</span>}
                  </p>
                </div>
                <button
                  className="add-btn"
                  onClick={() => handleAdd(item)}
                  disabled={adding === key}
                >
                  {adding === key ? '...' : '+'}
                </button>
              </div>
            );
          })}
        </div>
      )}
    </div>
  );
}
