import React, { useState } from "react";
import { motion, AnimatePresence } from "framer-motion"; // Install via 'npm install framer-motion'
import {
  LayoutGrid,
  ArrowDownRight,
  ArrowUpRight,
  Layers,
  Settings,
  Search,
  Bell,
  Menu,
} from "lucide-react";

// Import your sub-pages
import InventoryTable from "./components/InventoryTable";
import RequestForm from "./pages/RequestForm";

const App = () => {
  const [activeTab, setActiveTab] = useState("dashboard");

  return (
    <div className="flex min-h-screen">
      {/* 1. Futuristic Floating Sidebar */}
      <nav className="w-20 lg:w-64 flex flex-col p-6 gap-8 border-r border-white/5 bg-slate-950/50 backdrop-blur-xl sticky top-0 h-screen">
        <div className="flex items-center gap-3 px-2">
          <div className="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/20">
            <Layers className="text-white" size={24} />
          </div>
          <span className="font-bold text-xl hidden lg:block tracking-tighter">
            SIPA <span className="text-blue-500">2.0</span>
          </span>
        </div>

        <div className="flex flex-col gap-2 flex-1">
          <NavLink
            icon={<LayoutGrid />}
            label="Dashboard"
            active={activeTab === "dashboard"}
            onClick={() => setActiveTab("dashboard")}
          />
          <NavLink
            icon={<ArrowDownRight />}
            label="Inbound"
            active={activeTab === "inbound"}
            onClick={() => setActiveTab("inbound")}
          />
          <NavLink
            icon={<ArrowUpRight />}
            label="Outbound"
            active={activeTab === "outbound"}
            onClick={() => setActiveTab("outbound")}
          />
          <NavLink
            icon={<Layers />}
            label="Inventory"
            active={activeTab === "stok"}
            onClick={() => setActiveTab("stok")}
          />
        </div>

        <button className="flex items-center gap-3 px-4 py-3 text-slate-400 hover:text-white transition-all">
          <Settings size={20} />
          <span className="text-sm font-medium hidden lg:block">
            System Config
          </span>
        </button>
      </nav>

      {/* 2. Main Viewport */}
      <main className="flex-1 p-8 lg:p-12 relative">
        {/* Animated Background Glows */}
        <div className="absolute top-0 right-0 w-[500px] h-[500px] bg-blue-600/10 rounded-full blur-[120px] -z-10" />
        <div className="absolute bottom-0 left-0 w-[500px] h-[500px] bg-purple-600/10 rounded-full blur-[120px] -z-10" />

        {/* Top Header */}
        <header className="flex justify-between items-center mb-12">
          <h2 className="text-4xl font-black tracking-tighter capitalize">
            {activeTab}
          </h2>
          <div className="flex items-center gap-6">
            <div className="hidden md:flex items-center bg-white/5 border border-white/10 rounded-full px-4 py-2 w-64 focus-within:ring-2 ring-blue-500/50 transition-all">
              <Search size={18} className="text-slate-500" />
              <input
                type="text"
                placeholder="Global Search..."
                className="bg-transparent border-none focus:outline-none px-3 text-sm"
              />
            </div>
            <button className="p-3 glass-panel rounded-full text-slate-300 hover:text-white">
              <Bell size={20} />
            </button>
            <div className="w-10 h-10 rounded-full bg-gradient-to-tr from-blue-500 to-purple-500 p-[2px]">
              <div className="w-full h-full rounded-full bg-slate-950 flex items-center justify-center text-xs font-bold">
                AD
              </div>
            </div>
          </div>
        </header>

        {/* 3. Dynamic Page Content with Framer Motion Animation */}
        <AnimatePresence mode="wait">
          <motion.div
            key={activeTab}
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            exit={{ opacity: 0, y: -20 }}
            transition={{ duration: 0.3 }}
          >
            {activeTab === "dashboard" && <BentoDashboard />}
            {activeTab !== "dashboard" && (
              <div className="glass-panel rounded-[32px] overflow-hidden p-8">
                {/* Your existing tables/forms but with updated styles */}
                {activeTab === "stok" && <InventoryTable type="stok" />}
              </div>
            )}
          </motion.div>
        </AnimatePresence>
      </main>
    </div>
  );
};

// --- BENTO GRID COMPONENTS ---

const BentoDashboard = () => (
  <div className="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6 auto-rows-[180px]">
    {/* Large Growth Chart Item */}
    <div className="md:col-span-2 lg:col-span-3 row-span-2 glass-panel rounded-[32px] p-8">
      <h3 className="text-xl font-bold mb-8">Stock Dynamics</h3>
      <div className="h-[250px] w-full flex items-center justify-center text-slate-600 border-2 border-dashed border-white/5 rounded-2xl">
        Chart Visualizer Placeholder
      </div>
    </div>

    {/* Stat Cards as Small Bento Items */}
    <StatBox
      label="Total Assets"
      value="2,408"
      icon={<Layers className="text-blue-400" />}
    />
    <StatBox
      label="Critical Stock"
      value="12"
      sub="Immediate attention"
      alert
    />
    <StatBox label="Inbound Vol" value="+15.2%" sub="Since last month" />
  </div>
);

const StatBox = ({ label, value, sub, alert }) => (
  <div
    className={`glass-panel rounded-[32px] p-6 flex flex-col justify-between group cursor-pointer hover:border-blue-500/50 transition-all ${
      alert ? "bg-red-500/5 border-red-500/20" : ""
    }`}
  >
    <div>
      <p className="text-xs font-bold text-slate-500 uppercase tracking-widest">
        {label}
      </p>
      <h3 className={`text-3xl font-black mt-2 ${alert ? "text-red-400" : ""}`}>
        {value}
      </h3>
    </div>
    <p className="text-[10px] font-medium text-slate-500">{sub}</p>
  </div>
);

const NavLink = ({ icon, label, active, onClick }) => (
  <button
    onClick={onClick}
    className={`flex items-center gap-4 px-4 py-4 rounded-2xl transition-all relative ${
      active ? "text-white" : "text-slate-500 hover:text-white hover:bg-white/5"
    }`}
  >
    {active && (
      <motion.div
        layoutId="nav-glow"
        className="absolute inset-0 bg-blue-600/20 rounded-2xl border border-blue-500/50 shadow-[0_0_20px_rgba(59,130,246,0.3)]"
      />
    )}
    <span className="relative z-10">{icon}</span>
    <span className="text-sm font-bold relative z-10 hidden lg:block tracking-tight">
      {label}
    </span>
  </button>
);

export default App;
