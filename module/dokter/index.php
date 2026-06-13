<?php
session_start();

// Cek login dan role
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'dokter') {
    header("Location: ../../login.php");
    exit();
}

// Koneksi database dengan path absolut
include __DIR__ . '/../../config/db.php';

// Cek apakah koneksi berhasil
if(!$connect) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Hitung jumlah antrian hari ini
$antrian_query = mysqli_query($connect, "SELECT COUNT(*) as total FROM kunjungan WHERE tgl_kunjungan = CURDATE() AND status_antrian != 'selesai'");
$antrian = mysqli_fetch_assoc($antrian_query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Dokter - Klinik Sehat</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="../../assets/style.css" rel="stylesheet">
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-header"><h1>🏥 Klinik Sehat</h1></div>
        <nav class="sidebar-nav">
            <a href="index.php" class="active">📊 Dashboard</a>
            <a href="antrian.php">👥 Antrian Pasien</a>
            <a href="rekam_medis.php">📊 Rekam Medis</a>
            <a href="buat_resep.php">💊 Buat Resep</a>
        </nav>
        <div class="sidebar-footer"><a href="../../logout.php">🚪 Logout</a></div>
    </aside>

    <main class="main-content">
        <header class="top-navbar">
            <span class="page-title">Dashboard Dokter</span>
            <a href="../../logout.php" class="btn-logout">Logout</a>
        </header>

        <div class="container">
            <div class="welcome-card">
                <h2>👋 Selamat Datang, Dokter!</h2>
                <p>Hari ini ada <?php echo $antrian['total'] ?? 0; ?> pasien yang menunggu pemeriksaan.</p>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $antrian['total'] ?? 0; ?></div>
                    <div class="stat-label">Pasien Menunggu</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">🕒</div>
                    <div class="stat-label"><?php echo date('H:i'); ?> WIB</div>
                </div>
            </div>

            <div class="menu-grid">
                <a href="antrian.php" class="menu-card">
                    <div class="menu-icon">👥</div>
                    <div class="menu-title">Daftar Antrian</div>
                    <div class="menu-desc">Lihat & panggil pasien</div>
                </a>
                <a href="rekam_medis.php" class="menu-card">
                    <div class="menu-icon">📊</div>
                    <div class="menu-title">Rekam Medis</div>
                    <div class="menu-desc">Input diagnosis & tindakan</div>
                </a>
                <a href="buat_resep.php" class="menu-card">
                    <div class="menu-icon">💊</div>
                    <div class="menu-title">Buat Resep</div>
                    <div class="menu-desc">Resep obat pasien</div>
                </a>
            </div>
        </div>

        <footer class="footer">
            <p>© 2026 Klinik Sehat - Final Project Basis Data</p>
        </footer>
    </main>
</body>
</html>