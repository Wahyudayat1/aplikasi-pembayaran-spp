<?php 
include 'header.php'; 
include 'sidebar.php'; 

// 1. PROSES TAMBAH PETUGAS
if (isset($_POST['tambah'])) {
    $username     = mysqli_real_escape_string($koneksi, trim($_POST['username']));
    $password     = mysqli_real_escape_string($koneksi, $_POST['password']);
    $nama_petugas = mysqli_real_escape_string($koneksi, trim($_POST['nama_petugas']));
    $level        = mysqli_real_escape_string($koneksi, $_POST['level']);

    // Validasi Cek Username / Nama Petugas Duplikat
    $cek_duplikat = mysqli_query($koneksi, "SELECT * FROM petugas WHERE username = '$username' OR nama_petugas = '$nama_petugas'");
    
    if (mysqli_num_rows($cek_duplikat) > 0) {
        $data_ada = mysqli_fetch_array($cek_duplikat);
        if (strtolower($data_ada['username']) == strtolower($username)) {
            echo "<script>alert('Gagal! Username \"$username\" sudah digunakan. Gunakan username lain.'); window.history.back();</script>";
        } else {
            echo "<script>alert('Gagal! Nama Petugas \"$nama_petugas\" sudah terdaftar.'); window.history.back();</script>";
        }
    } else {
        // Enkripsi password (opsional/sesuai sistem Anda)
        $simpan = mysqli_query($koneksi, "INSERT INTO petugas (username, password, nama_petugas, level) VALUES ('$username', '$password', '$nama_petugas', '$level')");
        
        if ($simpan) {
            echo "<script>alert('Data petugas berhasil ditambahkan!'); location.href='data_petugas.php';</script>";
        } else {
            echo "<script>alert('Gagal menyimpan data: " . mysqli_error($koneksi) . "');</script>";
        }
    }
}

// 2. PROSES EDIT PETUGAS
if (isset($_POST['edit'])) {
    $id_petugas   = $_POST['id_petugas'];
    $username     = mysqli_real_escape_string($koneksi, trim($_POST['username']));
    $nama_petugas = mysqli_real_escape_string($koneksi, trim($_POST['nama_petugas']));
    $level        = mysqli_real_escape_string($koneksi, $_POST['level']);
    $password     = $_POST['password'];

    // Validasi Cek Duplikat selain ID petugas yang sedang di-edit
    $cek_duplikat = mysqli_query($koneksi, "SELECT * FROM petugas WHERE (username = '$username' OR nama_petugas = '$nama_petugas') AND id_petugas != '$id_petugas'");
    
    if (mysqli_num_rows($cek_duplikat) > 0) {
        $data_ada = mysqli_fetch_array($cek_duplikat);
        if (strtolower($data_ada['username']) == strtolower($username)) {
            echo "<script>alert('Gagal! Username \"$username\" sudah digunakan petugas lain.'); window.history.back();</script>";
        } else {
            echo "<script>alert('Gagal! Nama Petugas \"$nama_petugas\" sudah terdaftar.'); window.history.back();</script>";
        }
    } else {
        // Update password jika diisi, jika kosong gunakan password lama
        if (!empty($password)) {
            $query_update = "UPDATE petugas SET username='$username', password='$password', nama_petugas='$nama_petugas', level='$level' WHERE id_petugas='$id_petugas'";
        } else {
            $query_update = "UPDATE petugas SET username='$username', nama_petugas='$nama_petugas', level='$level' WHERE id_petugas='$id_petugas'";
        }

        $update = mysqli_query($koneksi, $query_update);

        if ($update) {
            echo "<script>alert('Data petugas berhasil diperbarui!'); location.href='data_petugas.php';</script>";
        } else {
            echo "<script>alert('Gagal memperbarui data: " . mysqli_error($koneksi) . "');</script>";
        }
    }
}

