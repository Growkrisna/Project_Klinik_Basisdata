<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'pendaftaran') {
    header("Location: ../../login.php");
    exit();
}
include '../../config/db.php';

$id_pasien = $_GET['id'] ?? 0;
$pasien = null;
if($id_pasien) {
    $query = "SELECT * FROM pasien WHERE id_pasien = $id_pasien";
    $result = mysqli_query($connect, $query);
    $pasien = mysqli_fetch_assoc($result);
}

// Ambil daftar dokter
$dokter_query = "SELECT * FROM dokter";
$dokter_result = mysqli_query($connect, $dokter_query);

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_pasien = $_POST['id_pasien'];
    $id_dokter = $_POST['id_dokter'];
    $keluhan = mysqli_real_escape_string($connect, $_POST['keluhan']);
    
    $query = "INSERT INTO kunjungan (id_pasien, id_dokter, keluhan_utama, status_antrian) 
              VALUES ('$id_pasien', '$id_dokter', '$keluhan', 'menunggu')";
    
    if(mysqli_query($connect, $query)) {
        $no_antrian = mysqli_insert_id($connect);
        $success = "Kunjungan berhasil! Nomor Antrian: $no_antrian";
    } else {
        $error = "Gagal: " . mysqli_error($connect);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buat Kunjungan - Klinik Sehat</title>
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
            <a href="buat_kunjungan.php" class="active">🎫 Buat Kunjungan</a>
        </nav>
        <div class="sidebar-footer"><a href="../../logout.php">🚪 Logout</a></div>
    </aside>

    <main class="main-content">
        <header class="top-navbar">
            <span class="page-title">Buat Kunjungan & Antrian</span>
            <a href="../../logout.php" class="btn-logout">Logout</a>
        </header>
        <div class="container">
            <div class="card" style="max-width: 600px; margin: 0 auto;">
                <div class="card-header">🎫 Form Kunjungan</div>
                
                <?php if(isset($success)): ?>
                    <div style="background: #dcfce7; color: #166534; padding: 12px; border-radius: 12px; margin-bottom: 20px;">
                        ✅ <?php echo $success; ?>
                        <button onclick="window.print()" class="btn btn-primary" style="margin-left: 10px;">🖨️ Cetak Antrian</button>
                    </div>
                <?php endif; ?>
                <?php if(isset($error)): ?>
                    <div style="background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 12px; margin-bottom: 20px;">❌ <?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST">
                    <input type="hidden" name="id_pasien" value="<?php echo $id_pasien; ?>">
                    <div class="form-group">
                        <label class="form-label">Pasien</label>
                        <input type="text" class="form-input" value="<?php echo $pasien ? $pasien['nama'] : 'Silakan pilih pasien dari menu Cari Pasien'; ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Pilih Dokter</label>
                        <select name="id_dokter" class="form-select" required>
                            <option value="">-- Pilih Dokter --</option>
                            <?php while($dokter = mysqli_fetch_assoc($dokter_result)): ?>
                                <option value="<?php echo $dokter['id_dokter']; ?>"><?php echo $dokter['nama']; ?> - <?php echo $dokter['spesialis']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Keluhan Utama</label>
                        <textarea name="keluhan" class="form-textarea" rows="4" placeholder="Ceritakan keluhan pasien..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">💾 Simpan & Cetak Antrian</button>
                    <a href="cari_pasien.php" class="btn btn-secondary">← Cari Pasien Lain</a>
                </form>
            </div>
        </div>
        <footer class="footer"><p>© 2026 Klinik Sehat</p></footer>
    </main>
</body>
</html>