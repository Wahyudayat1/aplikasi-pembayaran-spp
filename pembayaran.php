<?php 
include 'header.php'; 
include 'sidebar.php'; 

if (isset($_POST['bayar'])) {
    $id_petugas = $_POST['id_petugas'];
    $nisn = $_POST['nisn'];
    $tgl_bayar = $_POST['tgl_bayar'];
    $bulan_dibayar = $_POST['bulan_dibayar'];
    $tahun_dibayar = $_POST['tahun_dibayar'];
    $id_spp = $_POST['id_spp'];
    $jumlah_bayar = $_POST['jumlah_bayar'];

    $simpan = mysqli_query($koneksi, "INSERT INTO pembayaran VALUES (NULL, '$id_petugas', '$nisn', '$tgl_bayar', '$bulan_dibayar', '$tahun_dibayar', '$id_spp', '$jumlah_bayar')");
    
    if ($simpan) {
        $id_baru = mysqli_insert_id($koneksi);
        mysqli_query($koneksi, "INSERT INTO cek_pembayaran (id_pembayaran, status_verifikasi) VALUES ('$id_baru', 'Lunas')");
        echo "<script>alert('Transaksi Pembayaran Berhasil Ditambahkan!'); location.href='pembayaran.php';</script>";
    }
}
?>

<div class="col-md-10 p-4">
    <h4>Transaksi Pembayaran SPP</h4>
    <div class="card p-4 shadow-sm border-0 bg-white mt-3">
        <form method="POST">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Pilih Siswa</label>
                    <select name="nisn" class="form-select" required>
                        <option value="">-- Pilih Siswa --</option>
                        <?php
                        $q_siswa = mysqli_query($koneksi, "SELECT * FROM siswa");
                        while ($s = mysqli_fetch_array($q_siswa)) {
                            echo "<option value='".$s['nisn']."'>".$s['nisn']." - ".$s['nama']."</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Petugas Kasir</label>
                    <select name="id_petugas" class="form-select" required>
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
                        <option value="Januari">Januari</option>
                        <option value="Februari">Februari</option>
                        <option value="Maret">Maret</option>
                        <option value="April">April</option>
                        <option value="Mei">Mei</option>
                        <option value="Juni">Juni</option>
                        <option value="Juli">Juli</option>
                        <option value="Agustus">Agustus</option>
                        <option value="September">September</option>
                        <option value="Oktober">Oktober</option>
                        <option value="November">November</option>
                        <option value="Desember">Desember</option>
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