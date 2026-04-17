import React, { createContext, useCallback, useContext, useState } from 'react'
import { getWatchStatus } from '../api/api'

type FrontendType = 'movie' | 'tvshows' | 'anime'
type BackendType = 'movie' | 'tv' | 'anime'

const toBackendType = (t: string): BackendType => t === 'tvshows' ? 'tv' : (t as BackendType)
const toSource = (t: string): 'tmdb' | 'jikan' => t === 'anime' ? 'jikan' : 'tmdb'
const makeKey = (t: FrontendType, id: number) => `${toSource(t)}:${id}:${toBackendType(t)}`

interface WatchedContextType {
  isWatched: (type: string, id: number) => boolean;
  setWatched: (type: string, id: number, watched: boolean) => void;
  refreshFor: (items: { id: number; type: string }[]) => Promise<void>;
}

const WatchedContext = createContext<WatchedContextType | null>(null)

export function WatchedProvider({ children }: { children: React.ReactNode }) {
  const [watchedMap, setWatchedMap] = useState<Record<string, boolean>>({})

  const isWatched = useCallback((type: string, id: number) => {
    return !!watchedMap[makeKey(type as FrontendType, id)]
  }, [watchedMap])

  const setWatched = useCallback((type: string, id: number, watched: boolean) => {
    const key = makeKey(type as FrontendType, id)
    setWatchedMap(prev => {
      if (!!prev[key] === watched) return prev
      const next = { ...prev }
      if (watched) next[key] = true
      else delete next[key]
      return next
    })
  }, [])

  const refreshFor = useCallback(async (items: { id: number; type: string }[]) => {
    if (!items.length) return
    const payload = items.map(it => ({
      source: toSource(it.type),
      externalId: it.id,
      type: toBackendType(it.type),
    }))
    try {
      const res = await getWatchStatus(payload)
      setWatchedMap(prev => ({ ...prev, ...res.watched }))
    } catch (e) {
      console.error('watch-status fetch failed', e)
    }
  }, [])

  return (
    <WatchedContext.Provider value={{ isWatched, setWatched, refreshFor }}>
      {children}
    </WatchedContext.Provider>
  )
}

export function useWatched() {
  const ctx = useContext(WatchedContext)
  if (!ctx) throw new Error('useWatched must be used within WatchedProvider')
  return ctx
}
