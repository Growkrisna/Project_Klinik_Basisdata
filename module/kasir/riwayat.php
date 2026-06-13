<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'kasir') {
    header("Location: ../../login.php");
    exit();
}
include __DIR__ . '/../../config/db.php';

// Ambil riwayat pembayaran
$query = "SELECT p.*, t.total, t.biaya_konsultasi, t.biaya_obat, 
          k.id_kunjungan, ps.nama as nama_pasien, ps.nomor_rm
          FROM pembayaran p
          JOIN tagihan t ON p.id_tagihan = t.id_tagihan
          JOIN kunjungan k ON t.id_kunjungan = k.id_kunjungan
          JOIN pasien ps ON k.id_pasien = ps.id_pasien
          ORDER BY p.tgl_bayar DESC
          LIMIT 50";
$result = mysqli_query($connect, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Transaksi - Kasir</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="../../assets/style.css" rel="stylesheet">
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-header"><h1>🏥 Klinik Sehat</h1></div>
        <nav class="sidebar-nav">
            <a href="index.php">💰 Dashboard</a>
            <a href="tagihan.php">📋 Tagihan</a>
            <a href="pembayaran.php">💵 Pembayaran</a>
            <a href="riwayat.php" class="active">📜 Riwayat</a>
        </nav>
        <div class="sidebar-footer"><a href="../../logout.php">🚪 Logout</a></div>
    </aside>

    <main class="main-content">
        <header class="top-navbar">
            <span class="page-title">Riwayat Transaksi</span>
            <a href="../../logout.php" class="btn-logout">Logout</a>
        </header>

        <div class="container">
            <div class="card">
                <div class="card-header">📜 Riwayat Pembayaran</div>
                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                            <tr><th>Tgl Bayar</th><th>No RM</th><th>Nama Pasien</th><th>Total</th><th>Metode</th><th>Status</th></tr>
                        </thead>
                        <tbody>
                            <?php if(mysqli_num_rows($result) > 0): ?>
                                <?php while($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td><?php echo $row['tgl_bayar']; ?></td>
                                        <td><?php echo $row['nomor_rm']; ?></td>
                                        <td><?php echo $row['nama_pasien']; ?></td>
                                        <td>Rp <?php echo number_format($row['jumlah_bayar'], 0, ',', '.'); ?></td>
                                        <td><?php echo $row['metode']; ?></td>
                                        <td><span class="badge badge-success">Lunas</span></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="6" style="text-align: center;">Belum ada transaksi</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <footer class="footer"><p>© 2026 Klinik Sehat</p></footer>
    </main>
</body>
</html>