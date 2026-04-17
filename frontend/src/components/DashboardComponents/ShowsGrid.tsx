import '../style/ShowsGrid.css'
import type { Show } from '../../interface/Show'
import { useWatched } from '../../context/WatchedContext'

interface ShowsGridProps {
  title: string;
  shows: Show[];
  onSelect: (show: Show) => void;
  showWatchedBadge?: boolean;
}

export const ShowsGrid: React.FC<ShowsGridProps> = ({ title, shows, onSelect, showWatchedBadge = true }) => {
  const { isWatched } = useWatched();
  return (
    <div className="show-section">
      <h2>{title}</h2>
      <div className="show-row">
        {shows?.map((show) => (
          <div className="show-card" key={`${show.type}-${show.id}`} onClick={() => onSelect(show)}>
            {show.poster
              ? <img src={show.poster} alt={show.title} />
              : <span className="show-card-fallback">{show.title}</span>
            }
            {showWatchedBadge && isWatched(show.type, show.id) && <span className="show-card-watched-badge">✓ Watched</span>}
            <div className="show-card-info">
              <span className="show-card-title">{show.title}</span>
              <span className="show-card-rating">⭐ {show.rating.toFixed(1)}</span>
            </div>
          </div>
        ))}
      </div>
    </div>
  )
}
