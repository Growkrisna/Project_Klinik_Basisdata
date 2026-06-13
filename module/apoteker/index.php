<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'apoteker') {
    header("Location: ../../login.php");
    exit();
}
include __DIR__ . '/../../config/db.php';

// Hitung jumlah resep pending
$resep_query = mysqli_query($connect, "SELECT COUNT(*) as total FROM resep WHERE status = 'pending'");
$resep_pending = mysqli_fetch_assoc($resep_query);

// Hitung jumlah obat dengan stok menipis (< 10)
$stok_query = mysqli_query($connect, "SELECT COUNT(*) as total FROM obat WHERE stok < 10");
$stok_menipis = mysqli_fetch_assoc($stok_query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Apoteker - Klinik Sehat</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="../../assets/style.css" rel="stylesheet">
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-header">
            <h1>🏥 Klinik Sehat</h1>
            <p style="font-size: 0.7rem; opacity: 0.7;">Apoteker</p>
        </div>
        <nav class="sidebar-nav">
            <a href="index.php" class="active">📊 Dashboard</a>
            <a href="daftar_resep.php">📋 Daftar Resep</a>
            <a href="stok_obat.php">📦 Stok Obat</a>
            <a href="tambah_obat.php">➕ Tambah Obat</a>
            <a href="kelola_obat.php">✏️ Kelola Obat</a>
        </nav>
        <div class="sidebar-footer">
            <a href="../../logout.php">🚪 Logout</a>
        </div>
    </aside>

    <main class="main-content">
        <header class="top-navbar">
            <span class="page-title">Dashboard Apoteker</span>
            <a href="../../logout.php" class="btn-logout">Logout</a>
        </header>

        <div class="container">
            <div class="welcome-card">
                <h2>👋 Selamat Datang, Apoteker!</h2>
                <p>Kelola resep pasien dan ketersediaan obat dengan efisien.</p>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $resep_pending['total'] ?? 0; ?></div>
                    <div class="stat-label">Resep Pending</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stok_menipis['total'] ?? 0; ?></div>
                    <div class="stat-label">Stok Menipis</div>
                </div>
            </div>

            <div class="menu-grid">
                <a href="daftar_resep.php" class="menu-card">
                    <div class="menu-icon">📋</div>
                    <div class="menu-title">Daftar Resep</div>
                    <div class="menu-desc">Proses resep pasien</div>
                </a>
                <a href="stok_obat.php" class="menu-card">
                    <div class="menu-icon">📦</div>
                    <div class="menu-title">Stok Obat</div>
                    <div class="menu-desc">Cek & update stok</div>
                </a>
                <a href="tambah_obat.php" class="menu-card">
                    <div class="menu-icon">➕</div>
                    <div class="menu-title">Tambah Obat</div>
                    <div class="menu-desc">Tambah obat baru</div>
                </a>
            </div>
        </div>

        <footer class="footer">
            <p>© 2026 Klinik Sehat - Final Project Basis Data</p>
        </footer>
    </main>
</body>
</html>