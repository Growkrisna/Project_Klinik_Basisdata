<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'apoteker') {
    header("Location: ../../login.php");
    exit();
}
include __DIR__ . '/../../config/db.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_obat = mysqli_real_escape_string($connect, $_POST['nama_obat']);
    $stok = $_POST['stok'];
    $satuan = mysqli_real_escape_string($connect, $_POST['satuan']);
    $harga_jual = $_POST['harga_jual'];
    $tgl_kadaluarsa = $_POST['tgl_kadaluarsa'];
    
    $query = "INSERT INTO obat (nama_obat, stok, satuan, harga_jual, tgl_kadaluarsa) 
              VALUES ('$nama_obat', $stok, '$satuan', $harga_jual, '$tgl_kadaluarsa')";
    
    if(mysqli_query($connect, $query)) {
        $success = "Obat berhasil ditambahkan!";
    } else {
        $error = "Gagal: " . mysqli_error($connect);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Obat - Apoteker</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="../../assets/style.css" rel="stylesheet">
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-header"><h1>🏥 Klinik Sehat</h1></div>
        <nav class="sidebar-nav">
            <a href="index.php">📊 Dashboard</a>
            <a href="daftar_resep.php">📋 Daftar Resep</a>
            <a href="stok_obat.php">📦 Stok Obat</a>
            <a href="tambah_obat.php" class="active">➕ Tambah Obat</a>
        </nav>
        <div class="sidebar-footer"><a href="../../logout.php">🚪 Logout</a></div>
    </aside>

    <main class="main-content">
        <header class="top-navbar">
            <span class="page-title">Tambah Obat Baru</span>
            <a href="../../logout.php" class="btn-logout">Logout</a>
        </header>

        <div class="container">
            <div class="card" style="max-width: 600px; margin: 0 auto;">
                <div class="card-header">➕ Form Tambah Obat</div>
                
                <?php if(isset($success)): ?>
                    <div style="background: #dcfce7; color: #166534; padding: 12px; border-radius: 12px; margin-bottom: 20px;">✅ <?php echo $success; ?></div>
                <?php endif; ?>
                <?php if(isset($error)): ?>
                    <div style="background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 12px; margin-bottom: 20px;">❌ <?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label class="form-label">Nama Obat *</label>
                        <input type="text" name="nama_obat" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Stok Awal</label>
                        <input type="number" name="stok" class="form-input" value="0" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Satuan</label>
                        <select name="satuan" class="form-select">
                            <option value="Tablet">Tablet</option>
                            <option value="Kapsul">Kapsul</option>
                            <option value="Sirup">Sirup</option>
                            <option value="Salep">Salep</option>
                            <option value="Botol">Botol</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Harga Jual (Rp)</label>
                        <input type="number" name="harga_jual" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tanggal Kadaluarsa</label>
                        <input type="date" name="tgl_kadaluarsa" class="form-input">
                    </div>
                    <button type="submit" class="btn btn-primary">💾 Simpan Obat</button>
                    <a href="stok_obat.php" class="btn btn-secondary">← Lihat Stok</a>
                </form>
            </div>
        </div>

        <footer class="footer"><p>© 2026 Klinik Sehat</p></footer>
    </main>
</body>
</html>