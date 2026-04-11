import { use, useState } from "react"
import { Search } from "./Search"
import { ShowsGrid } from "./ShowsGrid"

export const Content: React.FC = () => {
  const[showMovies, setShowMovies] = useState([{ id: 12 }, { id: 33 }]);
  const[showTvshows, setShowTVshows] = useState([{ id: 56 }, { id: 2 }, { id: 35 }]);
  const[showAnimes, setShowAnime] = useState([]);

  return (
     <>
     <div className="search">
      <Search />
    </div>
      {showMovies && <ShowsGrid tile='Movies' shows={showMovies}/>}
      {showTvshows.length != 0 && <ShowsGrid tile='TV shows' shows={showTvshows}/>}
      {showAnimes.length != 0 && <ShowsGrid tile='Anime' shows={[]}/>}
     </>
  ) 
}