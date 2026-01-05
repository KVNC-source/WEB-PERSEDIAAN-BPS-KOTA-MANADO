import React from "react";
import { motion } from "framer-motion";
import { Printer, Trash2, CheckCircle, Clock } from "lucide-react";

const TableSkeleton = () => (
  <div className="p-8 space-y-6">
    {[...Array(6)].map((_, i) => (
      <div
        key={i}
        className="flex items-center justify-between py-4 border-b border-white/5"
      >
        <div className="space-y-2 w-1/3">
          <div className="h-4 skeleton-shimmer rounded-md w-3/4"></div>
          <div className="h-3 skeleton-shimmer rounded-md w-1/2 opacity-50"></div>
        </div>
        <div className="h-8 skeleton-shimmer rounded-lg w-16"></div>
        <div className="h-10 skeleton-shimmer rounded-xl w-32"></div>
      </div>
    ))}
  </div>
);

const InventoryTable = ({ data, loading, type, onStatusChange, onDelete }) => {
  if (loading) return <TableSkeleton />;

  return (
    <div className="p-8">
      <table className="w-full text-left">
        <thead>
          <tr className="text-slate-500 text-[10px] font-black uppercase tracking-[0.2em] border-b border-white/5">
            <th className="pb-6">Identifier / Recipient</th>
            <th className="pb-6 text-center">Qty</th>
            <th className="pb-6 text-right">Actions</th>
          </tr>
        </thead>
        <tbody className="divide-y divide-white/5">
          {data.map((item, idx) => {
            const isApproved = item.keterangan?.includes("Sudah Disetujui");
            return (
              <motion.tr
                initial={{ opacity: 0 }}
                animate={{ opacity: 1 }}
                transition={{ delay: idx * 0.03 }}
                key={idx}
                className="group hover:bg-white/2 transition-colors"
              >
                <td className="py-6">
                  <div className="font-bold text-slate-100">
                    {item.nama_barang_input || item.nama_barang}
                  </div>
                  <div className="text-[10px] text-slate-500 font-bold mt-1 uppercase tracking-widest">
                    {item.tanggal}
                  </div>
                </td>
                <td className="py-6 text-center">
                  <span
                    className={`text-lg font-black ${
                      type === "pengeluaran"
                        ? "text-red-400"
                        : "text-emerald-400"
                    }`}
                  >
                    {type === "pengeluaran" ? "-" : "+"}{" "}
                    {item.jumlah || item.sisa_stok}
                  </span>
                </td>
                <td className="py-6 text-right">
                  <div className="flex justify-end items-center gap-3">
                    {type === "pengeluaran" && (
                      <button
                        onClick={() =>
                          onStatusChange(
                            item.no_bukti,
                            isApproved ? "Draft" : "Sudah Disetujui"
                          )
                        }
                        className={`px-3 py-1.5 rounded-lg text-[10px] font-black uppercase transition-all ${
                          isApproved
                            ? "bg-emerald-500/10 text-emerald-400 border border-emerald-500/20"
                            : "bg-amber-500/10 text-amber-400 border border-amber-500/20"
                        }`}
                      >
                        {isApproved ? "Verified" : "Pending"}
                      </button>
                    )}
                    <button className="p-2 glass-panel rounded-xl text-slate-400 hover:text-white transition-all">
                      <Printer size={16} />
                    </button>
                    <button
                      onClick={() => onDelete(item.no_bukti)}
                      className="p-2 bg-red-500/10 text-red-400 rounded-xl hover:bg-red-500 hover:text-white transition-all"
                    >
                      <Trash2 size={16} />
                    </button>
                  </div>
                </td>
              </motion.tr>
            );
          })}
        </tbody>
      </table>
    </div>
  );
};

export default InventoryTable;
