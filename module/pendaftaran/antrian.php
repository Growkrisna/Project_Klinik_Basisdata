<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'pendaftaran') {
    header("Location: ../../login.php");
    exit();
}
include '../../config/db.php';

$query = "SELECT k.*, p.nama as nama_pasien, d.nama as nama_dokter 
          FROM kunjungan k 
          JOIN pasien p ON k.id_pasien = p.id_pasien 
          JOIN dokter d ON k.id_dokter = d.id_dokter 
          WHERE k.tgl_kunjungan = CURDATE() 
          ORDER BY k.id_kunjungan ASC";
$result = mysqli_query($connect, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Antrian Hari Ini - Klinik Sehat</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="../../assets/style.css" rel="stylesheet">
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-header"><h1>🏥 Klinik Sehat</h1></div>
        <nav class="sidebar-nav">
            <a href="index.php">📊 Dashboard</a>
            <a href="tambah_pasien.php">📝 Registrasi Pasien</a>
            <a href="cari_pasien.php">🔍 Cari Pasien</a>
            <a href="antrian.php" class="active">📋 Antrian</a>
        </nav>
        <div class="sidebar-footer"><a href="../../logout.php">🚪 Logout</a></div>
    </aside>

    <main class="main-content">
        <header class="top-navbar">
            <span class="page-title">Daftar Antrian Hari Ini</span>
            <a href="../../logout.php" class="btn-logout">Logout</a>
        </header>
        <div class="container">
            <div class="card">
                <div class="card-header">📋 Antrian Pasien - <?php echo date('d/m/Y'); ?></div>
                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                            <tr><th>No Antrian</th><th>Nama Pasien</th><th>Dokter</th><th>Keluhan</th><th>Status</th></tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><strong>#<?php echo $row['id_kunjungan']; ?></strong></td>
                                    <td><?php echo $row['nama_pasien']; ?></td>
                                    <td><?php echo $row['nama_dokter']; ?></td>
                                    <td><?php echo substr($row['keluhan_utama'], 0, 40); ?>...</td>
                                    <td>
                                        <span class="badge badge-<?php 
                                            echo $row['status_antrian'] == 'menunggu' ? 'warning' : ($row['status_antrian'] == 'dipanggil' ? 'info' : 'success'); 
                                        ?>">
                                            <?php echo $row['status_antrian']; ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <footer class="footer"><p>© 2026 Klinik Sehat</p></footer>
    </main>
</body>
</html>