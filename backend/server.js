const express = require("express");
const mysql = require("mysql2");
const cors = require("cors");
const app = express();

app.use(cors());
app.use(express.json());

const db = mysql.createPool({
  host: "localhost",
  user: "root",
  password: "kenola20",
  database: "barang_persediaan",
});

// 1. DASHBOARD STATS (Logic from dashboard.php)
app.get("/api/dashboard/stats", async (req, res) => {
  try {
    const [pemasukan] = await db
      .promise()
      .query(
        "SELECT SUM(jumlah) as total FROM pemasukan WHERE MONTH(tanggal) = MONTH(CURRENT_DATE)"
      );
    const [pengeluaran] = await db
      .promise()
      .query(
        "SELECT SUM(jumlah) as total FROM pengeluaran WHERE keterangan LIKE '%Sudah Disetujui%' AND MONTH(tanggal) = MONTH(CURRENT_DATE)"
      );
    const [catalog] = await db
      .promise()
      .query("SELECT COUNT(*) as total FROM barang_atk");

    res.json({
      masuk_recent: pemasukan[0].total || 0,
      keluar_recent: pengeluaran[0].total || 0,
      total_catalog: catalog[0].total || 0,
    });
  } catch (err) {
    res.status(500).json(err);
  }
});

// 2. STOCK MONITORING (Logic from stok.php)
app.get("/api/stok", async (req, res) => {
  const sql = `
        SELECT b.id_barang, b.nama_barang, b.satuan,
        IFNULL(SUM(p.jumlah), 0) AS total_masuk,
        IFNULL(sq_out.total_keluar, 0) AS total_keluar,
        (IFNULL(SUM(p.jumlah), 0) - IFNULL(sq_out.total_keluar, 0)) AS sisa_stok
        FROM barang_atk b
        LEFT JOIN pemasukan p ON b.id_barang = p.id_barang
        LEFT JOIN (
            SELECT id_barang, SUM(jumlah) AS total_keluar FROM pengeluaran 
            WHERE keterangan LIKE '%Sudah Disetujui%' GROUP BY id_barang
        ) AS sq_out ON b.id_barang = sq_out.id_barang
        GROUP BY b.id_barang ORDER BY b.nama_barang ASC`;
  const [rows] = await db.promise().query(sql);
  res.json(rows);
});

// 3. PENGELUARAN (Logic from index.php)
app.get("/api/pengeluaran", async (req, res) => {
  const [rows] = await db
    .promise()
    .query("SELECT * FROM pengeluaran ORDER BY tanggal DESC");
  res.json(rows);
});

// 4. PEMASUKAN (Logic from pemasukan.php)
app.get("/api/pemasukan", async (req, res) => {
  const [rows] = await db
    .promise()
    .query("SELECT * FROM pemasukan ORDER BY tanggal DESC");
  res.json(rows);
});

app.listen(5000, () => console.log("Backend running on port 5000"));

// Example endpoints for manage_data.php replacement
app.post("/api/manage/update-status", async (req, res) => {
  const { no_bukti, status } = req.body; // status: 'Sudah Disetujui' or 'Draft'
  const sql = "UPDATE pengeluaran SET keterangan = ? WHERE no_bukti = ?";
  await db.promise().query(sql, [status, no_bukti]);
  res.json({ message: "Status updated successfully" });
});

app.delete("/api/manage/delete/:no_bukti", async (req, res) => {
  await db
    .promise()
    .query("DELETE FROM pengeluaran WHERE no_bukti = ?", [req.params.no_bukti]);
  res.json({ message: "Deleted successfully" });
});

// CSV Upload logic (replaces process_upload.php)
// Use 'multer' and 'fast-csv' for real implementation
app.post("/api/upload/:type", (req, res) => {
  const { type } = req.params;
  // Logic to parse CSV and run INSERT queries from your process_upload.php
  res.json({ message: `Successfully processed ${type} data` });
});
