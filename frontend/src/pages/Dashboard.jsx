import { useState, useEffect } from 'react';
import { api } from '../api/client';
import PosterCard from '../components/PosterCard';
import SearchBar from '../components/SearchBar';
import ContentModal from '../components/ContentModal';

export default function Dashboard() {
  const [watchHistory, setWatchHistory] = useState([]);
  const [filter, setFilter] = useState('all');
  const [selectedContent, setSelectedContent] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    loadWatchHistory();
  }, []);

  const loadWatchHistory = async () => {
    try {
      const data = await api.getWatchHistory();
      setWatchHistory(data.items || []);
    } catch {
      setWatchHistory([]);
    } finally {
      setLoading(false);
    }
  };

  const filtered = filter === 'all'
    ? watchHistory
    : watchHistory.filter((item) => item.content_type === filter);

  const handleCardClick = async (item) => {
    try {
      const contentId = item.content_id || item.id;
      const data = await api.getContent(contentId);
      // Merge watch history info into content data
      setSelectedContent({
        ...data.content,
        watch_history_id: item.id,
        watch_status: item.status,
        progress_percent: item.progress_percent,
      });
    } catch {
      setSelectedContent(item);
    }
  };

  return (
    <div className="dashboard">
      <div className="dashboard-header">
        <SearchBar onContentAdded={loadWatchHistory} />
        <div className="filter-bar">
          {['all', 'movie', 'tv', 'anime'].map((type) => (
            <button
              key={type}
              className={`filter-btn ${filter === type ? 'active' : ''}`}
              onClick={() => setFilter(type)}
            >
              {type === 'all' ? 'All' : type === 'tv' ? 'TV Shows' : type.charAt(0).toUpperCase() + type.slice(1) + 's'}
            </button>
          ))}
        </div>
      </div>

      {loading ? (
        <div className="loading">Loading...</div>
      ) : filtered.length === 0 ? (
        <div className="empty-state">
          <p>No watch history yet. Search for something to track!</p>
        </div>
      ) : (
        <div className="poster-grid">
          {filtered.map((item) => (
            <PosterCard
              key={item.id}
              item={item}
              onClick={handleCardClick}
            />
          ))}
        </div>
      )}

      {selectedContent && (
        <ContentModal
          content={selectedContent}
          onClose={() => setSelectedContent(null)}
          onWatchHistoryChange={loadWatchHistory}
        />
      )}
    </div>
  );
}
