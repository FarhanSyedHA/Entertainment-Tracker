export default function PosterCard({ item, onClick }) {
  const year = item.release_date ? new Date(item.release_date).getFullYear() : '';
  const rating = item.rating_tmdb ? item.rating_tmdb.toFixed(1) : null;
  const hasProgress = item.progress_percent != null && item.progress_percent > 0 && item.progress_percent < 100;
  const statusLabel = item.status === 'completed' ? 'Completed'
    : item.status === 'dropped' ? 'Dropped'
    : null;

  return (
    <div className="poster-card" onClick={() => onClick?.(item)}>
      <div className="poster-image">
        {item.poster_url ? (
          <img src={item.poster_url} alt={item.title} loading="lazy" />
        ) : (
          <div className="poster-placeholder">{item.title?.[0] || '?'}</div>
        )}
        {rating && <span className="poster-rating">{rating}</span>}
        {statusLabel && (
          <span className={`poster-status poster-status--${item.status}`}>{statusLabel}</span>
        )}
        {hasProgress && (
          <div className="poster-progress">
            <div className="poster-progress-bar" style={{ width: `${item.progress_percent}%` }} />
          </div>
        )}
      </div>
      <div className="poster-info">
        <p className="poster-title">{item.title}</p>
        {year && <p className="poster-year">{year}</p>}
      </div>
    </div>
  );
}
