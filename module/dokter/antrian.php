<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'dokter') {
    header("Location: ../../login.php");
    exit();
}
include '../../config/db.php';

// Update status antrian jika dipanggil
if(isset($_GET['panggil'])) {
    $id = $_GET['panggil'];
    mysqli_query($connect, "UPDATE kunjungan SET status_antrian = 'dipanggil' WHERE id_kunjungan = $id");
    header("Location: antrian.php");
    exit();
}

if(isset($_GET['selesai'])) {
    $id = $_GET['selesai'];
    mysqli_query($connect, "UPDATE kunjungan SET status_antrian = 'selesai' WHERE id_kunjungan = $id");
    header("Location: antrian.php");
    exit();
}

$query = "SELECT k.*, p.nama as nama_pasien, p.nomor_rm, p.no_hp, d.nama as nama_dokter 
          FROM kunjungan k 
          JOIN pasien p ON k.id_pasien = p.id_pasien 
          JOIN dokter d ON k.id_dokter = d.id_dokter 
          WHERE k.tgl_kunjungan = CURDATE() AND k.status_antrian != 'selesai'
          ORDER BY FIELD(k.status_antrian, 'menunggu', 'dipanggil'), k.id_kunjungan ASC";
$result = mysqli_query($connect, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Antrian Pasien - Dokter</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="../../assets/style.css" rel="stylesheet">
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-header">
            <h1>🏥 Klinik Sehat</h1>
            <p style="font-size: 0.7rem; opacity: 0.7;">Dokter</p>
        </div>
        <nav class="sidebar-nav">
            <a href="index.php">📊 Dashboard</a>
            <a href="antrian.php" class="active">👥 Antrian Pasien</a>
            <a href="rekam_medis.php">📊 Rekam Medis</a>
            <a href="buat_resep.php">💊 Buat Resep</a>
        </nav>
        <div class="sidebar-footer">
            <a href="../../logout.php">🚪 Logout</a>
        </div>
    </aside>

    <main class="main-content">
        <header class="top-navbar">
            <span class="page-title">Antrian Pasien Hari Ini</span>
            <a href="../../logout.php" class="btn-logout">Logout</a>
        </header>

        <div class="container">
            <div class="welcome-card">
                <h2>👋 Selamat Datang, Dokter!</h2>
                <p>Berikut adalah daftar pasien yang menunggu pemeriksaan.</p>
            </div>

            <div class="card">
                <div class="card-header">📋 Daftar Antrian</div>
                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                            <tr><th>Antrian</th><th>No RM</th><th>Nama Pasien</th><th>Keluhan</th><th>Status</th><th>Aksi</th></tr>
                        </thead>
                        <tbody>
                            <?php if(mysqli_num_rows($result) > 0): ?>
                                <?php while($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td><strong>#<?php echo $row['id_kunjungan']; ?></strong></td>
                                        <td><?php echo $row['nomor_rm']; ?></td>
                                        <td><?php echo $row['nama_pasien']; ?></td>
                                        <td><?php echo substr($row['keluhan_utama'], 0, 50); ?>...</td>
                                        <td>
                                            <span class="badge badge-<?php echo $row['status_antrian'] == 'menunggu' ? 'warning' : 'info'; ?>">
                                                <?php echo $row['status_antrian'] == 'menunggu' ? 'Menunggu' : 'Dipanggil'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if($row['status_antrian'] == 'menunggu'): ?>
                                                <a href="?panggil=<?php echo $row['id_kunjungan']; ?>" class="btn btn-primary" style="padding: 5px 12px;">📢 Panggil</a>
                                            <?php elseif($row['status_antrian'] == 'dipanggil'): ?>
                                                <a href="rekam_medis.php?id=<?php echo $row['id_kunjungan']; ?>" class="btn btn-primary" style="padding: 5px 12px;">📝 Periksa</a>
                                                <a href="?selesai=<?php echo $row['id_kunjungan']; ?>" class="btn btn-secondary" style="padding: 5px 12px;">✅ Selesai</a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="6" style="text-align: center;">Belum ada antrian hari ini</td></tr>
                            <?php endif; ?>
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