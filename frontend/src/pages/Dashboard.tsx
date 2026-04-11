import { useState } from "react";
import { Navbar } from "../components/DashboardComponents/Navbar";
import { View } from "../components/DashboardComponents/View";
import type { PageTypes } from "../interface/Types";
import './style/Dashboard.css'

export default function Dashboard() {
  const [selectedPage, setSelectedPage] = useState<PageTypes>('home')
  return (
    <main className="dashboard">
      <Navbar selectedPage = {selectedPage} onPageChange={setSelectedPage}/>
      <View selectedPage = {selectedPage}/>
    </main>
  )
}