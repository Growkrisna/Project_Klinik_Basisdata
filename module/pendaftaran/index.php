<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'pendaftaran') {
    header("Location: ../../login.php");
    exit();
}

include '../../config/db.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pendaftaran - Klinik Sehat</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="../../assets/style.css" rel="stylesheet">
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <h1>🏥 Klinik Sehat</h1>
            <p style="font-size: 0.7rem; opacity: 0.7; margin-top: 4px;">Pendaftaran</p>
        </div>
        <nav class="sidebar-nav">
            <a href="#" class="active">
                <span>📊</span> Dashboard
            </a>
            <a href="#">
                <span>👥</span> Data Pasien
            </a>
            <a href="#">
                <span>🎫</span> Antrian
            </a>
            <a href="edit_pasien.php">
                <span>✏️</span> Kelola Pasien
            </a>
            <a href="kelola_dokter.php">
                <span>👨‍⚕️</span> Kelola Dokter
            </a>
        </nav>
        <div class="sidebar-footer">
            <a href="../../logout.php">
                <span>🚪</span> Logout
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <header class="top-navbar">
            <div>
                <span class="page-title">Dashboard Pendaftaran</span>
                <span class="role-badge">Petugas Pendaftaran</span>
            </div>
            <div>
                <span style="margin-right: 16px;">👋 <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <a href="../../logout.php" class="btn-logout">Logout</a>
            </div>
        </header>

        <div class="container">
            <!-- Welcome Card -->
            <div class="welcome-card">
                <h2>Selamat Datang, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
                <p>Monitor antrean pasien dan lakukan registrasi dengan efisien. Pastikan data pasien terdata dengan benar.</p>
            </div>

            <!-- Menu Grid -->
            <div class="menu-grid">
                <a href="tambah_pasien.php" class="menu-card">
                    <div class="menu-icon">📝</div>
                    <div class="menu-title">Registrasi Pasien Baru</div>
                    <div class="menu-desc">Mendaftarkan pasien pertama kali berobat</div>
                </a>
                <a href="cari_pasien.php" class="menu-card">
                    <div class="menu-icon">🔍</div>
                    <div class="menu-title">Cari Pasien Lama</div>
                    <div class="menu-desc">Mencari data pasien berdasarkan nomor RM</div>
                </a>
                <a href="buat_kunjungan.php" class="menu-card">
                    <div class="menu-icon">🎫</div>
                    <div class="menu-title">Buat Kunjungan & Antrian</div>
                    <div class="menu-desc">Mencatat kunjungan dan mencetak nomor antrian</div>
                </a>
                <a href="antrian.php" class="menu-card">
                    <div class="menu-icon">📋</div>
                    <div class="menu-title">Daftar Antrian Hari Ini</div>
                    <div class="menu-desc">Melihat daftar antrian pasien</div>
                </a>
            </div>

            <!-- Recent Registrations -->
            <div class="card" style="margin-top: 28px;">
                <div class="card-header">📋 Pasien Terdaftar Hari Ini</div>
                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                            <tr><th>No RM</th><th>Nama</th><th>No HP</th><th>Tgl Daftar</th></tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT * FROM pasien ORDER BY tgl_daftar DESC LIMIT 5";
                            $result = mysqli_query($connect, $query);
                            while($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>
                                    <td>{$row['nomor_rm']}</td>
                                    <td>{$row['nama']}</td>
                                    <td>{$row['no_hp']}</td>
                                    <td>{$row['tgl_daftar']}</td>
                                </tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <footer class="footer">
            <p>© 2026 Klinik Sehat - Final Project Basis Data</p>
        </footer>
    </main>
</body>
</html>