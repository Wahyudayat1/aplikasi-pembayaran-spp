<?php 
include 'header.php'; 
include 'sidebar.php'; 

// 1. Process Edit/Update Pembayaran
if (isset($_POST['edit'])) {
    $id_pembayaran = $_POST['id_pembayaran'];
    $tgl_bayar = $_POST['tgl_bayar'];
    $bulan_dibayar = $_POST['bulan_dibayar'];
    $tahun_dibayar = $_POST['tahun_dibayar'];
    $jumlah_bayar = $_POST['jumlah_bayar'];

    $query = mysqli_query($koneksi, "UPDATE pembayaran SET 
        tgl_bayar='$tgl_bayar', 
        bulan_dibayar='$bulan_dibayar', 
        tahun_dibayar='$tahun_dibayar', 
        jumlah_bayar='$jumlah_bayar' 
        WHERE id_pembayaran='$id_pembayaran'");

    if ($query) {
        echo "<script>alert('Data pembayaran berhasil diperbarui!'); location.href='detail_pembayaran.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui data: " . mysqli_error($koneksi) . "');</script>";
    }
}

// 2. Process Hapus Pembayaran (Langsung Hapus dari tabel pembayaran saja)
if (isset($_GET['hapus'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['hapus']);
    $query = mysqli_query($koneksi, "DELETE FROM pembayaran WHERE id_pembayaran='$id'");
    
    if ($query) {
        echo "<script>alert('Data transaksi berhasil dihapus!'); location.href='detail_pembayaran.php';</script>";
        exit;
    } else {
        echo "<script>alert('Gagal menghapus data: " . mysqli_error($koneksi) . "'); location.href='detail_pembayaran.php';</script>";
        exit;
    }
}

// Fetch Data
$data_pembayaran = [];
$query = mysqli_query($koneksi, "SELECT p.*, s.nama, pt.nama_petugas FROM pembayaran p 
                                 JOIN siswa s ON p.nisn = s.nisn 
                                 JOIN petugas pt ON p.id_petugas = pt.id_petugas 
                                 ORDER BY p.id_pembayaran DESC");
while ($row = mysqli_fetch_array($query)) {
    $data_pembayaran[] = $row;
}
?>

<div class="col-md-10 p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="m-0">Detail Pembayaran</h4>
        <button class="btn btn-secondary btn-sm" onclick="window.print()"><i class="bi bi-printer me-1"></i> Cetak Laporan</button>
    </div>

    <div class="table-responsive bg-white p-3 shadow-sm rounded">
        <table class="table table-bordered table-striped align-middle m-0">
            <thead class="table-secondary">
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>NISN</th>
                    <th>Nama Siswa</th>
                    <th>Tanggal Bayar</th>
                    <th>Bulan / Tahun</th>
                    <th>Jumlah Bayar</th>
                    <th>Petugas</th>
                    <th class="text-center" style="width: 100px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!empty($data_pembayaran)) {
                    $no = 1;
                    foreach ($data_pembayaran as $d) {
                    ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= $d['nisn']; ?></td>
                        <td><?= $d['nama']; ?></td>
                        <td><?= $d['tgl_bayar']; ?></td>
                        <td><?= $d['bulan_dibayar'] . " " . $d['tahun_dibayar']; ?></td>
                        <td>Rp <?= number_format($d['jumlah_bayar'], 0, ',', '.'); ?></td>
                        <td><?= $d['nama_petugas']; ?></td>
                        <td class="text-center text-nowrap">
                            <button class="btn btn-warning btn-sm p-1 px-2" data-bs-toggle="modal" data-bs-target="#modalEditPembayaran<?= $d['id_pembayaran']; ?>" title="Edit">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <a href="detail_pembayaran.php?hapus=<?= $d['id_pembayaran']; ?>" class="btn btn-danger btn-sm p-1 px-2" onclick="return confirm('Hapus transaksi ini?')" title="Hapus">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php 
                    }
                } else {
                    echo "<tr><td colspan='8' class='text-center text-muted'>Belum ada data pembayaran.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL EDIT PEMBAYARAN -->
<?php foreach ($data_pembayaran as $d) { ?>
<div class="modal fade" id="modalEditPembayaran<?= $d['id_pembayaran']; ?>" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form action="detail_pembayaran.php" method="POST" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Transaksi Pembayaran</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id_pembayaran" value="<?= $d['id_pembayaran']; ?>">
        
        <div class="mb-3">
            <label class="form-label">Nama Siswa / NISN</label>
            <input type="text" class="form-control" value="<?= $d['nisn'] . ' - ' . $d['nama']; ?>" readonly>
        </div>
        <div class="mb-3">
            <label class="form-label">Tanggal Bayar</label>
            <input type="date" name="tgl_bayar" class="form-control" value="<?= $d['tgl_bayar']; ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Bulan Dibayar</label>
            <select name="bulan_dibayar" class="form-select" required>
                <?php
                $bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                foreach ($bulan as $b) {
                    $selected = ($d['bulan_dibayar'] == $b) ? 'selected' : '';
                    echo "<option value='$b' $selected>$b</option>";
                }
                ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Tahun Dibayar</label>
            <input type="text" name="tahun_dibayar" class="form-control" value="<?= $d['tahun_dibayar']; ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Jumlah Bayar (Rp)</label>
            <input type="number" name="jumlah_bayar" class="form-control" value="<?= $d['jumlah_bayar']; ?>" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" name="edit" class="btn btn-warning">Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>
<?php } ?>

<?php include 'footer.php'; ?>