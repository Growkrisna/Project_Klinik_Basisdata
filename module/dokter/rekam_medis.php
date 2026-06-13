<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'dokter') {
    header("Location: ../../login.php");
    exit();
}
include '../../config/db.php';

$id_kunjungan = (int) ($_GET['id'] ?? 0);

// Ambil data pasien
$query = "SELECT k.*, p.nama as nama_pasien, p.nomor_rm, p.tgl_lahir, p.no_hp, d.nama as nama_dokter 
          FROM kunjungan k 
          JOIN pasien p ON k.id_pasien = p.id_pasien 
          JOIN dokter d ON k.id_dokter = d.id_dokter 
          WHERE k.id_kunjungan = $id_kunjungan";
$result = mysqli_query($connect, $query);
$kunjungan = mysqli_fetch_assoc($result);

if(!$kunjungan) {
    header("Location: antrian.php");
    exit();
}

// Cek apakah sudah ada rekam medis
$cek_rm = mysqli_query($connect, "SELECT * FROM rekam_medis WHERE id_kunjungan = $id_kunjungan");
$sudah_ada = mysqli_num_rows($cek_rm) > 0;
$rekam = mysqli_fetch_assoc($cek_rm);

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $anamnesis = mysqli_real_escape_string($connect, $_POST['anamnesis']);
    $diagnosis = mysqli_real_escape_string($connect, $_POST['diagnosis']);
    $tindakan = mysqli_real_escape_string($connect, $_POST['tindakan']);
    
    if($sudah_ada) {
        $query = "UPDATE rekam_medis SET anamnesis='$anamnesis', diagnosis='$diagnosis', tindakan='$tindakan' WHERE id_kunjungan=$id_kunjungan";
    } else {
        $query = "INSERT INTO rekam_medis (id_kunjungan, anamnesis, diagnosis, tindakan) VALUES ($id_kunjungan, '$anamnesis', '$diagnosis', '$tindakan')";
    }
    
    if(mysqli_query($connect, $query)) {
        $success = "Rekam medis berhasil disimpan!";
        // Update status kunjungan menjadi selesai
        mysqli_query($connect, "UPDATE kunjungan SET status_antrian = 'selesai' WHERE id_kunjungan = $id_kunjungan");
    } else {
        $error = "Gagal menyimpan: " . mysqli_error($connect);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekam Medis - Dokter</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="../../assets/style.css" rel="stylesheet">
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-header"><h1>🏥 Klinik Sehat</h1></div>
        <nav class="sidebar-nav">
            <a href="index.php">📊 Dashboard</a>
            <a href="antrian.php">👥 Antrian Pasien</a>
            <a href="rekam_medis.php?id=<?php echo $id_kunjungan; ?>" class="active">📊 Rekam Medis</a>
            <a href="buat_resep.php?id=<?php echo $id_kunjungan; ?>">💊 Buat Resep</a>
        </nav>
        <div class="sidebar-footer"><a href="../../logout.php">🚪 Logout</a></div>
    </aside>

    <main class="main-content">
        <header class="top-navbar">
            <span class="page-title">Rekam Medis Pasien</span>
            <a href="../../logout.php" class="btn-logout">Logout</a>
        </header>

        <div class="container">
            <div class="card" style="max-width: 700px; margin: 0 auto;">
                <div class="card-header">📋 Data Pasien</div>
                <div style="background: #f8fafc; padding: 16px; border-radius: 12px; margin-bottom: 20px;">
                    <p><strong>No RM:</strong> <?php echo $kunjungan['nomor_rm']; ?></p>
                    <p><strong>Nama:</strong> <?php echo $kunjungan['nama_pasien']; ?></p>
                    <p><strong>Keluhan:</strong> <?php echo $kunjungan['keluhan_utama']; ?></p>
                </div>

                <div class="card-header" style="margin-top: 20px;">📝 Form Rekam Medis</div>
                
                <?php if(isset($success)): ?>
                    <div style="background: #dcfce7; color: #166534; padding: 12px; border-radius: 12px; margin-bottom: 20px;">✅ <?php echo $success; ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label class="form-label">Anamnesis (Keluhan/Gejala)</label>
                        <textarea name="anamnesis" class="form-textarea" rows="4" required><?php echo $rekam['anamnesis'] ?? ''; ?></textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Diagnosis</label>
                        <input type="text" name="diagnosis" class="form-input" value="<?php echo $rekam['diagnosis'] ?? ''; ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tindakan / Terapi</label>
                        <textarea name="tindakan" class="form-textarea" rows="3"><?php echo $rekam['tindakan'] ?? ''; ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">💾 Simpan Rekam Medis</button>
                    <a href="antrian.php" class="btn btn-secondary">← Kembali ke Antrian</a>
                    <a href="buat_resep.php?id=<?php echo $id_kunjungan; ?>" class="btn btn-primary">💊 Buat Resep</a>
                </form>
            </div>
        </div>

        <footer class="footer"><p>© 2026 Klinik Sehat</p></footer>
    </main>
</body>
</html>