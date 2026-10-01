<?php 
include 'header.php'; 
include 'sidebar.php'; 

// 1. PROSES TAMBAH SISWA
if (isset($_POST['tambah'])) {
    $nisn     = mysqli_real_escape_string($koneksi, trim($_POST['nisn']));
    $nis      = mysqli_real_escape_string($koneksi, trim($_POST['nis']));
    $nama     = mysqli_real_escape_string($koneksi, trim($_POST['nama']));
    $id_kelas = mysqli_real_escape_string($koneksi, $_POST['id_kelas']);
    $alamat   = mysqli_real_escape_string($koneksi, trim($_POST['alamat']));
    $no_telp  = mysqli_real_escape_string($koneksi, trim($_POST['no_telp']));
    $id_spp   = mysqli_real_escape_string($koneksi, $_POST['id_spp']);

    // Cek apakah NISN atau NIS sudah dipakai siswa lain
    $cek_duplikat = mysqli_query($koneksi, "SELECT * FROM siswa WHERE nisn = '$nisn' OR nis = '$nis'");
    
    if (mysqli_num_rows($cek_duplikat) > 0) {
        $d_ada = mysqli_fetch_array($cek_duplikat);
        if ($d_ada['nisn'] == $nisn) {
            echo "<script>alert('Gagal! NISN \"$nisn\" sudah terdaftar untuk siswa lain.'); window.history.back();</script>";
        } else {
            echo "<script>alert('Gagal! NIS \"$nis\" sudah digunakan oleh siswa \"".$d_ada['nama']."\".'); window.history.back();</script>";
        }
    } else {
        $simpan = mysqli_query($koneksi, "INSERT INTO siswa (nisn, nis, nama, id_kelas, alamat, no_telp, id_spp) VALUES ('$nisn', '$nis', '$nama', '$id_kelas', '$alamat', '$no_telp', '$id_spp')");
        
        if ($simpan) {
            echo "<script>alert('Data siswa berhasil ditambahkan!'); location.href='data_siswa.php';</script>";
        } else {
            echo "<script>alert('Gagal menyimpan data: " . mysqli_error($koneksi) . "');</script>";
        }
    }
}

// 2. PROSES EDIT SISWA
if (isset($_POST['edit'])) {
    $nisn     = mysqli_real_escape_string($koneksi, trim($_POST['nisn']));
    $nis      = mysqli_real_escape_string($koneksi, trim($_POST['nis']));
    $nama     = mysqli_real_escape_string($koneksi, trim($_POST['nama']));
    $id_kelas = mysqli_real_escape_string($koneksi, $_POST['id_kelas']);
    $alamat   = mysqli_real_escape_string($koneksi, trim($_POST['alamat']));
    $no_telp  = mysqli_real_escape_string($koneksi, trim($_POST['no_telp']));
    $id_spp   = mysqli_real_escape_string($koneksi, $_POST['id_spp']);

    // Cek apakah NIS sudah dipakai oleh siswa LAIN (selain NISN ini)
    $cek_nis_lain = mysqli_query($koneksi, "SELECT * FROM siswa WHERE nis = '$nis' AND nisn != '$nisn'");

    if (mysqli_num_rows($cek_nis_lain) > 0) {
        $d_ada = mysqli_fetch_array($cek_nis_lain);
        echo "<script>alert('Gagal! NIS \"$nis\" sudah digunakan oleh siswa \"".$d_ada['nama']."\".'); window.history.back();</script>";
    } else {
        $update = mysqli_query($koneksi, "UPDATE siswa SET nis='$nis', nama='$nama', id_kelas='$id_kelas', alamat='$alamat', no_telp='$no_telp', id_spp='$id_spp' WHERE nisn='$nisn'");

        if ($update) {
            echo "<script>alert('Data siswa berhasil diperbarui!'); location.href='data_siswa.php';</script>";
        } else {
            echo "<script>alert('Gagal memperbarui data: " . mysqli_error($koneksi) . "');</script>";
        }
    }
}

// 3. PROSES HAPUS SISWA
if (isset($_GET['hapus'])) {
    $nisn = mysqli_real_escape_string($koneksi, $_GET['hapus']);
    $hapus = mysqli_query($koneksi, "DELETE FROM siswa WHERE nisn='$nisn'");

    if ($hapus) {
        echo "<script>alert('Data siswa berhasil dihapus!'); location.href='data_siswa.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data: " . mysqli_error($koneksi) . "'); location.href='data_siswa.php';</script>";
    }
}

