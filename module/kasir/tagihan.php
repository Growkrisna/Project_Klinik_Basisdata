<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'kasir') {
    header("Location: ../../login.php");
    exit();
}
include __DIR__ . '/../../config/db.php';

// Ambil daftar tagihan yang belum dibayar
$query = "SELECT t.*, k.id_kunjungan, p.nama as nama_pasien, p.nomor_rm, 
          (SELECT SUM(harga_jual * rd.jumlah) FROM detail_resep rd 
           JOIN resep r ON rd.id_resep = r.id_resep 
           JOIN obat o ON rd.id_obat = o.id_obat 
           WHERE r.id_kunjungan = k.id_kunjungan) as total_obat
          FROM tagihan t
          JOIN kunjungan k ON t.id_kunjungan = k.id_kunjungan
          JOIN pasien p ON k.id_pasien = p.id_pasien
          WHERE t.status_bayar = 'belum'
          ORDER BY t.id_tagihan ASC";
$result = mysqli_query($connect, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tagihan Pasien - Kasir</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="../../assets/style.css" rel="stylesheet">
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-header"><h1>🏥 Klinik Sehat</h1></div>
        <nav class="sidebar-nav">
            <a href="index.php">💰 Dashboard</a>
            <a href="tagihan.php" class="active">📋 Tagihan</a>
            <a href="pembayaran.php">💵 Pembayaran</a>
            <a href="riwayat.php">📜 Riwayat</a>
        </nav>
        <div class="sidebar-footer"><a href="../../logout.php">🚪 Logout</a></div>
    </aside>

    <main class="main-content">
        <header class="top-navbar">
            <span class="page-title">Tagihan Pasien</span>
            <a href="../../logout.php" class="btn-logout">Logout</a>
        </header>

        <div class="container">
            <div class="card">
                <div class="card-header">📋 Daftar Tagihan Belum Dibayar</div>
                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                            <tr><th>No RM</th><th>Nama Pasien</th><th>Biaya Konsultasi</th><th>Biaya Obat</th><th>Total</th><th>Aksi</th></tr>
                        </thead>
                        <tbody>
                            <?php if(mysqli_num_rows($result) > 0): ?>
                                <?php while($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td><?php echo $row['nomor_rm']; ?></td>
                                        <td><?php echo $row['nama_pasien']; ?></td>
                                        <td>Rp <?php echo number_format($row['biaya_konsultasi'], 0, ',', '.'); ?></td>
                                        <td>Rp <?php echo number_format($row['biaya_obat'], 0, ',', '.'); ?></td>
                                        <td><strong>Rp <?php echo number_format($row['total'], 0, ',', '.'); ?></strong></td>
                                        <td>
                                            <a href="pembayaran.php?id=<?php echo $row['id_tagihan']; ?>" class="btn btn-primary" style="padding: 5px 12px;">💰 Bayar</a>
                                         </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="6" style="text-align: center;">Tidak ada tagihan yang belum dibayar</td></tr>
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