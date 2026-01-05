import React from "react";
import { Printer, Trash2, CheckCircle, RotateCcw } from "lucide-react";

const InventoryTable = ({ data, type, onStatusChange, onDelete }) => {
  return (
    <div className="overflow-x-auto p-4">
      <table className="w-full text-left border-separate border-spacing-y-3">
        <thead>
          <tr className="text-[#64748b] text-[10px] font-black uppercase tracking-widest">
            <th className="px-6 py-2">Tanggal / Item</th>
            <th className="px-6 py-2 text-center">Jumlah</th>
            <th className="px-6 py-2 text-right">Aksi</th>
          </tr>
        </thead>
        <tbody>
          {data.map((item, idx) => {
            const isApproved = item.keterangan?.includes("Sudah Disetujui");
            return (
              <tr
                key={idx}
                className="bg-white shadow-sm hover:translate-x-1 transition-transform"
              >
                <td className="px-6 py-4 rounded-l-2xl">
                  <div className="font-bold text-[#002d5a]">
                    {item.nama_barang_input || item.nama_barang}
                  </div>
                  <div className="text-[10px] text-slate-400 font-bold uppercase tracking-tighter">
                    {item.tanggal}
                  </div>
                  {item.nama_pegawai && (
                    <div className="text-[9px] text-blue-600 font-black uppercase tracking-widest mt-1">
                      {item.nama_pegawai}
                    </div>
                  )}
                </td>
                <td className="px-6 py-4 text-center">
                  <span
                    className={`font-black px-3 py-1 rounded-lg ${
                      type === "pengeluaran"
                        ? "bg-red-50 text-red-600"
                        : "bg-green-50 text-green-600"
                    }`}
                  >
                    {type === "pengeluaran" ? "-" : "+"}{" "}
                    {item.jumlah || item.sisa_stok}
                  </span>
                </td>
                <td className="px-6 py-4 text-right rounded-r-2xl">
                  <div className="flex justify-end gap-2">
                    {type === "pengeluaran" && (
                      <>
                        <button
                          onClick={() =>
                            onStatusChange(
                              item.no_bukti,
                              isApproved ? "Draft" : "Sudah Disetujui"
                            )
                          }
                          className={`p-2 rounded-xl transition-colors ${
                            isApproved
                              ? "bg-green-100 text-green-700"
                              : "bg-yellow-100 text-yellow-700"
                          }`}
                        >
                          {isApproved ? (
                            <CheckCircle size={16} />
                          ) : (
                            <RotateCcw size={16} />
                          )}
                        </button>
                        <button className="p-2 bg-slate-100 text-blue-700 rounded-xl hover:bg-slate-200">
                          <Printer size={16} />
                        </button>
                        <button
                          onClick={() => onDelete(item.no_bukti)}
                          className="p-2 bg-red-50 text-red-600 rounded-xl hover:bg-red-100"
                        >
                          <Trash2 size={16} />
                        </button>
                      </>
                    )}
                  </div>
                </td>
              </tr>
            );
          })}
        </tbody>
      </table>
    </div>
  );
};

export default InventoryTable;
