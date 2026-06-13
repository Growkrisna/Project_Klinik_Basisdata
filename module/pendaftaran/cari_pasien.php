<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'pendaftaran') {
    header("Location: ../../login.php");
    exit();
}
include '../../config/db.php';

$hasil = [];
$keyword = '';

if(isset($_GET['cari'])) {
    $keyword = mysqli_real_escape_string($connect, $_GET['keyword']);
    $query = "SELECT * FROM pasien WHERE nomor_rm LIKE '%$keyword%' OR nama LIKE '%$keyword%' OR no_hp LIKE '%$keyword%'";
    $hasil = mysqli_query($connect, $query);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cari Pasien - Klinik Sehat</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="../../assets/style.css" rel="stylesheet">
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-header"><h1>🏥 Klinik Sehat</h1></div>
        <nav class="sidebar-nav">
            <a href="index.php">📊 Dashboard</a>
            <a href="tambah_pasien.php">📝 Registrasi Pasien</a>
            <a href="cari_pasien.php" class="active">🔍 Cari Pasien</a>
        </nav>
        <div class="sidebar-footer"><a href="../../logout.php">🚪 Logout</a></div>
    </aside>

    <main class="main-content">
        <header class="top-navbar">
            <span class="page-title">Cari Pasien</span>
            <a href="../../logout.php" class="btn-logout">Logout</a>
        </header>
        <div class="container">
            <div class="card">
                <div class="card-header">🔍 Cari Data Pasien</div>
                <form method="GET" style="display: flex; gap: 10px; margin-bottom: 20px;">
                    <input type="text" name="keyword" class="form-input" style="flex:1;" placeholder="Cari berdasarkan No RM / Nama / No HP" value="<?php echo htmlspecialchars($keyword); ?>">
                    <button type="submit" name="cari" class="btn btn-primary">🔍 Cari</button>
                </form>

                <?php if(isset($_GET['cari'])): ?>
                    <div class="table-wrapper">
                        <table class="table">
                            <thead>
                                <tr><th>No RM</th><th>Nama</th><th>No HP</th><th>Alamat</th><th>Aksi</th></tr>
                            </thead>
                            <tbody>
                                <?php if(mysqli_num_rows($hasil) > 0): ?>
                                    <?php while($row = mysqli_fetch_assoc($hasil)): ?>
                                        <tr>
                                            <td><?php echo $row['nomor_rm']; ?></td>
                                            <td><?php echo $row['nama']; ?></td>
                                            <td><?php echo $row['no_hp']; ?></td>
                                            <td><?php echo substr($row['alamat'], 0, 30); ?>...</td>
                                            <td><a href="buat_kunjungan.php?id=<?php echo $row['id_pasien']; ?>" class="btn btn-primary" style="padding: 5px 12px;">Pilih</a></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="5" style="text-align: center;">Data tidak ditemukan</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <footer class="footer"><p>© 2026 Klinik Sehat</p></footer>
    </main>
</body>
</html>