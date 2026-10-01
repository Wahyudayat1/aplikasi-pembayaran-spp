<?php 
include 'header.php'; 
include 'sidebar.php'; 

// Ambil NISN dari URL jika dikirim dari halaman cek_pembayaran.php
$nisn_pilihan = isset($_GET['nisn']) ? htmlspecialchars($_GET['nisn']) : '';

if (isset($_POST['bayar'])) {
    $id_petugas = $_POST['id_petugas'];
    $nisn = $_POST['nisn'];
    $tgl_bayar = $_POST['tgl_bayar'];
    $bulan_dibayar = $_POST['bulan_dibayar'];
    $tahun_dibayar = $_POST['tahun_dibayar'];
    $id_spp = $_POST['id_spp'];
    $jumlah_bayar = $_POST['jumlah_bayar'];

    if (empty($id_petugas) || empty($nisn) || empty($jumlah_bayar)) {
        echo "<script>alert('Gagal! Harap lengkapi seluruh form.'); location.href='pembayaran.php';</script>";
    } else {
        // --- VALIDASI CEK DUPLIKAT PEMBAYARAN ---
        $cek_duplikat = mysqli_query($koneksi, "SELECT * FROM pembayaran WHERE nisn = '$nisn' AND bulan_dibayar = '$bulan_dibayar' AND tahun_dibayar = '$tahun_dibayar'");
        
        if (mysqli_num_rows($cek_duplikat) > 0) {
            // Jika sudah pernah bayar pada bulan dan tahun yang sama
            echo "<script>alert('Gagal! Siswa dengan NISN $nisn SUDAH MEMBAYAR SPP untuk bulan $bulan_dibayar $tahun_dibayar.'); window.history.back();</script>";
        } else {
            // Jika belum bayar, proses simpan transaksi
            $simpan = mysqli_query($koneksi, "INSERT INTO pembayaran VALUES (NULL, '$id_petugas', '$nisn', '$tgl_bayar', '$bulan_dibayar', '$tahun_dibayar', '$id_spp', '$jumlah_bayar')");
            
            if ($simpan) {
                echo "<script>alert('Transaksi Pembayaran Berhasil Ditambahkan!'); location.href='detail_pembayaran.php';</script>";
            } else {
                echo "<script>alert('Gagal menyimpan transaksi: " . mysqli_error($koneksi) . "');</script>";
            }
        }
    }
}
?>

<div class="col-md-10 p-4">
    <h4>Transaksi Pembayaran SPP</h4>
    <div class="card p-4 shadow-sm border-0 bg-white mt-3">
        <form method="POST" action="pembayaran.php">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Pilih Siswa</label>
                    <select name="nisn" class="form-select" required>
                        <option value="">-- Pilih Siswa --</option>
                        <?php
                        $q_siswa = mysqli_query($koneksi, "SELECT * FROM siswa ORDER BY nama ASC");
                        while ($s = mysqli_fetch_array($q_siswa)) {
                            $selected = ($s['nisn'] == $nisn_pilihan) ? 'selected' : '';
                            echo "<option value='".$s['nisn']."' $selected>".$s['nisn']." - ".$s['nama']."</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Petugas Kasir</label>
                    <select name="id_petugas" class="form-select" required>
                        <option value="">-- Pilih Petugas --</option>
                        <?php
                        $q_petugas = mysqli_query($koneksi, "SELECT * FROM petugas");
                        while ($p = mysqli_fetch_array($q_petugas)) {
                            echo "<option value='".$p['id_petugas']."'>".$p['nama_petugas']."</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Tanggal Bayar</label>
                    <input type="date" name="tgl_bayar" class="form-control" value="<?= date('Y-m-d'); ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Bulan Dibayar</label>
                    <select name="bulan_dibayar" class="form-select" required>
                        <?php 
                        $bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                        foreach ($bulan as $b) {
                            echo "<option value='$b'>$b</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Tahun Dibayar</label>
                    <input type="text" name="tahun_dibayar" class="form-control" value="<?= date('Y'); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Tarif SPP</label>
                    <select name="id_spp" class="form-select" required>
                        <?php
                        $q_spp = mysqli_query($koneksi, "SELECT * FROM spp");
                        while ($sp = mysqli_fetch_array($q_spp)) {
                            echo "<option value='".$sp['id_spp']."'>Tahun ".$sp['tahun']." - Rp ".number_format($sp['nominal'], 0, ',', '.')."</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Jumlah Bayar (Rp)</label>
                    <input type="number" name="jumlah_bayar" class="form-control" placeholder="Nominal Uang" required>
                </div>
            </div>
            <button type="submit" name="bayar" class="btn btn-success px-4"><i class="bi bi-cash me-1"></i> Simpan Pembayaran</button>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>