<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'pendaftaran') {
    header("Location: ../../login.php");
    exit();
}
include '../../config/db.php';

$id_pasien = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Hapus pasien
if(isset($_GET['hapus'])) {
    $hapus_id = intval($_GET['hapus']);
    if(mysqli_query($connect, "DELETE FROM pasien WHERE id_pasien = $hapus_id")) {
        header("Location: edit_pasien.php?msg=hapus");
        exit();
    } else {
        $error = "Gagal menghapus: " . mysqli_error($connect);
    }
}

// Ambil data pasien untuk diedit
$pasien = null;
if($id_pasien) {
    $result = mysqli_query($connect, "SELECT * FROM pasien WHERE id_pasien = $id_pasien");
    $pasien = mysqli_fetch_assoc($result);
    if(!$pasien) {
        header("Location: edit_pasien.php");
        exit();
    }
}

// Proses update
if($_SERVER['REQUEST_METHOD'] == 'POST' && $id_pasien) {
    $nama      = mysqli_real_escape_string($connect, $_POST['nama']);
    $tgl_lahir = $_POST['tgl_lahir'];
    $alamat    = mysqli_real_escape_string($connect, $_POST['alamat']);
    $no_hp     = mysqli_real_escape_string($connect, $_POST['no_hp']);

    $query = "UPDATE pasien SET nama='$nama', tgl_lahir='$tgl_lahir', alamat='$alamat', no_hp='$no_hp'
              WHERE id_pasien = $id_pasien";

    if(mysqli_query($connect, $query)) {
        $success = "Data pasien berhasil diperbarui!";
        // Refresh data
        $result = mysqli_query($connect, "SELECT * FROM pasien WHERE id_pasien = $id_pasien");
        $pasien = mysqli_fetch_assoc($result);
    } else {
        $error = "Gagal memperbarui: " . mysqli_error($connect);
    }
}

// Ambil semua pasien untuk tabel
$semua_pasien = mysqli_query($connect, "SELECT * FROM pasien ORDER BY tgl_daftar DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Data Pasien - Klinik Sehat</title>
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
            <a href="edit_pasien.php" class="active">✏️ Kelola Pasien</a>
            <a href="antrian.php">📋 Antrian</a>
        </nav>
        <div class="sidebar-footer"><a href="../../logout.php">🚪 Logout</a></div>
    </aside>

    <main class="main-content">
        <header class="top-navbar">
            <span class="page-title">Kelola Data Pasien</span>
            <a href="../../logout.php" class="btn-logout">Logout</a>
        </header>

        <div class="container">

            <?php if(isset($_GET['msg']) && $_GET['msg'] == 'hapus'): ?>
                <div style="background:#dcfce7;color:#166534;padding:12px;border-radius:12px;margin-bottom:20px;">✅ Pasien berhasil dihapus.</div>
            <?php endif; ?>

            <!-- Form Edit (hanya tampil kalau ada ?id=) -->
            <?php if($pasien): ?>
            <div class="card" style="max-width:600px;margin:0 auto 28px auto;">
                <div class="card-header">✏️ Edit Data Pasien — <?php echo htmlspecialchars($pasien['nama']); ?></div>

                <?php if(isset($success)): ?>
                    <div style="background:#dcfce7;color:#166534;padding:12px;border-radius:12px;margin-bottom:20px;">✅ <?php echo $success; ?></div>
                <?php endif; ?>
                <?php if(isset($error)): ?>
                    <div style="background:#fee2e2;color:#991b1b;padding:12px;border-radius:12px;margin-bottom:20px;">❌ <?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label class="form-label">Nomor RM</label>
                        <input type="text" class="form-input" value="<?php echo htmlspecialchars($pasien['nomor_rm']); ?>" disabled style="background:#f1f5f9;">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nama Lengkap *</label>
                        <input type="text" name="nama" class="form-input" value="<?php echo htmlspecialchars($pasien['nama']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tanggal Lahir</label>
                        <input type="date" name="tgl_lahir" class="form-input" value="<?php echo $pasien['tgl_lahir']; ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Alamat</label>
                        <textarea name="alamat" class="form-textarea" rows="3"><?php echo htmlspecialchars($pasien['alamat']); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">No HP</label>
                        <input type="text" name="no_hp" class="form-input" value="<?php echo htmlspecialchars($pasien['no_hp']); ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">💾 Simpan Perubahan</button>
                    <a href="edit_pasien.php" class="btn btn-secondary">← Batal</a>
                </form>
            </div>
            <?php endif; ?>

            <!-- Tabel semua pasien -->
            <div class="card">
                <div class="card-header">👥 Daftar Semua Pasien</div>
                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                            <tr><th>No RM</th><th>Nama</th><th>No HP</th><th>Tgl Daftar</th><th>Aksi</th></tr>
                        </thead>
                        <tbody>
                            <?php if(mysqli_num_rows($semua_pasien) > 0): ?>
                                <?php while($row = mysqli_fetch_assoc($semua_pasien)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['nomor_rm']); ?></td>
                                        <td><?php echo htmlspecialchars($row['nama']); ?></td>
                                        <td><?php echo htmlspecialchars($row['no_hp']); ?></td>
                                        <td><?php echo $row['tgl_daftar']; ?></td>
                                        <td style="white-space:nowrap;">
                                            <a href="?id=<?php echo $row['id_pasien']; ?>" class="btn btn-primary" style="padding:5px 12px;">✏️ Edit</a>
                                            <a href="?hapus=<?php echo $row['id_pasien']; ?>"
                                               class="btn btn-danger" style="padding:5px 12px;"
                                               onclick="return confirm('Hapus pasien <?php echo htmlspecialchars($row['nama']); ?>? Data kunjungan terkait juga akan terhapus!')">
                                               🗑️ Hapus
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="5" style="text-align:center;">Belum ada data pasien</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
        <footer class="footer"><p>© 2026 Klinik Sehat - Final Project Basis Data</p></footer>
    </main>
</body>
</html>