import { Navbar } from "../components/DashboardComponents/Navbar";
import { View } from "../components/DashboardComponents/View";
import './style/Dashboard.css'

export default function Dashboard() {
  return (
    <main className="dashboard">
      <Navbar />
      <View />
    </main>
  )
}