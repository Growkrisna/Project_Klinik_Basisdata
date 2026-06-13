<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'apoteker') {
    header("Location: ../../login.php");
    exit();
}
include __DIR__ . '/../../config/db.php';

$id_obat = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Hapus obat
if(isset($_GET['hapus'])) {
    $hapus_id = intval($_GET['hapus']);
    if(mysqli_query($connect, "DELETE FROM obat WHERE id_obat = $hapus_id")) {
        header("Location: kelola_obat.php?msg=hapus");
        exit();
    } else {
        $error_hapus = "Gagal menghapus obat (mungkin sudah dipakai di resep).";
    }
}

// Ambil data obat untuk edit
$obat = null;
if($id_obat) {
    $result = mysqli_query($connect, "SELECT * FROM obat WHERE id_obat = $id_obat");
    $obat = mysqli_fetch_assoc($result);
    if(!$obat) {
        header("Location: kelola_obat.php");
        exit();
    }
}

// Proses update
if($_SERVER['REQUEST_METHOD'] == 'POST' && $id_obat) {
    $nama_obat      = mysqli_real_escape_string($connect, $_POST['nama_obat']);
    $stok           = intval($_POST['stok']);
    $satuan         = mysqli_real_escape_string($connect, $_POST['satuan']);
    $harga_jual     = floatval($_POST['harga_jual']);
    $tgl_kadaluarsa = $_POST['tgl_kadaluarsa'];

    $query = "UPDATE obat SET nama_obat='$nama_obat', stok=$stok, satuan='$satuan',
              harga_jual=$harga_jual, tgl_kadaluarsa='$tgl_kadaluarsa'
              WHERE id_obat = $id_obat";

    if(mysqli_query($connect, $query)) {
        $success = "Data obat berhasil diperbarui!";
        $result = mysqli_query($connect, "SELECT * FROM obat WHERE id_obat = $id_obat");
        $obat = mysqli_fetch_assoc($result);
    } else {
        $error = "Gagal: " . mysqli_error($connect);
    }
}

// Ambil semua obat
$semua_obat = mysqli_query($connect, "SELECT * FROM obat ORDER BY nama_obat ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Obat - Klinik Sehat</title>
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
            <a href="tambah_obat.php">➕ Tambah Obat</a>
            <a href="kelola_obat.php" class="active">✏️ Kelola Obat</a>
        </nav>
        <div class="sidebar-footer"><a href="../../logout.php">🚪 Logout</a></div>
    </aside>

    <main class="main-content">
        <header class="top-navbar">
            <span class="page-title">Kelola Data Obat</span>
            <a href="../../logout.php" class="btn-logout">Logout</a>
        </header>

        <div class="container">

            <?php if(isset($_GET['msg']) && $_GET['msg'] == 'hapus'): ?>
                <div style="background:#dcfce7;color:#166534;padding:12px;border-radius:12px;margin-bottom:20px;">✅ Obat berhasil dihapus.</div>
            <?php endif; ?>
            <?php if(isset($error_hapus)): ?>
                <div style="background:#fee2e2;color:#991b1b;padding:12px;border-radius:12px;margin-bottom:20px;">❌ <?php echo $error_hapus; ?></div>
            <?php endif; ?>

            <!-- Form Edit (hanya tampil kalau ada ?id=) -->
            <?php if($obat): ?>
            <div class="card" style="max-width:600px;margin:0 auto 28px auto;">
                <div class="card-header">✏️ Edit Obat — <?php echo htmlspecialchars($obat['nama_obat']); ?></div>

                <?php if(isset($success)): ?>
                    <div style="background:#dcfce7;color:#166534;padding:12px;border-radius:12px;margin-bottom:20px;">✅ <?php echo $success; ?></div>
                <?php endif; ?>
                <?php if(isset($error)): ?>
                    <div style="background:#fee2e2;color:#991b1b;padding:12px;border-radius:12px;margin-bottom:20px;">❌ <?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label class="form-label">Nama Obat *</label>
                        <input type="text" name="nama_obat" class="form-input" value="<?php echo htmlspecialchars($obat['nama_obat']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Stok</label>
                        <input type="number" name="stok" class="form-input" value="<?php echo $obat['stok']; ?>" min="0" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Satuan</label>
                        <select name="satuan" class="form-select">
                            <?php foreach(['Tablet','Kapsul','Sirup','Salep','Botol'] as $s): ?>
                                <option value="<?php echo $s; ?>" <?php echo $obat['satuan'] == $s ? 'selected' : ''; ?>><?php echo $s; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Harga Jual (Rp)</label>
                        <input type="number" name="harga_jual" class="form-input" value="<?php echo $obat['harga_jual']; ?>" min="0" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tanggal Kadaluarsa</label>
                        <input type="date" name="tgl_kadaluarsa" class="form-input" value="<?php echo $obat['tgl_kadaluarsa']; ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">💾 Simpan Perubahan</button>
                    <a href="kelola_obat.php" class="btn btn-secondary">← Batal</a>
                </form>
            </div>
            <?php endif; ?>

            <!-- Tabel semua obat -->
            <div class="card">
                <div class="card-header">💊 Daftar Semua Obat</div>
                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                            <tr><th>Nama Obat</th><th>Stok</th><th>Satuan</th><th>Harga Jual</th><th>Kadaluarsa</th><th>Aksi</th></tr>
                        </thead>
                        <tbody>
                            <?php if(mysqli_num_rows($semua_obat) > 0): ?>
                                <?php while($row = mysqli_fetch_assoc($semua_obat)): ?>
                                    <tr style="<?php echo $row['stok'] < 10 ? 'background:#fff7ed;' : ''; ?>">
                                        <td><?php echo htmlspecialchars($row['nama_obat']); ?></td>
                                        <td>
                                            <?php if($row['stok'] < 10): ?>
                                                <span class="badge badge-danger"><?php echo $row['stok']; ?></span>
                                            <?php else: ?>
                                                <?php echo $row['stok']; ?>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $row['satuan']; ?></td>
                                        <td>Rp <?php echo number_format($row['harga_jual'],0,',','.'); ?></td>
                                        <td><?php echo $row['tgl_kadaluarsa'] ?? '-'; ?></td>
                                        <td style="white-space:nowrap;">
                                            <a href="?id=<?php echo $row['id_obat']; ?>" class="btn btn-primary" style="padding:5px 12px;">✏️ Edit</a>
                                            <a href="?hapus=<?php echo $row['id_obat']; ?>"
                                               class="btn btn-danger" style="padding:5px 12px;"
                                               onclick="return confirm('Hapus <?php echo htmlspecialchars($row['nama_obat']); ?>?')">
                                               🗑️ Hapus
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="6" style="text-align:center;">Belum ada data obat</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <p style="font-size:0.75rem;color:#94a3b8;margin-top:12px;">🟠 Baris oranye = stok menipis (< 10)</p>
            </div>

        </div>
        <footer class="footer"><p>© 2026 Klinik Sehat - Final Project Basis Data</p></footer>
    </main>
</body>
</html>