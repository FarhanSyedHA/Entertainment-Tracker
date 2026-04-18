import { useEffect, useState } from 'react'
import '../style/shows/ContentModal.css'
import type { Show } from '../../interface/Show'
import type { ShowDetails } from '../../interface/ShowDetails'
import { getDetails, markAsWatched, unmarkWatched } from '../../api/api'
import { useWatched } from '../../context/WatchedContext'
import { useToast } from '../../context/ToastContext'

interface Props {
  show: Show;
  onClose: () => void;
}

export const ContentModal: React.FC<Props> = ({ show, onClose }) => {
  const [details, setDetails] = useState<ShowDetails | null>(null);
  const [loading, setLoading] = useState(true);
  const [pending, setPending] = useState(false);
  const { isWatched, setWatched } = useWatched();
  const { showToast } = useToast();

  const watched = isWatched(show.type, show.id);
  const source: 'tmdb' | 'jikan' = show.type === 'anime' ? 'jikan' : 'tmdb';
  const backendType: 'movie' | 'tv' | 'anime' = show.type === 'tvshows' ? 'tv' : (show.type as 'movie' | 'anime');

  const handleToggleWatched = () => {
    setPending(true);
    const promise = watched
      ? unmarkWatched(source, show.id, backendType)
      : markAsWatched(source, show.id, backendType);
    promise
      .then(() => {
        setWatched(show.type, show.id, !watched);
        showToast(watched ? 'Removed from watched' : 'Marked as watched', 'success');
      })
      .catch((e) => {
        console.error(e);
        showToast(watched ? 'Failed to unmark' : 'Failed to mark as watched', 'error');
      })
      .finally(() => setPending(false));
  }

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

  const hasSeasons = !!details?.seasons?.length;
  const isAnime = show.type === 'anime';

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

            <div className="modal-genres-row">
              <div className="modal-genres">
                {details?.genres?.map((g) => <span key={g} className="modal-genre">{g}</span>)}
              </div>
              <button
                className={`modal-watch-chip ${watched ? 'modal-watch-chip-watched' : ''}`}
                onClick={handleToggleWatched}
                disabled={pending}
                aria-label={watched ? 'Remove from watched' : 'Mark as watched'}
                title={watched ? 'Remove from watched' : 'Mark as watched'}
              >
                <span className="modal-watch-chip-icon">
                  {pending ? '…' : watched ? '✓' : '+'}
                </span>
                <span className="modal-watch-chip-label">
                  {pending ? 'Saving' : watched ? 'Watched' : 'Watch'}
                </span>
              </button>
            </div>

            {details?.overview && <p className="modal-overview">{details.overview}</p>}

            {hasSeasons && (
              <div className="modal-seasons-summary">
                {isAnime
                  ? `${details!.seasons![0].episode_count} ${details!.seasons![0].episode_count === 1 ? 'episode' : 'episodes'}`
                  : `${details!.seasons!.length} ${details!.seasons!.length === 1 ? 'season' : 'seasons'} · ${totalEpisodes(details!)} episodes`
                }
              </div>
            )}
          </div>
        </div>
      </div>
    </div>
  )
}

const totalEpisodes = (d: ShowDetails): number =>
  (d.seasons ?? []).reduce((sum, s) => sum + s.episode_count, 0);
