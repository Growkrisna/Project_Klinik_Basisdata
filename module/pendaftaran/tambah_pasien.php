<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'pendaftaran') {
    header("Location: ../../login.php");
    exit();
}
include '../../config/db.php';

// Generate nomor RM otomatis
$query_rm = "SELECT MAX(CAST(SUBSTRING(nomor_rm, 3) AS UNSIGNED)) as max_rm FROM pasien";
$result_rm = mysqli_query($connect, $query_rm);
$row_rm = mysqli_fetch_assoc($result_rm);
$new_rm_number = ($row_rm['max_rm'] ?? 0) + 1;
$nomor_rm = "RM" . str_pad($new_rm_number, 5, '0', STR_PAD_LEFT);

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = mysqli_real_escape_string($connect, $_POST['nama']);
    $tgl_lahir = $_POST['tgl_lahir'];
    $alamat = mysqli_real_escape_string($connect, $_POST['alamat']);
    $no_hp = $_POST['no_hp'];
    
    $query = "INSERT INTO pasien (nomor_rm, nama, tgl_lahir, alamat, no_hp) 
              VALUES ('$nomor_rm', '$nama', '$tgl_lahir', '$alamat', '$no_hp')";
    
    if(mysqli_query($connect, $query)) {
        $success = "Pasien berhasil didaftarkan! Nomor RM: $nomor_rm";
    } else {
        $error = "Gagal mendaftarkan pasien: " . mysqli_error($connect);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Pasien - Klinik Sehat</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="../../assets/style.css" rel="stylesheet">
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-header"><h1>🏥 Klinik Sehat</h1></div>
        <nav class="sidebar-nav">
            <a href="index.php">📊 Dashboard</a>
            <a href="tambah_pasien.php" class="active">📝 Registrasi Pasien</a>
            <a href="cari_pasien.php">🔍 Cari Pasien</a>
        </nav>
        <div class="sidebar-footer"><a href="../../logout.php">🚪 Logout</a></div>
    </aside>

    <main class="main-content">
        <header class="top-navbar">
            <span class="page-title">Registrasi Pasien Baru</span>
            <a href="../../logout.php" class="btn-logout">Logout</a>
        </header>
        <div class="container">
            <div class="card" style="max-width: 600px; margin: 0 auto;">
                <div class="card-header">📝 Form Registrasi Pasien</div>
                
                <?php if(isset($success)): ?>
                    <div style="background: #dcfce7; color: #166534; padding: 12px; border-radius: 12px; margin-bottom: 20px;">✅ <?php echo $success; ?></div>
                <?php endif; ?>
                <?php if(isset($error)): ?>
                    <div style="background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 12px; margin-bottom: 20px;">❌ <?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label class="form-label">Nomor Rekam Medis (Otomatis)</label>
                        <input type="text" class="form-input" value="<?php echo $nomor_rm; ?>" disabled style="background:#f1f5f9;">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nama Lengkap *</label>
                        <input type="text" name="nama" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tanggal Lahir</label>
                        <input type="date" name="tgl_lahir" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Alamat</label>
                        <textarea name="alamat" class="form-textarea" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">No HP</label>
                        <input type="text" name="no_hp" class="form-input">
                    </div>
                    <button type="submit" class="btn btn-primary">💾 Simpan Pasien</button>
                    <a href="index.php" class="btn btn-secondary">← Kembali</a>
                </form>
            </div>
        </div>
        <footer class="footer"><p>© 2026 Klinik Sehat</p></footer>
    </main>
</body>
</html>