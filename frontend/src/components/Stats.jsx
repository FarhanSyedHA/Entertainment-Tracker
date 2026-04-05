import { useState, useEffect } from 'react';
import { api } from '../api/client';

export default function Stats() {
  const [stats, setStats] = useState(null);

  useEffect(() => {
    api.getStats().then((data) => setStats(data.stats)).catch(() => {});
  }, []);

  if (!stats) return null;

  const total =
    (stats.by_type.movie || 0) + (stats.by_type.tv || 0) + (stats.by_type.anime || 0);

  if (total === 0) return null;

  return (
    <div className="stats-bar">
      <div className="stat-item">
        <span className="stat-value">{total}</span>
        <span className="stat-label">Total</span>
      </div>
      {stats.by_type.movie > 0 && (
        <div className="stat-item">
          <span className="stat-value">{stats.by_type.movie}</span>
          <span className="stat-label">Movies</span>
        </div>
      )}
      {stats.by_type.tv > 0 && (
        <div className="stat-item">
          <span className="stat-value">{stats.by_type.tv}</span>
          <span className="stat-label">TV Shows</span>
        </div>
      )}
      {stats.by_type.anime > 0 && (
        <div className="stat-item">
          <span className="stat-value">{stats.by_type.anime}</span>
          <span className="stat-label">Anime</span>
        </div>
      )}
      {stats.episodes_watched > 0 && (
        <div className="stat-item">
          <span className="stat-value">{stats.episodes_watched}</span>
          <span className="stat-label">Episodes</span>
        </div>
      )}
      {stats.movie_watch_time_hours > 0 && (
        <div className="stat-item">
          <span className="stat-value">{stats.movie_watch_time_hours}h</span>
          <span className="stat-label">Watch Time</span>
        </div>
      )}
    </div>
  );
}
