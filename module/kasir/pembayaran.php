<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'kasir') {
    header("Location: ../../login.php");
    exit();
}
include __DIR__ . '/../../config/db.php';

$id_tagihan = (int) ($_GET['id'] ?? 0);

// Ambil data tagihan
$query = "SELECT t.*, k.id_kunjungan, p.nama as nama_pasien, p.nomor_rm 
          FROM tagihan t
          JOIN kunjungan k ON t.id_kunjungan = k.id_kunjungan
          JOIN pasien p ON k.id_pasien = p.id_pasien
          WHERE t.id_tagihan = $id_tagihan AND t.status_bayar = 'belum'";
$result = mysqli_query($connect, $query);
$tagihan = mysqli_fetch_assoc($result);

if(!$tagihan) {
    header("Location: tagihan.php");
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $metode = mysqli_real_escape_string($connect, $_POST['metode']);
    $jumlah_bayar = (int) ($_POST['jumlah_bayar'] ?? 0);
    $total_tagihan = (int) $tagihan['total'];

    if($jumlah_bayar < $total_tagihan) {
        $error = "Jumlah bayar kurang dari total tagihan.";
    } else {
        $kembalian = $jumlah_bayar - $total_tagihan;

        // Simpan pembayaran sebesar nilai tagihan agar rekap pendapatan tidak berlebih.
        $query = "INSERT INTO pembayaran (id_tagihan, metode, jumlah_bayar) 
                  VALUES ($id_tagihan, '$metode', $total_tagihan)";

        if(mysqli_query($connect, $query)) {
        // Update status tagihan
            mysqli_query($connect, "UPDATE tagihan SET status_bayar = 'lunas' WHERE id_tagihan = $id_tagihan");
            $success = "Pembayaran berhasil!";

            // Ambil data untuk struk
            $struk_query = mysqli_query($connect, "SELECT p.*, t.*, ps.nama as nama_pasien 
                          FROM pembayaran p 
                          JOIN tagihan t ON p.id_tagihan = t.id_tagihan
                          JOIN kunjungan k ON t.id_kunjungan = k.id_kunjungan
                          JOIN pasien ps ON k.id_pasien = ps.id_pasien
                          WHERE p.id_tagihan = $id_tagihan");
            $struk = mysqli_fetch_assoc($struk_query);
            $struk['jumlah_diterima'] = $jumlah_bayar;
            $struk['kembalian'] = $kembalian;
        } else {
            $error = "Gagal: " . mysqli_error($connect);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Proses Pembayaran - Kasir</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="../../assets/style.css" rel="stylesheet">
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-header"><h1>🏥 Klinik Sehat</h1></div>
        <nav class="sidebar-nav">
            <a href="index.php">💰 Dashboard</a>
            <a href="tagihan.php">📋 Tagihan</a>
            <a href="pembayaran.php" class="active">💵 Pembayaran</a>
            <a href="riwayat.php">📜 Riwayat</a>
        </nav>
        <div class="sidebar-footer"><a href="../../logout.php">🚪 Logout</a></div>
    </aside>

    <main class="main-content">
        <header class="top-navbar">
            <span class="page-title">Proses Pembayaran</span>
            <a href="../../logout.php" class="btn-logout">Logout</a>
        </header>

        <div class="container">
            <div class="card" style="max-width: 600px; margin: 0 auto;">
                <div class="card-header">💰 Detail Tagihan</div>
                
                <?php if(isset($success)): ?>
                    <div style="background: #dcfce7; color: #166534; padding: 12px; border-radius: 12px; margin-bottom: 20px;">
                        ✅ <?php echo $success; ?>
                        <button onclick="window.print()" class="btn btn-primary" style="margin-left: 10px;">🖨️ Cetak Struk</button>
                    </div>
                    
                    <!-- Struk Pembayaran -->
                    <div id="struk" style="border: 1px dashed #ccc; padding: 20px; margin-top: 20px; text-align: center;">
                        <h3>KLINIK SEHAT</h3>
                        <p><?php echo date('d/m/Y H:i:s'); ?></p>
                        <hr>
                        <p><strong>No RM:</strong> <?php echo $struk['nomor_rm']; ?></p>
                        <p><strong>Pasien:</strong> <?php echo $struk['nama_pasien']; ?></p>
                        <hr>
                        <p>Biaya Konsultasi: Rp <?php echo number_format($struk['biaya_konsultasi'], 0, ',', '.'); ?></p>
                        <p>Biaya Obat: Rp <?php echo number_format($struk['biaya_obat'], 0, ',', '.'); ?></p>
                        <hr>
                        <p><strong>Total: Rp <?php echo number_format($struk['total'], 0, ',', '.'); ?></strong></p>
                        <p>Uang Diterima: Rp <?php echo number_format($struk['jumlah_diterima'], 0, ',', '.'); ?></p>
                        <p><strong>Kembalian: Rp <?php echo number_format($struk['kembalian'], 0, ',', '.'); ?></strong></p>
                        <p>Metode: <?php echo $struk['metode']; ?></p>
                        <hr>
                        <p>Terima kasih telah berobat di Klinik Sehat</p>
                    </div>
                    
                    <a href="tagihan.php" class="btn btn-primary" style="margin-top: 20px;">← Kembali ke Tagihan</a>
                <?php else: ?>
                    <?php if(isset($error)): ?>
                        <div style="background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 12px; margin-bottom: 20px;">
                            ❌ <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    <div style="background: #f8fafc; padding: 16px; border-radius: 12px; margin-bottom: 20px;">
                        <p><strong>No RM:</strong> <?php echo $tagihan['nomor_rm']; ?></p>
                        <p><strong>Nama Pasien:</strong> <?php echo $tagihan['nama_pasien']; ?></p>
                        <p><strong>Biaya Konsultasi:</strong> Rp <?php echo number_format($tagihan['biaya_konsultasi'], 0, ',', '.'); ?></p>
                        <p><strong>Biaya Obat:</strong> Rp <?php echo number_format($tagihan['biaya_obat'], 0, ',', '.'); ?></p>
                        <hr style="margin: 10px 0;">
                        <p><strong>Total Tagihan:</strong> Rp <?php echo number_format($tagihan['total'], 0, ',', '.'); ?></p>
                    </div>

                    <form method="POST">
                        <div class="form-group">
                            <label class="form-label">Metode Pembayaran</label>
                            <select name="metode" class="form-select" required>
                                <option value="Tunai">💵 Tunai</option>
                                <option value="Kartu Debit">💳 Kartu Debit</option>
                                <option value="Kartu Kredit">💳 Kartu Kredit</option>
                                <option value="Transfer Bank">🏦 Transfer Bank</option>
                                <option value="E-Wallet">📱 E-Wallet</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Uang Diterima</label>
                            <input type="number" name="jumlah_bayar" class="form-input" value="<?php echo $tagihan['total']; ?>" min="<?php echo $tagihan['total']; ?>" required>
                        </div>
                        <button type="submit" class="btn btn-primary">💰 Proses Pembayaran</button>
                        <a href="tagihan.php" class="btn btn-secondary">← Batal</a>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <footer class="footer"><p>© 2026 Klinik Sehat</p></footer>
    </main>
</body>
</html>