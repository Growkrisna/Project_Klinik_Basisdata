<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'pendaftaran') {
    header("Location: ../../login.php");
    exit();
}
include '../../config/db.php';

$id_dokter = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Hapus dokter
if(isset($_GET['hapus'])) {
    $hapus_id = intval($_GET['hapus']);
    if(mysqli_query($connect, "DELETE FROM dokter WHERE id_dokter = $hapus_id")) {
        header("Location: kelola_dokter.php?msg=hapus");
        exit();
    } else {
        $error_hapus = "Gagal menghapus dokter (mungkin masih ada kunjungan terkait).";
    }
}

// Ambil data dokter untuk edit
$dokter = null;
if($id_dokter) {
    $result = mysqli_query($connect, "SELECT * FROM dokter WHERE id_dokter = $id_dokter");
    $dokter = mysqli_fetch_assoc($result);
}

// Proses tambah atau update
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama      = mysqli_real_escape_string($connect, $_POST['nama']);
    $spesialis = mysqli_real_escape_string($connect, $_POST['spesialis']);

    if($id_dokter) {
        // Update
        $query = "UPDATE dokter SET nama='$nama', spesialis='$spesialis' WHERE id_dokter = $id_dokter";
        $action_msg = "diperbarui";
    } else {
        // Insert
        $query = "INSERT INTO dokter (nama, spesialis) VALUES ('$nama', '$spesialis')";
        $action_msg = "ditambahkan";
    }

    if(mysqli_query($connect, $query)) {
        $success = "Dokter berhasil $action_msg!";
        if($id_dokter) {
            $result = mysqli_query($connect, "SELECT * FROM dokter WHERE id_dokter = $id_dokter");
            $dokter = mysqli_fetch_assoc($result);
        }
        $id_dokter = 0; // Reset form setelah insert
        $dokter = null;
    } else {
        $error = "Gagal: " . mysqli_error($connect);
    }
}

// Ambil semua dokter
$semua_dokter = mysqli_query($connect, "SELECT * FROM dokter ORDER BY nama ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Dokter - Klinik Sehat</title>
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
            <a href="edit_pasien.php">✏️ Kelola Pasien</a>
            <a href="kelola_dokter.php" class="active">👨‍⚕️ Kelola Dokter</a>
            <a href="antrian.php">📋 Antrian</a>
        </nav>
        <div class="sidebar-footer"><a href="../../logout.php">🚪 Logout</a></div>
    </aside>

    <main class="main-content">
        <header class="top-navbar">
            <span class="page-title">Kelola Data Dokter</span>
            <a href="../../logout.php" class="btn-logout">Logout</a>
        </header>

        <div class="container">

            <?php if(isset($_GET['msg']) && $_GET['msg'] == 'hapus'): ?>
                <div style="background:#dcfce7;color:#166534;padding:12px;border-radius:12px;margin-bottom:20px;">✅ Dokter berhasil dihapus.</div>
            <?php endif; ?>
            <?php if(isset($error_hapus)): ?>
                <div style="background:#fee2e2;color:#991b1b;padding:12px;border-radius:12px;margin-bottom:20px;">❌ <?php echo $error_hapus; ?></div>
            <?php endif; ?>

            <!-- Form Tambah / Edit -->
            <div class="card" style="max-width:560px;margin:0 auto 28px auto;">
                <div class="card-header"><?php echo $dokter ? '✏️ Edit Dokter' : '➕ Tambah Dokter Baru'; ?></div>

                <?php if(isset($success)): ?>
                    <div style="background:#dcfce7;color:#166534;padding:12px;border-radius:12px;margin-bottom:20px;">✅ <?php echo $success; ?></div>
                <?php endif; ?>
                <?php if(isset($error)): ?>
                    <div style="background:#fee2e2;color:#991b1b;padding:12px;border-radius:12px;margin-bottom:20px;">❌ <?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST" action="kelola_dokter.php<?php echo $id_dokter ? "?id=$id_dokter" : ''; ?>">
                    <div class="form-group">
                        <label class="form-label">Nama Dokter *</label>
                        <input type="text" name="nama" class="form-input"
                               value="<?php echo htmlspecialchars($dokter['nama'] ?? ''); ?>"
                               placeholder="Contoh: dr. Budi Santoso" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Spesialis</label>
                        <select name="spesialis" class="form-select">
                            <?php
                            $spesialis_list = ['Umum','Anak','Penyakit Dalam','Gigi','Kandungan','Bedah','Mata','THT','Kulit','Jantung'];
                            foreach($spesialis_list as $sp):
                                $selected = (isset($dokter['spesialis']) && $dokter['spesialis'] == $sp) ? 'selected' : '';
                            ?>
                                <option value="<?php echo $sp; ?>" <?php echo $selected; ?>><?php echo $sp; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">💾 <?php echo $dokter ? 'Simpan Perubahan' : 'Tambah Dokter'; ?></button>
                    <?php if($dokter): ?>
                        <a href="kelola_dokter.php" class="btn btn-secondary">← Batal</a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Tabel semua dokter -->
            <div class="card">
                <div class="card-header">👨‍⚕️ Daftar Dokter</div>
                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                            <tr><th>ID</th><th>Nama Dokter</th><th>Spesialis</th><th>Aksi</th></tr>
                        </thead>
                        <tbody>
                            <?php if(mysqli_num_rows($semua_dokter) > 0): ?>
                                <?php while($row = mysqli_fetch_assoc($semua_dokter)): ?>
                                    <tr>
                                        <td><?php echo $row['id_dokter']; ?></td>
                                        <td><?php echo htmlspecialchars($row['nama']); ?></td>
                                        <td><?php echo htmlspecialchars($row['spesialis']); ?></td>
                                        <td style="white-space:nowrap;">
                                            <a href="?id=<?php echo $row['id_dokter']; ?>" class="btn btn-primary" style="padding:5px 12px;">✏️ Edit</a>
                                            <a href="?hapus=<?php echo $row['id_dokter']; ?>"
                                               class="btn btn-danger" style="padding:5px 12px;"
                                               onclick="return confirm('Hapus dr. <?php echo htmlspecialchars($row['nama']); ?>?')">
                                               🗑️ Hapus
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="4" style="text-align:center;">Belum ada dokter</td></tr>
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