// Fetch Data Siswa
$data_siswa = [];
$query = mysqli_query($koneksi, "SELECT s.*, k.nama_kelas, sp.nominal FROM siswa s 
                                 JOIN kelas k ON s.id_kelas = k.id_kelas 
                                 JOIN spp sp ON s.id_spp = sp.id_spp 
                                 ORDER BY s.nama ASC");
while ($row = mysqli_fetch_array($query)) {
    $data_siswa[] = $row;
}
?>

<div class="col-md-10 p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="m-0">Data Siswa</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahSiswa">
            <i class="bi bi-plus-circle me-1"></i> Tambah Siswa
        </button>
    </div>

    <div class="table-responsive bg-white p-3 shadow-sm rounded">
        <table class="table table-bordered table-striped align-middle m-0">
            <thead class="table-secondary">
                <tr>
                    <th style="width: 40px;">No</th>
                    <th>NISN</th>
                    <th>NIS</th>
                    <th>Nama</th>
                    <th>Kelas</th>
                    <th>Alamat</th>
                    <th>No. Telp</th>
                    <th>SPP</th>
                    <th class="text-center" style="width: 100px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!empty($data_siswa)) {
                    $no = 1;
                    foreach ($data_siswa as $s) {
                    ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= htmlspecialchars($s['nisn']); ?></td>
                        <td><?= htmlspecialchars($s['nis']); ?></td>
                        <td><?= htmlspecialchars($s['nama']); ?></td>
                        <td><?= htmlspecialchars($s['nama_kelas']); ?></td>
                        <td><?= htmlspecialchars($s['alamat']); ?></td>
                        <td><?= htmlspecialchars($s['no_telp']); ?></td>
                        <td>Rp <?= number_format($s['nominal'], 0, ',', '.'); ?></td>
                        <td class="text-center text-nowrap">
                            <button class="btn btn-warning btn-sm p-1 px-2" data-bs-toggle="modal" data-bs-target="#modalEditSiswa<?= $s['nisn']; ?>" title="Edit">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <a href="data_siswa.php?hapus=<?= $s['nisn']; ?>" class="btn btn-danger btn-sm p-1 px-2" onclick="return confirm('Yakin ingin menghapus data siswa ini?')" title="Hapus">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php 
                    }
                } else {
                    echo "<tr><td colspan='9' class='text-center text-muted'>Belum ada data siswa.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL TAMBAH SISWA -->
<div class="modal fade" id="modalTambahSiswa" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form action="data_siswa.php" method="POST" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Tambah Data Siswa</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
            <label class="form-label fw-bold">NISN</label>
            <input type="text" name="nisn" class="form-control" placeholder="Masukkan NISN Unik" required>
        </div>
        <div class="mb-3">
            <label class="form-label fw-bold">NIS</label>
            <input type="text" name="nis" class="form-control" placeholder="Masukkan NIS Unik" required>
        </div>
        <div class="mb-3">
            <label class="form-label fw-bold">Nama Siswa</label>
            <input type="text" name="nama" class="form-control" placeholder="Masukkan Nama Lengkap" required>
        </div>
        <div class="mb-3">
            <label class="form-label fw-bold">Kelas</label>
            <select name="id_kelas" class="form-select" required>
                <option value="">-- Pilih Kelas --</option>
                <?php
                $q_k = mysqli_query($koneksi, "SELECT * FROM kelas");
                while ($k = mysqli_fetch_array($q_k)) {
                    echo "<option value='".$k['id_kelas']."'>".$k['nama_kelas']." (".$k['kompetensi_keahlian'].")</option>";
                }
                ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label fw-bold">Alamat</label>
            <textarea name="alamat" class="form-control" rows="2" placeholder="Masukkan Alamat Lengkap"></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label fw-bold">No. Telepon</label>
            <input type="text" name="no_telp" class="form-control" placeholder="Masukkan No HP/Telp">
        </div>
        <div class="mb-3">
            <label class="form-label fw-bold">Tarif SPP</label>
            <select name="id_spp" class="form-select" required>
                <option value="">-- Pilih SPP --</option>
                <?php
                $q_s = mysqli_query($koneksi, "SELECT * FROM spp");
                while ($spp = mysqli_fetch_array($q_s)) {
                    echo "<option value='".$spp['id_spp']."'>Tahun ".$spp['tahun']." - Rp ".number_format($spp['nominal'], 0, ',', '.')."</option>";
                }
                ?>
            </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" name="tambah" class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL EDIT SISWA -->
<?php foreach ($data_siswa as $s) { ?>
<div class="modal fade" id="modalEditSiswa<?= $s['nisn']; ?>" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form action="data_siswa.php" method="POST" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Data Siswa</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
            <label class="form-label fw-bold">NISN (Tidak dapat diubah)</label>
            <input type="text" name="nisn" class="form-control" value="<?= $s['nisn']; ?>" readonly>
        </div>
        <div class="mb-3">
            <label class="form-label fw-bold">NIS</label>
            <input type="text" name="nis" class="form-control" value="<?= htmlspecialchars($s['nis']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label fw-bold">Nama Siswa</label>
            <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($s['nama']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label fw-bold">Kelas</label>
            <select name="id_kelas" class="form-select" required>
                <?php
                $q_k = mysqli_query($koneksi, "SELECT * FROM kelas");
                while ($k = mysqli_fetch_array($q_k)) {
                    $selected = ($k['id_kelas'] == $s['id_kelas']) ? 'selected' : '';
                    echo "<option value='".$k['id_kelas']."' $selected>".$k['nama_kelas']." (".$k['kompetensi_keahlian'].")</option>";
                }
                ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label fw-bold">Alamat</label>
            <textarea name="alamat" class="form-control" rows="2"><?= htmlspecialchars($s['alamat']); ?></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label fw-bold">No. Telepon</label>
            <input type="text" name="no_telp" class="form-control" value="<?= htmlspecialchars($s['no_telp']); ?>">
        </div>
        <div class="mb-3">
            <label class="form-label fw-bold">Tarif SPP</label>
            <select name="id_spp" class="form-select" required>
                <?php
                $q_s = mysqli_query($koneksi, "SELECT * FROM spp");
                while ($spp = mysqli_fetch_array($q_s)) {
                    $selected = ($spp['id_spp'] == $s['id_spp']) ? 'selected' : '';
                    echo "<option value='".$spp['id_spp']."' $selected>Tahun ".$spp['tahun']." - Rp ".number_format($spp['nominal'], 0, ',', '.')."</option>";
                }
                ?>
            </select>
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