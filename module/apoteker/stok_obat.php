<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'apoteker') {
    header("Location: ../../login.php");
    exit();
}
include __DIR__ . '/../../config/db.php';

// Update stok
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_stok'])) {
    $id_obat = $_POST['id_obat'];
    $stok_baru = $_POST['stok'];
    mysqli_query($connect, "UPDATE obat SET stok = $stok_baru WHERE id_obat = $id_obat");
    $success = "Stok berhasil diupdate!";
}

// Ambil daftar obat
$query = "SELECT * FROM obat ORDER BY nama_obat ASC";
$result = mysqli_query($connect, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Stok Obat - Apoteker</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="../../assets/style.css" rel="stylesheet">
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-header"><h1>🏥 Klinik Sehat</h1></div>
        <nav class="sidebar-nav">
            <a href="index.php">📊 Dashboard</a>
            <a href="daftar_resep.php">📋 Daftar Resep</a>
            <a href="stok_obat.php" class="active">📦 Stok Obat</a>
            <a href="tambah_obat.php">➕ Tambah Obat</a>
        </nav>
        <div class="sidebar-footer"><a href="../../logout.php">🚪 Logout</a></div>
    </aside>

    <main class="main-content">
        <header class="top-navbar">
            <span class="page-title">Manajemen Stok Obat</span>
            <a href="../../logout.php" class="btn-logout">Logout</a>
        </header>

        <div class="container">
            <div class="card">
                <div class="card-header">📦 Daftar Obat</div>
                
                <?php if(isset($success)): ?>
                    <div style="background: #dcfce7; color: #166534; padding: 12px; border-radius: 12px; margin-bottom: 20px;">✅ <?php echo $success; ?></div>
                <?php endif; ?>

                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                            <tr><th>Nama Obat</th><th>Stok</th><th>Satuan</th><th>Harga Jual</th><th>Kadaluarsa</th><th>Aksi</th></tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="id_obat" value="<?php echo $row['id_obat']; ?>">
                                        <td><?php echo $row['nama_obat']; ?></td>
                                        <td>
                                            <input type="number" name="stok" value="<?php echo $row['stok']; ?>" style="width: 80px; padding: 5px;" required>
                                        </td>
                                        <td><?php echo $row['satuan'] ?? '-'; ?></td>
                                        <td>Rp <?php echo number_format($row['harga_jual'],0,',','.'); ?></td>
                                        <td><?php echo $row['tgl_kadaluarsa'] ?? '-'; ?></td>
                                        <td>
                                            <button type="submit" name="update_stok" class="btn btn-primary" style="padding: 5px 12px;">💾 Update</button>
                                        </td>
                                    </form>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <footer class="footer"><p>© 2026 Klinik Sehat</p></footer>
    </main>
</body>
</html>