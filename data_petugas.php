<?php 
include 'header.php'; 
include 'sidebar.php'; 

// 1. Tambah Petugas
if (isset($_POST['tambah'])) {
    $username = $_POST['username'];
    $password = md5($_POST['password']);
    $nama_petugas = $_POST['nama_petugas'];
    $level = $_POST['level'];

    mysqli_query($koneksi, "INSERT INTO petugas VALUES (NULL, '$username', '$password', '$nama_petugas', '$level')");
    echo "<script>alert('Data berhasil ditambahkan!'); location.href='data_petugas.php';</script>";
}

// 2. Edit Petugas (Proses UPDATE)
if (isset($_POST['edit'])) {
    $id_petugas = $_POST['id_petugas'];
    $username = $_POST['username'];
    $nama_petugas = $_POST['nama_petugas'];
    $level = $_POST['level'];

    if (!empty($_POST['password'])) {
        $password = md5($_POST['password']);
        $query = mysqli_query($koneksi, "UPDATE petugas SET username='$username', password='$password', nama_petugas='$nama_petugas', level='$level' WHERE id_petugas='$id_petugas'");
    } else {
        $query = mysqli_query($koneksi, "UPDATE petugas SET username='$username', nama_petugas='$nama_petugas', level='$level' WHERE id_petugas='$id_petugas'");
    }

    if ($query) {
        echo "<script>alert('Data berhasil diperbarui!'); location.href='data_petugas.php';</script>";
    } else {
        echo "<script>alert('Gagal mengupdate data: " . mysqli_error($koneksi) . "');</script>";
    }
}

// 3. Hapus Petugas
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM petugas WHERE id_petugas='$id'");
    echo "<script>alert('Data berhasil dihapus!'); location.href='data_petugas.php';</script>";
}

// Ambil semua data petugas dan simpan ke Array
$data_petugas = [];
$q = mysqli_query($koneksi, "SELECT * FROM petugas");
while ($row = mysqli_fetch_array($q)) {
    $data_petugas[] = $row;
}
?>

<div class="col-md-10 p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="m-0">Data Petugas</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahPetugas">
            <i class="bi bi-plus-circle me-1"></i>Tambah Petugas
        </button>
    </div>

    <div class="table-responsive bg-white p-3 shadow-sm rounded">
        <table class="table table-bordered table-striped align-middle m-0">
            <thead class="table-secondary">
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Username</th>
                    <th>Nama Petugas</th>
                    <th style="width: 120px;">Level</th>
                    <th class="text-center" style="width: 100px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                foreach ($data_petugas as $d) {
                ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= $d['username']; ?></td>
                    <td><?= $d['nama_petugas']; ?></td>
                    <td>
                        <span class="badge bg-info text-dark"><?= strtoupper($d['level']); ?></span>
                    </td>
                    <td class="text-center text-nowrap">
                        <button class="btn btn-warning btn-sm p-1 px-2" data-bs-toggle="modal" data-bs-target="#modalEditPetugas<?= $d['id_petugas']; ?>" title="Edit">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <a href="data_petugas.php?hapus=<?= $d['id_petugas']; ?>" class="btn btn-danger btn-sm p-1 px-2" onclick="return confirm('Hapus petugas ini?')" title="Hapus">
                            <i class="bi bi-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ================= MODAL EDIT PETUGAS (DITARUH DI LUAR TABEL) ================= -->
<?php foreach ($data_petugas as $d) { ?>
<div class="modal fade" id="modalEditPetugas<?= $d['id_petugas']; ?>" tabindex="-1" aria-labelledby="labelModal<?= $d['id_petugas']; ?>" aria-hidden="true">
  <div class="modal-dialog">
    <form action="data_petugas.php" method="POST" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="labelModal<?= $d['id_petugas']; ?>">Edit Data Petugas</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- input ID Petugas -->
        <input type="hidden" name="id_petugas" value="<?= $d['id_petugas']; ?>">
        
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" value="<?= $d['username']; ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Nama Petugas</label>
            <input type="text" name="nama_petugas" class="form-control" value="<?= $d['nama_petugas']; ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Password Baru <small class="text-muted">(Kosongkan jika tidak diubah)</small></label>
            <input type="password" name="password" class="form-control" placeholder="Isi jika ingin ubah password">
        </div>
        <div class="mb-3">
            <label class="form-label">Level</label>
            <select name="level" class="form-select" required>
                <option value="admin" <?= ($d['level'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                <option value="petugas" <?= ($d['level'] == 'petugas') ? 'selected' : ''; ?>>Petugas</option>
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

<!-- ================= MODAL TAMBAH PETUGAS ================= -->
<div class="modal fade" id="modalTambahPetugas" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form action="data_petugas.php" method="POST" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Tambah Data Petugas</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Nama Petugas</label>
            <input type="text" name="nama_petugas" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Level</label>
            <select name="level" class="form-select" required>
                <option value="admin">Admin</option>
                <option value="petugas">Petugas</option>
            </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" name="tambah" class="btn btn-primary">Simpan Data</button>
      </div>
    </form>
  </div>
</div>

<?php include 'footer.php'; ?>