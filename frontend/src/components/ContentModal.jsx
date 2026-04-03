import { useState } from 'react';

export default function ContentModal({ content, onClose }) {
  const [expandedSeason, setExpandedSeason] = useState(null);

  if (!content) return null;

  const year = content.release_date ? new Date(content.release_date).getFullYear() : '';
  const hasSeasons = content.seasons && content.seasons.length > 0;

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
