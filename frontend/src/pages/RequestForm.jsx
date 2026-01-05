import React, { useState } from "react";
import axios from "axios";
import { Upload, AlertCircle, FileText } from "lucide-react";

const RequestForm = () => {
  const [file, setFile] = useState(null);
  const [type, setType] = useState("pengeluaran");

  const handleUpload = async (e) => {
    e.preventDefault();
    if (!file?.name.toLowerCase().endsWith(".csv")) {
      alert("Harap unggah file format .csv sesuai standar sistem.");
      return;
    }

    const formData = new FormData();
    formData.append("excel_file", file);
    formData.append("type", type);

    try {
      await axios.post(`http://localhost:5000/api/upload/${type}`, formData);
      alert("Data berhasil diproses!");
    } catch {
      alert("Gagal memproses data.");
    }
  };

  return (
    <div className="max-w-4xl mx-auto space-y-8 p-6">
      <div className="bg-white p-8 rounded-[32px] shadow-xl border border-white/50">
        <h2 className="text-2xl font-black text-deep-navy mb-6 tracking-tighter">
          Pusat Sinkronisasi Data
        </h2>
        <form onSubmit={handleUpload} className="space-y-6">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            {["pengeluaran", "pemasukan", "master"].map((t) => (
              <button
                key={t}
                type="button"
                onClick={() => setType(t)}
                className={`p-4 rounded-2xl border-2 transition-all font-bold capitalize ${
                  type === t
                    ? "border-blue-600 bg-blue-50 text-blue-700"
                    : "border-slate-100 text-slate-400"
                }`}
              >
                Data {t}
              </button>
            ))}
          </div>

          <div className="border-2 border-dashed border-slate-200 rounded-[24px] p-12 text-center">
            <input
              type="file"
              id="csv-upload"
              className="hidden"
              onChange={(e) => setFile(e.target.files[0])}
            />
            <label
              htmlFor="csv-upload"
              className="cursor-pointer flex flex-col items-center gap-4"
            >
              <div className="p-4 bg-blue-50 rounded-2xl text-blue-600">
                <Upload size={32} />
              </div>
              <div>
                <p className="font-bold text-deep-navy">
                  {file ? file.name : "Klik untuk pilih file CSV"}
                </p>
                <p className="text-xs text-slate-400 uppercase tracking-widest mt-1">
                  Hanya mendukung format .csv
                </p>
              </div>
            </label>
          </div>

          <button
            type="submit"
            className="w-full py-4 bg-blue-600 text-white rounded-2xl font-black uppercase tracking-[0.2em] shadow-lg shadow-blue-100 hover:bg-blue-700"
          >
            Mulai Sinkronisasi
          </button>
        </form>
      </div>

      <div className="bg-amber-50 border border-amber-100 p-6 rounded-[24px] flex gap-4">
        <AlertCircle className="text-amber-600 shrink-0" />
        <p className="text-xs font-bold text-amber-700 uppercase leading-relaxed tracking-wide">
          Sistem otomatis mengisi sel tanggal kosong berdasarkan nilai pada
          baris sebelumnya (Carry-Forward). Pastikan header file sesuai
          template.
        </p>
      </div>
    </div>
  );
};

export default RequestForm;