// 3. PROSES HAPUS PETUGAS
if (isset($_GET['hapus'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['hapus']);
    $hapus = mysqli_query($koneksi, "DELETE FROM petugas WHERE id_petugas='$id'");

    if ($hapus) {
        echo "<script>alert('Data petugas berhasil dihapus!'); location.href='data_petugas.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data: " . mysqli_error($koneksi) . "'); location.href='data_petugas.php';</script>";
    }
}

// Fetch All Data Petugas
$data_petugas = [];
$query = mysqli_query($koneksi, "SELECT * FROM petugas ORDER BY id_petugas ASC");
while ($row = mysqli_fetch_array($query)) {
    $data_petugas[] = $row;
}
?>

<div class="col-md-10 p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="m-0">Data Petugas</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahPetugas">
            <i class="bi bi-plus-circle me-1"></i> Tambah Petugas
        </button>
    </div>

    <div class="table-responsive bg-white p-3 shadow-sm rounded">
        <table class="table table-bordered table-striped align-middle m-0">
            <thead class="table-secondary">
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Username</th>
                    <th>Nama Petugas</th>
                    <th>Level</th>
                    <th class="text-center" style="width: 100px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!empty($data_petugas)) {
                    $no = 1;
                    foreach ($data_petugas as $p) {
                    ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= htmlspecialchars($p['username']); ?></td>
                        <td><?= htmlspecialchars($p['nama_petugas']); ?></td>
                        <td>
                            <?php if (strtolower($p['level']) == 'admin'): ?>
                                <span class="badge bg-info text-dark">ADMIN</span>
                            <?php else: ?>
                                <span class="badge bg-cyan text-white" style="background-color: #0dcaf0;">PETUGAS</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center text-nowrap">
                            <button class="btn btn-warning btn-sm p-1 px-2" data-bs-toggle="modal" data-bs-target="#modalEditPetugas<?= $p['id_petugas']; ?>" title="Edit">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <a href="data_petugas.php?hapus=<?= $p['id_petugas']; ?>" class="btn btn-danger btn-sm p-1 px-2" onclick="return confirm('Yakin ingin menghapus petugas ini?')" title="Hapus">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php 
                    }
                } else {
                    echo "<tr><td colspan='5' class='text-center text-muted'>Belum ada data petugas.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL TAMBAH PETUGAS -->
<div class="modal fade" id="modalTambahPetugas" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form action="data_petugas.php" method="POST" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Tambah Data Petugas</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
            <label class="form-label fw-bold">Username</label>
            <input type="text" name="username" class="form-control" placeholder="Masukkan Username" required>
        </div>
        <div class="mb-3">
            <label class="form-label fw-bold">Password</label>
            <input type="password" name="password" class="form-control" placeholder="Masukkan Password" required>
        </div>
        <div class="mb-3">
            <label class="form-label fw-bold">Nama Petugas</label>
            <input type="text" name="nama_petugas" class="form-control" placeholder="Masukkan Nama Lengkap Petugas" required>
        </div>
        <div class="mb-3">
            <label class="form-label fw-bold">Level Hak Akses</label>
            <select name="level" class="form-select" required>
                <option value="petugas">Petugas</option>
                <option value="admin">Admin</option>
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

<!-- MODAL EDIT PETUGAS -->
<?php foreach ($data_petugas as $p) { ?>
<div class="modal fade" id="modalEditPetugas<?= $p['id_petugas']; ?>" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form action="data_petugas.php" method="POST" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Data Petugas</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id_petugas" value="<?= $p['id_petugas']; ?>">
        
        <div class="mb-3">
            <label class="form-label fw-bold">Username</label>
            <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($p['username']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label fw-bold">Password Baru <small class="text-muted fw-normal">(Kosongkan jika tidak diubah)</small></label>
            <input type="password" name="password" class="form-control" placeholder="Ubah Password">
        </div>
        <div class="mb-3">
            <label class="form-label fw-bold">Nama Petugas</label>
            <input type="text" name="nama_petugas" class="form-control" value="<?= htmlspecialchars($p['nama_petugas']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label fw-bold">Level Hak Akses</label>
            <select name="level" class="form-select" required>
                <option value="petugas" <?= (strtolower($p['level']) == 'petugas') ? 'selected' : ''; ?>>Petugas</option>
                <option value="admin" <?= (strtolower($p['level']) == 'admin') ? 'selected' : ''; ?>>Admin</option>
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