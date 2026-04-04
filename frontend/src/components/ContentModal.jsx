import { useState } from 'react';
import { api } from '../api/client';

export default function ContentModal({ content, onClose, onWatchHistoryChange }) {
  const [expandedSeason, setExpandedSeason] = useState(null);
  const [loading, setLoading] = useState(false);

  if (!content) return null;

  const year = content.release_date ? new Date(content.release_date).getFullYear() : '';
  const hasSeasons = content.seasons && content.seasons.length > 0;
  const isMovie = content.content_type === 'movie';

  const handleAddToWatched = async (status = 'completed') => {
    setLoading(true);
    try {
      await api.addToWatchHistory(content.id, null, status);
      onWatchHistoryChange?.();
    } catch (err) {
      console.error('Failed to add to watched', err);
    } finally {
      setLoading(false);
    }
  };

  const handleEpisodeWatched = async (episodeId) => {
    try {
      await api.addToWatchHistory(content.id, episodeId, 'completed');
      onWatchHistoryChange?.();
    } catch (err) {
      console.error('Failed to mark episode', err);
    }
  };

  const handleStatusChange = async (status) => {
    if (!content.watch_history_id) return;
    setLoading(true);
    try {
      await api.updateWatchHistory(content.watch_history_id, { status });
      onWatchHistoryChange?.();
    } catch (err) {
      console.error('Failed to update status', err);
    } finally {
      setLoading(false);
    }
  };

  const handleRemove = async () => {
    if (!content.watch_history_id) return;
    setLoading(true);
    try {
      await api.deleteWatchHistory(content.watch_history_id);
      onWatchHistoryChange?.();
      onClose();
    } catch (err) {
      console.error('Failed to remove', err);
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="modal-overlay" onClick={onClose}>
      <div className="modal-content" onClick={(e) => e.stopPropagation()}>
        <button className="modal-close" onClick={onClose}>&times;</button>

        <div className="modal-header">
          {content.backdrop_url && (
            <div
              className="modal-backdrop"
              style={{ backgroundImage: `url(${content.backdrop_url})` }}
            />
          )}
          <div className="modal-header-info">
            {content.poster_url && (
              <img className="modal-poster" src={content.poster_url} alt={content.title} />
            )}
            <div className="modal-meta">
              <h2>{content.title}</h2>
              <div className="modal-tags">
                <span className="badge">{content.content_type}</span>
                {year && <span>{year}</span>}
                {content.runtime_minutes && <span>{content.runtime_minutes} min</span>}
                {content.status && <span>{content.status}</span>}
              </div>
              <div className="modal-ratings">
                {content.rating_tmdb && <span>TMDB: {content.rating_tmdb}</span>}
                {content.rating_imdb && <span>IMDb: {content.rating_imdb}</span>}
                {content.rating_rt && <span>RT: {content.rating_rt}</span>}
                {content.rating_metacritic && <span>MC: {content.rating_metacritic}</span>}
              </div>
              {content.genres && (
                <div className="modal-genres">
                  {content.genres.map((g) => (
                    <span key={g} className="genre-tag">{g}</span>
                  ))}
                </div>
              )}

              <div className="modal-actions">
                {content.watch_history_id ? (
                  <>
                    <div className="status-buttons">
                      {['in_progress', 'completed', 'dropped'].map((s) => (
                        <button
                          key={s}
                          className={`status-btn ${content.watch_status === s ? 'active' : ''}`}
                          onClick={() => handleStatusChange(s)}
                          disabled={loading}
                        >
                          {s === 'in_progress' ? 'Watching' : s === 'completed' ? 'Completed' : 'Dropped'}
                        </button>
                      ))}
                    </div>
                    <button className="remove-btn" onClick={handleRemove} disabled={loading}>
                      Remove
                    </button>
                  </>
                ) : (
                  <div className="add-actions">
                    {isMovie ? (
                      <>
                        <button className="action-btn primary" onClick={() => handleAddToWatched('completed')} disabled={loading}>
                          Mark as Watched
                        </button>
                        <button className="action-btn" onClick={() => handleAddToWatched('in_progress')} disabled={loading}>
                          Watching
                        </button>
                      </>
                    ) : (
                      <button className="action-btn primary" onClick={() => handleAddToWatched('in_progress')} disabled={loading}>
                        Start Watching
                      </button>
                    )}
                  </div>
                )}
              </div>
            </div>
          </div>
        </div>

        {content.overview && (
          <div className="modal-overview">
            <p>{content.overview}</p>
          </div>
        )}

        {hasSeasons && (
          <div className="modal-seasons">
            <h3>Seasons</h3>
            {content.seasons.map((season) => (
              <div key={season.id} className="season-block">
                <div
                  className="season-header"
                  onClick={() =>
                    setExpandedSeason(expandedSeason === season.id ? null : season.id)
                  }
                >
                  <span>{season.name || `Season ${season.season_number}`}</span>
                  <span className="episode-count">
                    {season.episode_count || season.episodes?.length || 0} episodes
                  </span>
                  <span className="expand-icon">
                    {expandedSeason === season.id ? '\u25B2' : '\u25BC'}
                  </span>
                </div>

                {expandedSeason === season.id && season.episodes && (
                  <div className="episode-grid">
                    {season.episodes.map((ep) => (
                      <div key={ep.id} className="episode-item">
                        <span className="ep-number">E{ep.episode_number}</span>
                        <span className="ep-name">{ep.name || `Episode ${ep.episode_number}`}</span>
                        {ep.air_date && <span className="ep-date">{ep.air_date}</span>}
                        <button
                          className="ep-watch-btn"
                          onClick={() => handleEpisodeWatched(ep.id)}
                          title="Mark as watched"
                        >
                          &#10003;
                        </button>
                      </div>
                    ))}
                  </div>
                )}
              </div>
            ))}
          </div>
        )}
      </div>
    </div>
  );
}
