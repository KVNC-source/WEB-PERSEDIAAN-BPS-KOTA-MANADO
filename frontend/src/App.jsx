import React, { useState, useEffect } from "react";
import axios from "axios";
import { motion, AnimatePresence } from "framer-motion";
import {
  LayoutGrid,
  ArrowDownRight,
  ArrowUpRight,
  Layers,
  Search,
  Bell,
  PlusCircle,
  Box,
  ArrowDown,
  ArrowUp,
} from "lucide-react";

import InventoryTable from "./components/InventoryTable";
import RequestForm from "./pages/RequestForm";

const App = () => {
  const [activeTab, setActiveTab] = useState("dashboard");
  const [data, setData] = useState([]);
  const [stats, setStats] = useState({ masuk: 0, keluar: 0, total: 0 });
  const [isLoading, setIsLoading] = useState(true);

  useEffect(() => {
    const fetchData = async () => {
      setIsLoading(true);
      try {
        if (activeTab === "dashboard") {
          const res = await axios.get(
            "http://localhost:5000/api/dashboard/stats"
          );
          setStats(res.data);
        } else if (activeTab !== "upload") {
          const endpoint = activeTab === "stok" ? "stok" : activeTab;
          const res = await axios.get(`http://localhost:5000/api/${endpoint}`);
          setData(res.data);
        }
      } catch (error) {
        console.error("Connection failed", error);
      } finally {
        setTimeout(() => setIsLoading(false), 600);
      }
    };
    fetchData();
  }, [activeTab]);

  return (
    <div className="flex min-h-screen">
      {/* 1. Futuristic Sidebar */}
      <nav className="w-20 lg:w-64 glass-panel border-r border-white/5 p-6 flex flex-col gap-8 sticky top-0 h-screen z-50">
        <div className="flex items-center gap-3 px-2">
          <div className="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/30">
            <Box className="text-white" size={24} />
          </div>
          <span className="font-bold text-xl hidden lg:block tracking-tighter italic">
            SIPA <span className="text-blue-500">2.0</span>
          </span>
        </div>

        <div className="flex flex-col gap-2 flex-1">
          <NavLink
            icon={<LayoutGrid size={20} />}
            label="Dashboard"
            active={activeTab === "dashboard"}
            onClick={() => setActiveTab("dashboard")}
          />
          <NavLink
            icon={<ArrowDownRight size={20} />}
            label="Inbound"
            active={activeTab === "pemasukan"}
            onClick={() => setActiveTab("pemasukan")}
          />
          <NavLink
            icon={<ArrowUpRight size={20} />}
            label="Outbound"
            active={activeTab === "pengeluaran"}
            onClick={() => setActiveTab("pengeluaran")}
          />
          <NavLink
            icon={<Layers size={20} />}
            label="Inventory"
            active={activeTab === "stok"}
            onClick={() => setActiveTab("stok")}
          />
          <NavLink
            icon={<PlusCircle size={20} />}
            label="Sync Data"
            active={activeTab === "upload"}
            onClick={() => setActiveTab("upload")}
          />
        </div>
      </nav>

      {/* 2. Content Area */}
      <main className="flex-1 p-8 lg:p-12 relative overflow-x-hidden">
        {/* Ambient Glow Background */}
        <div className="absolute top-0 right-0 w-[600px] h-[600px] bg-blue-600/10 rounded-full blur-[120px] -z-10" />

        <header className="flex justify-between items-center mb-12">
          <h2 className="text-4xl font-black tracking-tighter capitalize">
            {activeTab}
          </h2>
          <div className="flex items-center gap-4">
            <button className="p-3 glass-panel rounded-full text-slate-400 hover:text-white transition-colors">
              <Bell size={20} />
            </button>
            <div className="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center font-bold text-xs">
              AD
            </div>
          </div>
        </header>

        <AnimatePresence mode="wait">
          <motion.div
            key={activeTab}
            initial={{ opacity: 0, y: 15 }}
            animate={{ opacity: 1, y: 0 }}
            exit={{ opacity: 0, y: -15 }}
            transition={{ duration: 0.3 }}
          >
            {activeTab === "dashboard" ? (
              <BentoGrid stats={stats} />
            ) : activeTab === "upload" ? (
              <RequestForm />
            ) : (
              <div className="glass-panel rounded-[32px] overflow-hidden">
                <InventoryTable
                  data={data}
                  loading={isLoading}
                  type={activeTab}
                />
              </div>
            )}
          </motion.div>
        </AnimatePresence>
      </main>
    </div>
  );
};

