<?php 
include 'header.php'; 
include 'sidebar.php'; 

// 1. Tambah Kelas
if (isset($_POST['tambah'])) {
    $nama_kelas = $_POST['nama_kelas'];
    $kompetensi_keahlian = $_POST['kompetensi_keahlian'];

    $query = mysqli_query($koneksi, "INSERT INTO kelas VALUES (NULL, '$nama_kelas', '$kompetensi_keahlian')");
    if ($query) {
        echo "<script>alert('Data kelas berhasil ditambahkan!'); location.href='data_kelas.php';</script>";
    } else {
        echo "<script>alert('Gagal menambah data: " . mysqli_error($koneksi) . "');</script>";
    }
}

// 2. Edit Kelas (Proses UPDATE)
if (isset($_POST['edit'])) {
    $id_kelas = $_POST['id_kelas'];
    $nama_kelas = $_POST['nama_kelas'];
    $kompetensi_keahlian = $_POST['kompetensi_keahlian'];

    $query = mysqli_query($koneksi, "UPDATE kelas SET nama_kelas='$nama_kelas', kompetensi_keahlian='$kompetensi_keahlian' WHERE id_kelas='$id_kelas'");
    if ($query) {
        echo "<script>alert('Data kelas berhasil diperbarui!'); location.href='data_kelas.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui data: " . mysqli_error($koneksi) . "');</script>";
    }
}

// 3. Hapus Kelas
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $query = mysqli_query($koneksi, "DELETE FROM kelas WHERE id_kelas='$id'");
    if ($query) {
        echo "<script>alert('Data kelas berhasil dihapus!'); location.href='data_kelas.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data: " . mysqli_error($koneksi) . "');</script>";
    }
}

// Ambil seluruh data kelas ke dalam Array
$data_kelas = [];
$q = mysqli_query($koneksi, "SELECT * FROM kelas");
while ($row = mysqli_fetch_array($q)) {
    $data_kelas[] = $row;
}
?>

<div class="col-md-10 p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="m-0">Data Kelas</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahKelas">
            <i class="bi bi-plus-circle me-1"></i>Tambah Kelas
        </button>
    </div>

    <div class="table-responsive bg-white p-3 shadow-sm rounded">
        <table class="table table-bordered table-striped align-middle m-0">
            <thead class="table-secondary">
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Nama Kelas</th>
                    <th>Kompetensi Keahlian</th>
                    <th class="text-center" style="width: 100px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                foreach ($data_kelas as $d) {
                ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= $d['nama_kelas']; ?></td>
                    <td><?= $d['kompetensi_keahlian']; ?></td>
                    <td class="text-center text-nowrap">
                        <!-- Tombol Edit Ringkas -->
                        <button class="btn btn-warning btn-sm p-1 px-2" data-bs-toggle="modal" data-bs-target="#modalEditKelas<?= $d['id_kelas']; ?>" title="Edit">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <!-- Tombol Hapus Ringkas -->
                        <a href="data_kelas.php?hapus=<?= $d['id_kelas']; ?>" class="btn btn-danger btn-sm p-1 px-2" onclick="return confirm('Hapus kelas ini?')" title="Hapus">
                            <i class="bi bi-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ================= MODAL EDIT KELAS (DI LUAR TABEL) ================= -->
<?php foreach ($data_kelas as $d) { ?>
<div class="modal fade" id="modalEditKelas<?= $d['id_kelas']; ?>" tabindex="-1" aria-labelledby="labelModalKelas<?= $d['id_kelas']; ?>" aria-hidden="true">
  <div class="modal-dialog">
    <form action="data_kelas.php" method="POST" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="labelModalKelas<?= $d['id_kelas']; ?>">Edit Data Kelas</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Hidden input ID Kelas -->
        <input type="hidden" name="id_kelas" value="<?= $d['id_kelas']; ?>">
        
        <div class="mb-3">
            <label class="form-label">Nama Kelas</label>
            <input type="text" name="nama_kelas" class="form-control" value="<?= $d['nama_kelas']; ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Kompetensi Keahlian</label>
            <input type="text" name="kompetensi_keahlian" class="form-control" value="<?= $d['kompetensi_keahlian']; ?>" required>
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

<!-- ================= MODAL TAMBAH KELAS ================= -->
<div class="modal fade" id="modalTambahKelas" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form action="data_kelas.php" method="POST" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Tambah Data Kelas</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
            <label class="form-label">Nama Kelas</label>
            <input type="text" name="nama_kelas" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Kompetensi Keahlian</label>
            <input type="text" name="kompetensi_keahlian" class="form-control" required>
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