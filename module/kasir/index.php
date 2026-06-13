<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'kasir') {
    header("Location: ../../login.php");
    exit();
}
include __DIR__ . '/../../config/db.php';

// Hitung total pendapatan hari ini
$pendapatan_query = mysqli_query($connect, "SELECT SUM(jumlah_bayar) as total FROM pembayaran WHERE tgl_bayar = CURDATE()");
$pendapatan = mysqli_fetch_assoc($pendapatan_query);

// Hitung jumlah transaksi hari ini
$transaksi_query = mysqli_query($connect, "SELECT COUNT(*) as total FROM pembayaran WHERE tgl_bayar = CURDATE()");
$transaksi = mysqli_fetch_assoc($transaksi_query);

// Hitung antrean tagihan belum bayar
$tagihan_query = mysqli_query($connect, "SELECT COUNT(*) as total FROM tagihan WHERE status_bayar = 'belum'");
$tagihan = mysqli_fetch_assoc($tagihan_query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Kasir - Klinik Sehat</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="../../assets/style.css" rel="stylesheet">
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-header">
            <h1>🏥 Klinik Sehat</h1>
            <p style="font-size: 0.7rem; opacity: 0.7;">Kasir</p>
        </div>
        <nav class="sidebar-nav">
            <a href="index.php" class="active">💰 Dashboard</a>
            <a href="tagihan.php">📋 Tagihan</a>
            <a href="pembayaran.php">💵 Pembayaran</a>
            <a href="riwayat.php">📜 Riwayat</a>
        </nav>
        <div class="sidebar-footer">
            <a href="../../logout.php">🚪 Logout</a>
        </div>
    </aside>

    <main class="main-content">
        <header class="top-navbar">
            <span class="page-title">Dashboard Kasir</span>
            <a href="../../logout.php" class="btn-logout">Logout</a>
        </header>

        <div class="container">
            <div class="welcome-card">
                <h2>👋 Selamat Datang, Kasir!</h2>
                <p>Kelola transaksi keuangan klinik dengan presisi hari ini.</p>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number">Rp <?php echo number_format($pendapatan['total'] ?? 0, 0, ',', '.'); ?></div>
                    <div class="stat-label">Pendapatan Hari Ini</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $transaksi['total'] ?? 0; ?></div>
                    <div class="stat-label">Transaksi Hari Ini</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $tagihan['total'] ?? 0; ?></div>
                    <div class="stat-label">Tagihan Belum Bayar</div>
                </div>
            </div>

            <div class="menu-grid">
                <a href="tagihan.php" class="menu-card">
                    <div class="menu-icon">📋</div>
                    <div class="menu-title">Tagihan Pasien</div>
                    <div class="menu-desc">Lihat daftar tagihan</div>
                </a>
                <a href="pembayaran.php" class="menu-card">
                    <div class="menu-icon">💵</div>
                    <div class="menu-title">Proses Pembayaran</div>
                    <div class="menu-desc">Input pembayaran pasien</div>
                </a>
                <a href="riwayat.php" class="menu-card">
                    <div class="menu-icon">📜</div>
                    <div class="menu-title">Riwayat Transaksi</div>
                    <div class="menu-desc">Lihat history pembayaran</div>
                </a>
            </div>
        </div>

        <footer class="footer">
            <p>© 2026 Klinik Sehat - Final Project Basis Data</p>
        </footer>
    </main>
</body>
</html>