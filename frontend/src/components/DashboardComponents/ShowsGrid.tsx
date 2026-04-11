import '../style/ShowsGrid.css'

export interface Show {
  id: number; //id for now, we can decide more appropriate details later.
}

interface ShowsGridProps {
  tile: string;
  shows: Show[]
}

export const ShowsGrid: React.FC<ShowsGridProps> = ({tile, shows}) => {
  
  return (
    <>
      <div className="show-section">
        <h2>{tile}</h2>
        <div className="show-row">
          {shows?.map((show) => (
            <div className="show-card" key={show.id}>
              <h1>{show.id}</h1>
            </div>
          ))}
        </div>
      </div>
    </>
  )
}