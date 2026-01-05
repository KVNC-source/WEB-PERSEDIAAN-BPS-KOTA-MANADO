import React from "react";

const InventoryTable = ({ data }) => {
  return (
    <div className="overflow-x-auto">
      <table className="w-full border-separate border-spacing-y-3">
        <thead>
          <tr className="text-slate-400 text-[10px] font-black uppercase tracking-widest">
            <th className="px-6 py-3 text-left">Detail Barang</th>
            <th className="px-6 py-3 text-center">Satuan</th>
            <th className="px-6 py-3 text-center">Sisa Stok</th>
            <th className="px-6 py-3 text-right">Status</th>
          </tr>
        </thead>
        <tbody>
          {data.map((item) => (
            <tr
              key={item.id_barang}
              className="bg-white shadow-sm hover:translate-x-2 transition-transform duration-300"
            >
              <td className="px-6 py-4 rounded-l-2xl border-y border-l border-slate-50">
                <div className="font-bold text-deep-navy">
                  {item.nama_barang}
                </div>
                <div className="text-[10px] text-slate-400 font-bold uppercase">
                  ID: {item.id_barang}
                </div>
              </td>
              <td className="px-6 py-4 text-center border-y border-slate-50">
                <span className="bg-slate-50 px-3 py-1 rounded-md text-[10px] font-black text-slate-500 uppercase">
                  {item.satuan}
                </span>
              </td>
              <td className="px-6 py-4 text-center border-y border-slate-50">
                <span
                  className={`font-black text-lg ${
                    item.sisa_stok < 5 ? "text-red-500" : "text-deep-navy"
                  }`}
                >
                  {item.sisa_stok}
                </span>
              </td>
              <td className="px-6 py-4 text-right rounded-r-2xl border-y border-r border-slate-50">
                <span
                  className={`px-3 py-1 rounded-lg text-[10px] font-black uppercase shadow-sm ${
                    item.sisa_stok <= 0
                      ? "bg-red-50 text-red-600"
                      : item.sisa_stok < 5
                      ? "bg-orange-50 text-orange-600"
                      : "bg-emerald-50 text-emerald-600"
                  }`}
                >
                  {item.sisa_stok <= 0
                    ? "Habis"
                    : item.sisa_stok < 5
                    ? "Menipis"
                    : "Tersedia"}
                </span>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
};

export default InventoryTable;
