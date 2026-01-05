import React, { useState, useEffect } from "react";
import axios from "axios";

const RequestForm = () => {
  const [items, setItems] = useState([]);
  const [employees, setEmployees] = useState([]);

  useEffect(() => {
    // Mengambil data dropdown dari API
    axios.get("/api/inventory").then((res) => setItems(res.data));
    axios.get("/api/pegawai").then((res) => setEmployees(res.data));
  }, []);

  return (
    <div className="max-w-xl mx-auto bg-white p-10 rounded-[32px] shadow-2xl border border-white">
      <h2 className="text-2xl font-black text-deep-navy mb-6 text-center">
        Form Permintaan ATK
      </h2>
      <form className="space-y-5">
        <div>
          <label className="block text-[10px] font-black text-slate-400 uppercase mb-2">
            Nama Pegawai
          </label>
          <select className="w-full bg-slate-50 border border-slate-100 rounded-xl p-3 font-bold text-deep-navy focus:ring-2 focus:ring-blue-500 outline-none">
            <option>Pilih nama Anda...</option>
            {employees.map((emp) => (
              <option key={emp.id}>{emp.nama_pegawai}</option>
            ))}
          </select>
        </div>
        {/* ... Lanjutkan input lainnya sesuai form_permintaan.php ... */}
        <button className="w-full bg-deep-navy text-white font-black py-4 rounded-2xl shadow-lg hover:-translate-y-1 transition-all uppercase tracking-widest">
          Kirim Laporan
        </button>
      </form>
    </div>
  );
};
