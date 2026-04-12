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
}
