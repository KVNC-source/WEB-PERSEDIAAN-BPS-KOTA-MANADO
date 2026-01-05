const db = require("../config/db");

// Logika Stok Real-time dari stok.php
exports.getInventoryStatus = async (req, res) => {
  try {
    const query = `
            SELECT 
                b.id_barang, b.nama_barang, b.satuan,
                IFNULL(SUM(p.jumlah), 0) AS total_masuk,
                IFNULL(sq_out.total_keluar, 0) AS total_keluar,
                (IFNULL(SUM(p.jumlah), 0) - IFNULL(sq_out.total_keluar, 0)) AS sisa_stok
            FROM barang_atk b
            LEFT JOIN pemasukan p ON b.id_barang = p.id_barang
            LEFT JOIN (
                SELECT id_barang, SUM(jumlah) AS total_keluar 
                FROM pengeluaran 
                WHERE keterangan LIKE '%Sudah Disetujui%'
                GROUP BY id_barang
            ) AS sq_out ON b.id_barang = sq_out.id_barang
            GROUP BY b.id_barang, b.nama_barang, b.satuan
            ORDER BY b.nama_barang ASC
        `;
    const [rows] = await db.promise().query(query);
    res.json(rows);
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
};
