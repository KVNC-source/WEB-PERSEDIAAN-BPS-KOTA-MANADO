# Web Persediaan BPS Kota Manado

Aplikasi berbasis web yang dirancang untuk mengelola inventaris dan persediaan barang di **Badan Pusat Statistik (BPS) Kota Manado**. Sistem ini memungkinkan pencatatan pemasukan barang, pengeluaran barang kepada pegawai, serta pemantauan stok secara real-time.

## 🚀 Fitur Utama

* **Manajemen Stok**: Pantau jumlah persediaan barang yang tersedia secara akurat.

* **Pencatatan Pemasukan**: Input data barang yang masuk ke gudang persediaan.

* **Pencatatan Pengeluaran**: Kelola distribusi barang kepada pegawai disertai informasi penerima.

* **Import CSV**: Fitur unggah data secara massal melalui file CSV untuk efisiensi input data pemasukan dan pengeluaran.

* **Laporan PDF**: Pembuatan laporan stok dan transaksi dalam format PDF yang siap cetak.

* **Pencarian Cerdas**: Pencarian barang otomatis yang mampu menangani perbedaan penulisan (spasi/karakter).

## 🛠️ Teknologi yang Digunakan

* **Bahasa Pemrograman**: PHP

* **Database**: MySQL

* **Interface**: HTML5, CSS3 (Custom styles), JavaScript

* **Framework & Library**:

  * Bootstrap (UI Components)

  * FPDF (Library untuk generate laporan PDF)

* **Environment**: XAMPP / Apache / MariaDB

## 📋 Persyaratan Sistem

1. Server lokal (misalnya XAMPP v3.3.0 atau yang lebih baru).

2. Browser modern (Chrome, Edge, Firefox).

3. Koneksi database yang dikonfigurasi pada `DB/config.php`.

## 📦 Cara Instalasi

1. **Clone/Download** repositori ini ke folder `htdocs` Anda.

2. **Impor Database**:

   * Buka `phpMyAdmin`.

   * Buat database baru dengan nama `barang_persediaan`.

   * Impor file `DB/barang_persediaan (1).sql`.

3. **Konfigurasi**:

   * Sesuaikan pengaturan host, user, dan password database di file `DB/config.php`.

4. **Jalankan**:

   * Buka browser dan akses `http://localhost/web-persediaan-bps-kota-manado/Template/index.php`.

## 👥 Tim Pengembang

Proyek ini dikembangkan oleh Mahasiswa Magang di **Badan Pusat Statistik (BPS) Kota Manado**:

1. **Keefa Noah Lasut** NIM: 230211060068

   *Universitas Sam Ratulangi*

2. **Nabil Hamba Illahi** NIM: 230211060071

   *Universitas Sam Ratulangi*

© 2025 - Magang BPS Kota Manado & Universitas Sam Ratulangi
