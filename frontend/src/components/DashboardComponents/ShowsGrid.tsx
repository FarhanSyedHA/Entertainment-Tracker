import '../style/ShowsGrid.css'
import type { Show } from '../../interface/Show'

interface ShowsGridProps {
  title: string;
  shows: Show[]
}

export const ShowsGrid: React.FC<ShowsGridProps> = ({ title, shows }) => {
  return (
    <div className="show-section">
      <h2>{title}</h2>
      <div className="show-row">
        {shows?.map((show) => (
          <div className="show-card" key={`${show.type}-${show.id}`}>
            {show.poster
              ? <img src={show.poster} alt={show.title} />
              : <span className="show-card-fallback">{show.title}</span>
            }
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