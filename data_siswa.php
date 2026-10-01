<?php 
include 'header.php'; 
include 'sidebar.php'; 

// 1. Tambah Siswa
if (isset($_POST['tambah'])) {
    $nisn = $_POST['nisn'];
    $nis = $_POST['nis'];
    $nama = $_POST['nama'];
    $id_kelas = $_POST['id_kelas'];
    $alamat = $_POST['alamat'];
    $no_telp = $_POST['no_telp'];
    $id_spp = $_POST['id_spp'];

    $query = mysqli_query($koneksi, "INSERT INTO siswa VALUES ('$nisn', '$nis', '$nama', '$id_kelas', '$alamat', '$no_telp', '$id_spp')");
    if ($query) {
        echo "<script>alert('Data siswa berhasil ditambahkan!'); location.href='data_siswa.php';</script>";
    } else {
        echo "<script>alert('Gagal menambah data: " . mysqli_error($koneksi) . "');</script>";
    }
}

// 2. Edit Siswa (Proses UPDATE)
if (isset($_POST['edit'])) {
    $nisn = $_POST['nisn'];
    $nis = $_POST['nis'];
    $nama = $_POST['nama'];
    $id_kelas = $_POST['id_kelas'];
    $alamat = $_POST['alamat'];
    $no_telp = $_POST['no_telp'];
    $id_spp = $_POST['id_spp'];

    $query = mysqli_query($koneksi, "UPDATE siswa SET 
        nis='$nis', 
        nama='$nama', 
        id_kelas='$id_kelas', 
        alamat='$alamat', 
        no_telp='$no_telp', 
        id_spp='$id_spp' 
        WHERE nisn='$nisn'");

    if ($query) {
        echo "<script>alert('Data siswa berhasil diperbarui!'); location.href='data_siswa.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui data: " . mysqli_error($koneksi) . "');</script>";
    }
}

// 3. Hapus Siswa
if (isset($_GET['hapus'])) {
    $nisn = $_GET['hapus'];
    $query = mysqli_query($koneksi, "DELETE FROM siswa WHERE nisn='$nisn'");
    if ($query) {
        echo "<script>alert('Data siswa berhasil dihapus!'); location.href='data_siswa.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data: " . mysqli_error($koneksi) . "');</script>";
    }
}

// Fetch Master Kelas & SPP untuk Dropdown Modal
$list_kelas = [];
$q_k = mysqli_query($koneksi, "SELECT * FROM kelas");
while ($k = mysqli_fetch_array($q_k)) {
    $list_kelas[] = $k;
}

$list_spp = [];
$q_s = mysqli_query($koneksi, "SELECT * FROM spp");
while ($s = mysqli_fetch_array($q_s)) {
    $list_spp[] = $s;
}

// Fetch Data Siswa ke Array
$data_siswa = [];
$query = mysqli_query($koneksi, "SELECT siswa.*, kelas.nama_kelas, spp.nominal, spp.tahun 
                                 FROM siswa 
                                 JOIN kelas ON siswa.id_kelas = kelas.id_kelas 
                                 JOIN spp ON siswa.id_spp = spp.id_spp");
while ($row = mysqli_fetch_array($query)) {
    $data_siswa[] = $row;
}
?>

<div class="col-md-10 p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="m-0">Data Siswa</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahSiswa">
            <i class="bi bi-plus-circle me-1"></i>Tambah Siswa
        </button>
    </div>

    <div class="table-responsive bg-white p-3 shadow-sm rounded">
        <table class="table table-bordered table-striped align-middle m-0">
            <thead class="table-secondary">
                <tr>
                    <th>NISN</th>
                    <th>NIS</th>
                    <th>Nama</th>
                    <th>Kelas</th>
                    <th>SPP</th>
                    <th>Alamat</th>
                    <th>No. Telp</th>
                    <th class="text-center" style="width: 100px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data_siswa as $d) { ?>
                <tr>
                    <td><?= $d['nisn']; ?></td>
                    <td><?= $d['nis']; ?></td>
                    <td><?= $d['nama']; ?></td>
                    <td><?= $d['nama_kelas']; ?></td>
                    <td>Rp <?= number_format($d['nominal'], 0, ',', '.'); ?></td>
                    <td><?= $d['alamat']; ?></td>
                    <td><?= $d['no_telp']; ?></td>
                    <td class="text-center text-nowrap">
                        <!-- Tombol Edit Ringkas -->
                        <button class="btn btn-warning btn-sm p-1 px-2" data-bs-toggle="modal" data-bs-target="#modalEditSiswa<?= $d['nisn']; ?>" title="Edit">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <!-- Tombol Hapus Ringkas -->
                        <a href="data_siswa.php?hapus=<?= $d['nisn']; ?>" class="btn btn-danger btn-sm p-1 px-2" onclick="return confirm('Hapus siswa ini?')" title="Hapus">
                            <i class="bi bi-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ================= MODAL EDIT SISWA (DI LUAR TABEL) ================= -->
<?php foreach ($data_siswa as $d) { ?>
<div class="modal fade" id="modalEditSiswa<?= $d['nisn']; ?>" tabindex="-1" aria-labelledby="labelModalSiswa<?= $d['nisn']; ?>" aria-hidden="true">
  <div class="modal-dialog">
    <form action="data_siswa.php" method="POST" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="labelModalSiswa<?= $d['nisn']; ?>">Edit Data Siswa</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-2">
            <label class="form-label">NISN (Primary Key)</label>
            <input type="text" name="nisn" class="form-control" value="<?= $d['nisn']; ?>" readonly>
        </div>
        <div class="mb-2">
            <label class="form-label">NIS</label>
            <input type="text" name="nis" class="form-control" value="<?= $d['nis']; ?>" required>
        </div>
        <div class="mb-2">
            <label class="form-label">Nama Siswa</label>
            <input type="text" name="nama" class="form-control" value="<?= $d['nama']; ?>" required>
        </div>
        <div class="mb-2">
            <label class="form-label">Kelas</label>
            <select name="id_kelas" class="form-select" required>
                <?php foreach ($list_kelas as $k) { 
                    $selected = ($k['id_kelas'] == $d['id_kelas']) ? 'selected' : '';
                ?>
                    <option value="<?= $k['id_kelas']; ?>" <?= $selected; ?>><?= $k['nama_kelas']; ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="mb-2">
            <label class="form-label">SPP</label>
            <select name="id_spp" class="form-select" required>
                <?php foreach ($list_spp as $s) { 
                    $selected = ($s['id_spp'] == $d['id_spp']) ? 'selected' : '';
                ?>
                    <option value="<?= $s['id_spp']; ?>" <?= $selected; ?>>
                        Tahun <?= $s['tahun']; ?> - Rp <?= number_format($s['nominal'], 0, ',', '.'); ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <div class="mb-2">
            <label class="form-label">Alamat</label>
            <textarea name="alamat" class="form-control" required><?= $d['alamat']; ?></textarea>
        </div>
        <div class="mb-2">
            <label class="form-label">No. Telp</label>
            <input type="text" name="no_telp" class="form-control" value="<?= $d['no_telp']; ?>" required>
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

<!-- ================= MODAL TAMBAH SISWA ================= -->
<div class="modal fade" id="modalTambahSiswa" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form action="data_siswa.php" method="POST" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Tambah Data Siswa</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-2"><label class="form-label">NISN</label><input type="text" name="nisn" class="form-control" required></div>
        <div class="mb-2"><label class="form-label">NIS</label><input type="text" name="nis" class="form-control" required></div>
        <div class="mb-2"><label class="form-label">Nama Siswa</label><input type="text" name="nama" class="form-control" required></div>
        <div class="mb-2">
            <label class="form-label">Kelas</label>
            <select name="id_kelas" class="form-select" required>
                <?php foreach ($list_kelas as $k) { ?>
                    <option value="<?= $k['id_kelas']; ?>"><?= $k['nama_kelas']; ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="mb-2">
            <label class="form-label">SPP</label>
            <select name="id_spp" class="form-select" required>
                <?php foreach ($list_spp as $s) { ?>
                    <option value="<?= $s['id_spp']; ?>">Tahun <?= $s['tahun']; ?> - Rp <?= number_format($s['nominal'], 0, ',', '.'); ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="mb-2"><label class="form-label">Alamat</label><textarea name="alamat" class="form-control" required></textarea></div>
        <div class="mb-2"><label class="form-label">No. Telp</label><input type="text" name="no_telp" class="form-control" required></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" name="tambah" class="btn btn-primary">Simpan Data</button>
      </div>
    </form>
  </div>
</div>

<?php include 'footer.php'; ?>