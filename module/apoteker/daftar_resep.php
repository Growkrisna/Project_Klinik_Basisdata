<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'apoteker') {
    header("Location: ../../login.php");
    exit();
}
include __DIR__ . '/../../config/db.php';

$flash_success = $_SESSION['flash_success'] ?? null;
$flash_error = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

// Proses update status resep
if(isset($_GET['proses'])) {
    $id_resep = (int) $_GET['proses'];

    $resep_query = mysqli_query($connect, "SELECT id_kunjungan FROM resep WHERE id_resep = $id_resep");
    $resep = mysqli_fetch_assoc($resep_query);

    if($resep) {
        $id_kunjungan = (int) $resep['id_kunjungan'];

        mysqli_begin_transaction($connect);
        try {
            $cek_stok_query = mysqli_query($connect, "SELECT o.id_obat, o.nama_obat, o.stok, rd.jumlah
                FROM detail_resep rd
                JOIN obat o ON rd.id_obat = o.id_obat
                WHERE rd.id_resep = $id_resep");

            while($stok = mysqli_fetch_assoc($cek_stok_query)) {
                if((int) $stok['stok'] < (int) $stok['jumlah']) {
                    throw new Exception("Stok obat " . $stok['nama_obat'] . " tidak cukup untuk diproses.");
                }
            }

            mysqli_query($connect, "UPDATE obat o
                JOIN detail_resep rd ON o.id_obat = rd.id_obat
                SET o.stok = o.stok - rd.jumlah
                WHERE rd.id_resep = $id_resep");

            $total_obat_query = mysqli_query($connect, "SELECT COALESCE(SUM(o.harga_jual * rd.jumlah), 0) AS total_obat
                FROM detail_resep rd
                JOIN obat o ON rd.id_obat = o.id_obat
                WHERE rd.id_resep = $id_resep");
            $total_obat = mysqli_fetch_assoc($total_obat_query);
            $biaya_obat = (int) ($total_obat['total_obat'] ?? 0);
            $biaya_konsultasi = 0;
            $total = $biaya_konsultasi + $biaya_obat;

            $tagihan_query = mysqli_query($connect, "SELECT id_tagihan FROM tagihan WHERE id_kunjungan = $id_kunjungan LIMIT 1");
            if(mysqli_num_rows($tagihan_query) == 0) {
                mysqli_query($connect, "INSERT INTO tagihan (id_kunjungan, biaya_konsultasi, biaya_obat, total, status_bayar)
                    VALUES ($id_kunjungan, $biaya_konsultasi, $biaya_obat, $total, 'belum')");
            } else {
                mysqli_query($connect, "UPDATE tagihan
                    SET biaya_konsultasi = $biaya_konsultasi, biaya_obat = $biaya_obat, total = $total
                    WHERE id_kunjungan = $id_kunjungan AND status_bayar = 'belum'");
            }

            mysqli_query($connect, "UPDATE resep SET status = 'selesai' WHERE id_resep = $id_resep");
            mysqli_commit($connect);
            $_SESSION['flash_success'] = "Resep berhasil diproses, stok berkurang, dan tagihan dibuat.";
        } catch (Throwable $e) {
            mysqli_rollback($connect);
            $_SESSION['flash_error'] = $e->getMessage();
        }
    } else {
        $_SESSION['flash_error'] = "Resep tidak ditemukan.";
    }

    header("Location: daftar_resep.php");
    exit();
}

// Ambil daftar resep
$query = "SELECT r.*, k.id_kunjungan, p.nama as nama_pasien, d.nama as nama_dokter,
          GROUP_CONCAT(CONCAT(o.nama_obat, ' (', rd.jumlah, ')') SEPARATOR ', ') as daftar_obat
          FROM resep r
          JOIN kunjungan k ON r.id_kunjungan = k.id_kunjungan
          JOIN pasien p ON k.id_pasien = p.id_pasien
          JOIN dokter d ON k.id_dokter = d.id_dokter
          JOIN detail_resep rd ON r.id_resep = rd.id_resep
          JOIN obat o ON rd.id_obat = o.id_obat
          GROUP BY r.id_resep
          ORDER BY r.tgl_resep DESC";
$result = mysqli_query($connect, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Resep - Apoteker</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="../../assets/style.css" rel="stylesheet">
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-header"><h1>🏥 Klinik Sehat</h1></div>
        <nav class="sidebar-nav">
            <a href="index.php">📊 Dashboard</a>
            <a href="daftar_resep.php" class="active">📋 Daftar Resep</a>
            <a href="stok_obat.php">📦 Stok Obat</a>
            <a href="tambah_obat.php">➕ Tambah Obat</a>
        </nav>
        <div class="sidebar-footer"><a href="../../logout.php">🚪 Logout</a></div>
    </aside>

    <main class="main-content">
        <header class="top-navbar">
            <span class="page-title">Daftar Resep Pasien</span>
            <a href="../../logout.php" class="btn-logout">Logout</a>
        </header>

        <div class="container">
            <div class="card">
                <div class="card-header">📋 Resep yang Perlu Diproses</div>
                <?php if($flash_success): ?>
                    <div style="background: #dcfce7; color: #166534; padding: 12px; border-radius: 12px; margin: 10px 0 16px;">
                        ✅ <?php echo $flash_success; ?>
                    </div>
                <?php endif; ?>
                <?php if($flash_error): ?>
                    <div style="background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 12px; margin: 10px 0 16px;">
                        ❌ <?php echo $flash_error; ?>
                    </div>
                <?php endif; ?>
                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Pasien</th>
                                <th>Dokter</th>
                                <th>Obat</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(mysqli_num_rows($result) > 0): ?>
                                <?php while($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td><?php echo $row['tgl_resep']; ?></td>
                                        <td><?php echo $row['nama_pasien']; ?></td>
                                        <td><?php echo $row['nama_dokter']; ?></td>
                                        <td><?php echo substr($row['daftar_obat'], 0, 50); ?>...</td>
                                        <td>
                                            <span class="badge badge-<?php echo $row['status'] == 'pending' ? 'warning' : 'success'; ?>">
                                                <?php echo $row['status'] == 'pending' ? 'Pending' : 'Selesai'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if($row['status'] == 'pending'): ?>
                                                <a href="?proses=<?php echo $row['id_resep']; ?>" class="btn btn-primary" style="padding: 5px 12px;" onclick="return confirm('Resep sudah disiapkan?')">✅ Proses</a>
                                            <?php else: ?>
                                                <span class="badge badge-success">Selesai</span>
                                            <?php endif; ?>
                                         </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="6" style="text-align: center;">Belum ada resep</td></tr>
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