/* --- SUB-COMPONENTS --- */

const NavLink = ({ icon, label, active, onClick }) => (
  <button
    onClick={onClick}
    className={`flex items-center gap-4 px-4 py-4 rounded-2xl transition-all relative group ${
      active ? "text-white" : "text-slate-500 hover:text-slate-300"
    }`}
  >
    {active && (
      <motion.div
        layoutId="nav-pill"
        className="absolute inset-0 bg-blue-600/15 rounded-2xl border border-blue-500/30"
      />
    )}
    <span className="relative z-10">{icon}</span>
    <span className="text-sm font-bold relative z-10 hidden lg:block tracking-tight">
      {label}
    </span>
  </button>
);

const BentoGrid = ({ stats }) => (
  <motion.div
    initial="hidden"
    animate="show"
    variants={{ show: { transition: { staggerChildren: 0.1 } } }}
    className="grid grid-cols-1 md:grid-cols-4 gap-6 auto-rows-[180px]"
  >
    <motion.div
      variants={{ hidden: { opacity: 0, y: 20 }, show: { opacity: 1, y: 0 } }}
      className="md:col-span-3 row-span-2 glass-panel rounded-[32px] p-8 flex flex-col justify-between"
    >
      <div className="flex justify-between items-center">
        <h3 className="text-xl font-bold">Trend Analysis</h3>
        <div className="flex gap-4 text-[10px] font-black uppercase tracking-widest text-slate-500">
          <span className="text-emerald-400">● In</span>{" "}
          <span className="text-red-400">● Out</span>
        </div>
      </div>
      <div className="flex-1 bg-white/5 rounded-2xl mt-4 border border-white/5 flex items-center justify-center text-slate-600 italic">
        Chart.js Rendering Area
      </div>
    </motion.div>

    <StatCard
      label="Inbound"
      value={stats.masuk}
      color="text-emerald-400"
      bgColor="bg-emerald-400/10"
      icon={<ArrowDown size={20} />}
    />
    <StatCard
      label="Outbound"
      value={stats.keluar}
      color="text-red-400"
      bgColor="bg-red-400/10"
      icon={<ArrowUp size={20} />}
    />
    <StatCard
      label="Catalog"
      value={stats.total}
      color="text-blue-400"
      bgColor="bg-blue-400/10"
      icon={<Layers size={20} />}
    />

    <motion.div
      variants={{ hidden: { opacity: 0, y: 20 }, show: { opacity: 1, y: 0 } }}
      className="glass-panel rounded-[32px] p-6 flex flex-col items-center justify-center text-center"
    >
      <p className="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">
        Database Status
      </p>
      <div className="text-emerald-400 font-black text-xl flex items-center gap-2">
        <div className="w-2 h-2 rounded-full bg-emerald-400 animate-pulse" />{" "}
        SYNCED
      </div>
    </motion.div>
  </motion.div>
);

const StatCard = ({ label, value, color, bgColor, icon }) => (
  <motion.div
    variants={{ hidden: { opacity: 0, y: 20 }, show: { opacity: 1, y: 0 } }}
    whileHover={{ y: -5, backgroundColor: "rgba(255, 255, 255, 0.05)" }}
    className="glass-panel rounded-[32px] p-6 flex flex-col justify-between cursor-pointer transition-all"
  >
    <div
      className={`w-10 h-10 ${bgColor} ${color} rounded-xl flex items-center justify-center`}
    >
      {icon}
    </div>
    <div>
      <p className="text-[10px] font-bold text-slate-500 uppercase tracking-widest">
        {label}
      </p>
      <h3 className={`text-3xl font-black ${color}`}>
        {value?.toLocaleString("id-ID") || 0}
      </h3>
    </div>
  </motion.div>
);

export default App;
