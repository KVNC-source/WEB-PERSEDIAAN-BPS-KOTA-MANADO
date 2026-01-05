import React from "react";
import {
  Package,
  ArrowUpCircle,
  ArrowDownCircle,
  Database,
  Bell,
} from "lucide-react";

const StatCard = ({ title, value, icon: Icon, colorClass }) => (
  <div className="bg-white p-6 rounded-[28px] shadow-[15px_15px_30px_#e2e8f0,-15px_-15px_30px_#ffffff] border border-white/50 flex items-center space-x-5">
    <div className={`p-4 rounded-2xl ${colorClass} bg-opacity-10`}>
      {Icon && (
        <Icon className={`w-7 h-7 ${colorClass.replace("bg-", "text-")}`} />
      )}
    </div>
    <div>
      <p className="text-[10px] font-black text-slate-400 uppercase tracking-[0.15em] mb-1">
        {title}
      </p>
      <h3 className="text-2xl font-black text-deep-navy">{value}</h3>
    </div>
  </div>
);

function App() {
  return (
    <div className="min-h-screen bg-[#f8fafc] p-6 lg:p-12">
      {/* Header Section */}
      <header className="flex justify-between items-center mb-12">
        <div>
          <h1 className="text-3xl font-black text-deep-navy tracking-tighter">
            SIPA-BPS <span className="text-blue-600">MANADO</span>
          </h1>
          <p className="text-slate-500 font-bold text-sm">
            Sistem Informasi Persediaan ATK
          </p>
        </div>
        <button className="relative p-3 bg-white rounded-2xl shadow-sm border border-slate-200 text-deep-navy hover:shadow-md transition-all">
          <Bell size={20} />
          <span className="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full border-2 border-white"></span>
        </button>
      </header>

      {/* Grid Statistik */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
        <StatCard
          title="Total Katalog"
          value="128"
          icon={Package}
          colorClass="bg-blue-600"
        />
        <StatCard
          title="Barang Masuk"
          value="2.450"
          icon={ArrowUpCircle}
          colorClass="bg-emerald-600"
        />
        <StatCard
          title="Barang Keluar"
          value="842"
          icon={ArrowDownCircle}
          colorClass="bg-orange-600"
        />
        <StatCard
          title="Posisi Stok"
          value="1.608"
          icon={Database}
          colorClass="bg-indigo-600"
        />
      </div>

      {/* Content Area */}
      <div className="grid grid-cols-1 gap-8">
        <div className="bg-white rounded-[35px] p-8 shadow-[20px_20px_60px_#bebebe,-20px_-20px_60px_#ffffff] border border-white">
          <div className="flex justify-between items-center mb-8">
            <h2 className="text-xl font-black text-deep-navy">
              Monitoring Stok ATK
            </h2>
            <div className="flex space-x-2">
              <span className="px-4 py-1.5 bg-slate-100 rounded-full text-xs font-bold text-slate-600">
                Terakhir Update: Hari Ini
              </span>
            </div>
          </div>

          {/* Placeholder Table */}
          <div className="overflow-hidden rounded-2xl border border-slate-100">
            <table className="w-full text-left border-collapse">
              <thead className="bg-slate-50">
                <tr>
                  <th className="p-4 text-xs font-black text-slate-400 uppercase tracking-widest">
                    Nama Barang
                  </th>
                  <th className="p-4 text-xs font-black text-slate-400 uppercase tracking-widest">
                    Satuan
                  </th>
                  <th className="p-4 text-xs font-black text-slate-400 uppercase tracking-widest text-center">
                    Stok
                  </th>
                  <th className="p-4 text-xs font-black text-slate-400 uppercase tracking-widest text-right">
                    Status
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr className="border-t border-slate-50">
                  <td className="p-4 font-bold text-deep-navy">
                    Kertas A4 80gr
                  </td>
                  <td className="p-4 text-slate-500 font-medium text-sm">
                    Rim
                  </td>
                  <td className="p-4 font-black text-center text-deep-navy text-lg">
                    45
                  </td>
                  <td className="p-4 text-right">
                    <span className="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-lg text-[10px] font-black uppercase">
                      Tersedia
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  );
}

export default App;
