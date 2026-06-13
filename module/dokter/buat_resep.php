<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'dokter') {
    header("Location: ../../login.php");
    exit();
}
include '../../config/db.php';

$id_kunjungan = (int) ($_GET['id'] ?? 0);

// Ambil data pasien
$query = "SELECT k.*, p.nama as nama_pasien, p.nomor_rm 
          FROM kunjungan k 
          JOIN pasien p ON k.id_pasien = p.id_pasien 
          WHERE k.id_kunjungan = $id_kunjungan";
$result = mysqli_query($connect, $query);
$kunjungan = mysqli_fetch_assoc($result);

if(!$kunjungan) {
    header("Location: antrian.php");
    exit();
}

// Ambil daftar obat
$obat_query = "SELECT * FROM obat WHERE stok > 0";
$obat_result = mysqli_query($connect, $obat_query);

// Ambil resep yang sudah ada
$resep_query = "SELECT r.*, rd.*, o.nama_obat 
                FROM resep r 
                JOIN detail_resep rd ON r.id_resep = rd.id_resep 
                JOIN obat o ON rd.id_obat = o.id_obat 
                WHERE r.id_kunjungan = $id_kunjungan";
$resep_result = mysqli_query($connect, $resep_query);

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_obat = (int) $_POST['id_obat'];
    $jumlah = (int) $_POST['jumlah'];
    $aturan = mysqli_real_escape_string($connect, $_POST['aturan']);
    
    // Cek apakah sudah ada resep untuk kunjungan ini
    $cek_resep = mysqli_query($connect, "SELECT id_resep FROM resep WHERE id_kunjungan = $id_kunjungan");
    if(mysqli_num_rows($cek_resep) == 0) {
        mysqli_query($connect, "INSERT INTO resep (id_kunjungan, status) VALUES ($id_kunjungan, 'pending')");
        $id_resep = mysqli_insert_id($connect);
    } else {
        $row = mysqli_fetch_assoc($cek_resep);
        $id_resep = $row['id_resep'];
    }
    
    $query = "INSERT INTO detail_resep (id_resep, id_obat, jumlah, aturan_pakai) VALUES ($id_resep, $id_obat, $jumlah, '$aturan')";
    
    if(mysqli_query($connect, $query)) {
        $success = "Obat berhasil ditambahkan ke resep!";
        header("Location: buat_resep.php?id=$id_kunjungan");
        exit();
    } else {
        $error = "Gagal: " . mysqli_error($connect);
    }
}

if(isset($_GET['hapus'])) {
    $id_detail = $_GET['hapus'];
    mysqli_query($connect, "DELETE FROM detail_resep WHERE id_detail = $id_detail");
    header("Location: buat_resep.php?id=$id_kunjungan");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buat Resep - Dokter</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="../../assets/style.css" rel="stylesheet">
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-header"><h1>🏥 Klinik Sehat</h1></div>
        <nav class="sidebar-nav">
            <a href="index.php">📊 Dashboard</a>
            <a href="antrian.php">👥 Antrian Pasien</a>
            <a href="rekam_medis.php?id=<?php echo $id_kunjungan; ?>">📊 Rekam Medis</a>
            <a href="buat_resep.php?id=<?php echo $id_kunjungan; ?>" class="active">💊 Buat Resep</a>
        </nav>
        <div class="sidebar-footer"><a href="../../logout.php">🚪 Logout</a></div>
    </aside>

    <main class="main-content">
        <header class="top-navbar">
            <span class="page-title">Buat Resep Obat</span>
            <a href="../../logout.php" class="btn-logout">Logout</a>
        </header>

        <div class="container">
            <div class="card" style="max-width: 700px; margin: 0 auto;">
                <div class="card-header">💊 Resep untuk: <?php echo $kunjungan['nama_pasien']; ?></div>
                <div style="background: #f8fafc; padding: 12px 16px; border-radius: 12px; margin: 14px 0 20px 0;">
                    <p><strong>No RM:</strong> <?php echo $kunjungan['nomor_rm']; ?></p>
                    <p><strong>Keluhan:</strong> <?php echo $kunjungan['keluhan_utama']; ?></p>
                </div>

                <?php if(isset($success)): ?>
                    <div style="background: #dcfce7; color: #166534; padding: 12px; border-radius: 12px; margin-bottom: 20px;">✅ <?php echo $success; ?></div>
                <?php endif; ?>

                <!-- Daftar Obat yang Sudah di Resep -->
                <?php if(mysqli_num_rows($resep_result) > 0): ?>
                    <div style="margin-bottom: 20px;">
                        <h4 style="margin-bottom: 10px;">📋 Daftar Obat:</h4>
                        <table class="table">
                            <thead><tr><th>Obat</th><th>Jumlah</th><th>Aturan</th><th>Aksi</th></tr></thead>
                            <tbody>
                                <?php while($row = mysqli_fetch_assoc($resep_result)): ?>
                                    <tr>
                                        <td><?php echo $row['nama_obat']; ?></td>
                                        <td><?php echo $row['jumlah']; ?></td>
                                        <td><?php echo $row['aturan_pakai']; ?></td>
                                        <td><a href="?hapus=<?php echo $row['id_detail']; ?>&id=<?php echo $id_kunjungan; ?>" onclick="return confirm('Hapus?')">🗑️</a></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

                <!-- Form Tambah Obat -->
                <div class="card-header" style="margin-top: 20px;">➕ Tambah Obat</div>
                <form method="POST">
                    <div class="form-group">
                        <label class="form-label">Pilih Obat</label>
                        <select name="id_obat" class="form-select" required>
                            <option value="">-- Pilih Obat --</option>
                            <?php while($obat = mysqli_fetch_assoc($obat_result)): ?>
                                <option value="<?php echo $obat['id_obat']; ?>"><?php echo $obat['nama_obat']; ?> (Stok: <?php echo $obat['stok']; ?> - Rp <?php echo number_format($obat['harga_jual'],0,',','.'); ?>)</option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Jumlah</label>
                        <input type="number" name="jumlah" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Aturan Pakai</label>
                        <input type="text" name="aturan" class="form-input" placeholder="Contoh: 3x1 sehari sesudah makan" required>
                    </div>
                    <button type="submit" class="btn btn-primary">➕ Tambah ke Resep</button>
                    <a href="antrian.php" class="btn btn-secondary">← Selesai</a>
                </form>
            </div>
        </div>

        <footer class="footer"><p>© 2026 Klinik Sehat</p></footer>
    </main>
</body>
</html>