<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense Analytics Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .chart-container { position: relative; height: 300px; width: 100%; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800">

    <div class="min-h-screen p-4 md:p-8">
        <!-- Header -->
        <div class="max-w-7xl mx-auto mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Dashboard Pengeluaran</h1>
                <p class="text-gray-500">Pantau dan analisis kesehatan finansial Anda secara real-time.</p>
            </div>
            <div class="flex gap-2">
                <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                    Tambah Transaksi
                </button>
                <button class="bg-white border border-gray-200 hover:bg-gray-50 px-4 py-2 rounded-lg flex items-center gap-2 transition-colors">
                    <i data-lucide="download" class="w-4 h-4"></i>
                    Export PDF
                </button>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                        <i data-lucide="wallet" class="w-6 h-6"></i>
                    </div>
                    <span class="text-xs font-semibold text-green-500 bg-green-50 px-2 py-1 rounded-full">+12% vs bln lalu</span>
                </div>
                <h3 class="text-gray-500 text-sm font-medium">Total Pengeluaran Bulan Ini</h3>
                <p class="text-2xl font-bold mt-1" id="total-expense">Rp 0</p>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-2 bg-orange-50 text-orange-600 rounded-lg">
                        <i data-lucide="trending-up" class="w-6 h-6"></i>
                    </div>
                </div>
                <h3 class="text-gray-500 text-sm font-medium">Rata-rata Harian</h3>
                <p class="text-2xl font-bold mt-1" id="avg-expense">Rp 0</p>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-2 bg-purple-50 text-purple-600 rounded-lg">
                        <i data-lucide="pie-chart" class="w-6 h-6"></i>
                    </div>
                </div>
                <h3 class="text-gray-500 text-sm font-medium">Kategori Terbesar</h3>
                <p class="text-2xl font-bold mt-1" id="top-category">-</p>
            </div>
        </div>

        <!-- Main Charts Section -->
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Line Chart: Trends -->
            <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="font-bold text-lg">Tren Pengeluaran 6 Bulan Terakhir</h3>
                    <select class="text-sm border-none bg-gray-50 rounded-md p-1 focus:ring-0">
                        <option>2024</option>
                        <option>2023</option>
                    </select>
                </div>
                <div class="chart-container">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>

            <!-- Doughnut Chart: Categories -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h3 class="font-bold text-lg mb-6 text-center">Distribusi Kategori</h3>
                <div class="chart-container flex items-center justify-center">
                    <canvas id="categoryChart"></canvas>
                </div>
                <div class="mt-4 space-y-2" id="category-legend">
                    <!-- Legend will be populated by JS -->
                </div>
            </div>
        </div>

        <!-- Recent Transactions Table -->
        <div class="max-w-7xl mx-auto bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-lg">Transaksi Terbaru</h3>
                <button class="text-indigo-600 text-sm font-medium hover:underline">Lihat Semua</button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                        <tr>
                            <th class="px-6 py-4 font-semibold">Deskripsi</th>
                            <th class="px-6 py-4 font-semibold">Kategori</th>
                            <th class="px-6 py-4 font-semibold text-right">Tanggal</th>
                            <th class="px-6 py-4 font-semibold text-right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100" id="transaction-list">
                        <!-- Rows populated by JS -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Initialize Lucide Icons
        lucide.createIcons();

        // --- Mock Data ---
        const formatIDR = (val) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val);

        const dataTren = {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
            values: [1200000, 1900000, 1500000, 2500000, 2200000, 3100000]
        };

        const dataKategori = {
            labels: ['Makanan', 'Transportasi', 'Hiburan', 'Tagihan', 'Lainnya'],
            values: [35, 15, 10, 30, 10],
            colors: ['#4f46e5', '#0ea5e9', '#8b5cf6', '#f59e0b', '#64748b']
        };

        const transactions = [
            { desc: 'Makan Siang Bakso', cat: 'Makanan', date: '2024-06-15', amount: 35000 },
            { desc: 'Token Listrik', cat: 'Tagihan', date: '2024-06-14', amount: 200000 },
            { desc: 'Gojek ke Kantor', cat: 'Transportasi', date: '2024-06-14', amount: 15000 },
            { desc: 'Nonton Bioskop', cat: 'Hiburan', date: '2024-06-12', amount: 75000 },
        ];

        // --- Stats Calculation ---
        document.getElementById('total-expense').innerText = formatIDR(3100000);
        document.getElementById('avg-expense').innerText = formatIDR(3100000 / 30);
        document.getElementById('top-category').innerText = 'Makanan';

        // --- Render Charts ---

        // 1. Line Chart
        const ctxTrend = document.getElementById('trendChart').getContext('2d');
        const gradient = ctxTrend.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(79, 70, 229, 0.2)');
        gradient.addColorStop(1, 'rgba(79, 70, 229, 0)');

        new Chart(ctxTrend, {
            type: 'line',
            data: {
                labels: dataTren.labels,
                datasets: [{
                    label: 'Pengeluaran',
                    data: dataTren.values,
                    borderColor: '#4f46e5',
                    borderWidth: 3,
                    fill: true,
                    backgroundColor: gradient,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#fff',
                    pointBorderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { 
                        beginAtZero: true, 
                        grid: { color: '#f3f4f6' },
                        ticks: { callback: value => 'Rp ' + (value/1000) + 'k' }
                    },
                    x: { grid: { display: false } }
                }
            }
        });

        // 2. Category Chart
        const ctxCat = document.getElementById('categoryChart').getContext('2d');
        new Chart(ctxCat, {
            type: 'doughnut',
            data: {
                labels: dataKategori.labels,
                datasets: [{
                    data: dataKategori.values,
                    backgroundColor: dataKategori.colors,
                    borderWidth: 0,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%',
                plugins: { legend: { display: false } }
            }
        });

        // Populate Legend
        const legendContainer = document.getElementById('category-legend');
        dataKategori.labels.forEach((label, i) => {
            const item = document.createElement('div');
            item.className = 'flex items-center justify-between text-sm';
            item.innerHTML = `
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full" style="background: ${dataKategori.colors[i]}"></span>
                    <span class="text-gray-600">${label}</span>
                </div>
                <span class="font-semibold text-gray-900">${dataKategori.values[i]}%</span>
            `;
            legendContainer.appendChild(item);
        });

        // Populate Transactions
        const tableBody = document.getElementById('transaction-list');
        transactions.forEach(t => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="px-6 py-4 font-medium text-gray-900">${t.desc}</td>
                <td class="px-6 py-4 text-gray-500 text-sm">
                    <span class="bg-gray-100 px-2 py-1 rounded text-xs uppercase font-semibold">${t.cat}</span>
                </td>
                <td class="px-6 py-4 text-gray-500 text-sm text-right">${t.date}</td>
                <td class="px-6 py-4 font-bold text-gray-900 text-right">${formatIDR(t.amount)}</td>
            `;
            tableBody.appendChild(row);
        });
    </script>
</body>
</html>