export interface Episode {
  episode_number: number;
  title: string | null;
  runtime_seconds: number | null;
  air_date?: string | null;
}

export interface Season {
  season_number: number;
  name: string;
  episode_count: number;
  episodes: Episode[];
}

export interface ShowDetails {
  id: number;
  title: string;
  poster: string | null;
  backdrop: string | null;
  rating: number;
  year: string;
  type: string;
  overview: string;
  genres: string[];
  runtime: number | string | null;
  seasons?: Season[];
  total_episodes?: number | null;
}
