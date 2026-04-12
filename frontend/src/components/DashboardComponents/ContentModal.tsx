import { useEffect, useState } from 'react'
import '../style/ContentModal.css'
import type { Show } from '../../interface/Show'
import type { ShowDetails } from '../../interface/ShowDetails'
import { getDetails } from '../../api/api'

interface Props {
  show: Show;
  onClose: () => void;
}

export const ContentModal: React.FC<Props> = ({ show, onClose }) => {
  const [details, setDetails] = useState<ShowDetails | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    setLoading(true);
    getDetails(show.id, show.type)
      .then((res) => setDetails(res))
      .catch((e) => console.error('details fetch failed:', e))
      .finally(() => setLoading(false));
  }, [show.id, show.type])

  useEffect(() => {
    const onKey = (e: KeyboardEvent) => { if (e.key === 'Escape') onClose(); }
    window.addEventListener('keydown', onKey);
    return () => window.removeEventListener('keydown', onKey);
  }, [onClose])

  return (
    <div className="modal-backdrop" onClick={onClose}>
      <div className="modal-card" onClick={(e) => e.stopPropagation()}>
        <button className="modal-close" onClick={onClose}>✕</button>

        {details?.backdrop && (
          <div className="modal-backdrop-img" style={{ backgroundImage: `url(${details.backdrop})` }}/>
        )}

        <div className={details?.backdrop ? 'modal-body' : 'modal-body no-backdrop'}>
          {show.poster && <img className="modal-poster" src={show.poster} alt={show.title}/>}

          <div className="modal-info">
            <h2>{show.title}</h2>
            <div className="modal-meta">
              <span>⭐ {show.rating.toFixed(1)}</span>
              {show.year && <span>{show.year}</span>}
              {details?.runtime && <span>{details.runtime}{typeof details.runtime === 'number' ? ' min' : ''}</span>}
            </div>

            {loading && <p className="modal-loading">Loading…</p>}

            {details?.genres && details.genres.length > 0 && (
              <div className="modal-genres">
                {details.genres.map((g) => <span key={g} className="modal-genre">{g}</span>)}
              </div>
            )}

            {details?.overview && <p className="modal-overview">{details.overview}</p>}
          </div>
        </div>
      </div>
    </div>
  )
}
