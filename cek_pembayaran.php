<?php 
include 'header.php'; 
include 'sidebar.php'; 
?>

<div class="col-md-10 p-4">
    <h4>Cek Pembayaran</h4>
    <div class="card p-3 my-3 shadow-sm border-0">
        <h6>Cek NISN dengan verifikasi Otomatis</h6>
        <form method="GET" class="row g-2 align-items-center">
            <div class="col-auto">
                <input type="text" name="nisn" class="form-control" placeholder="Masukkan NISN Siswa" value="<?= $_GET['nisn'] ?? '' ?>" required>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-secondary">Cek Pembayaran</button>
            </div>
        </form>
    </div>

    <div class="row">
        <div class="col-md-6">
            <h6 class="text-center fw-bold mb-3">Siswa Yang Sudah Lunas</h6>
            <div class="table-responsive bg-white p-2 shadow-sm rounded">
                <table class="table table-sm table-bordered align-middle">
                    <thead class="table-secondary">
                        <tr>
                            <th>NISN</th>
                            <th>Tanggal</th>
                            <th>Bulan/Tahun</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $where = isset($_GET['nisn']) && !empty($_GET['nisn']) ? "AND p.nisn='".$_GET['nisn']."'" : "";
                        $q_lunas = mysqli_query($koneksi, "SELECT p.*, c.status_verifikasi FROM pembayaran p 
                                                          JOIN cek_pembayaran c ON p.id_pembayaran = c.id_pembayaran 
                                                          WHERE c.status_verifikasi = 'Lunas' $where");
                        while ($r1 = mysqli_fetch_array($q_lunas)) {
                        ?>
                        <tr>
                            <td><?= $r1['nisn']; ?></td>
                            <td><?= $r1['tgl_bayar']; ?></td>
                            <td><?= $r1['bulan_dibayar'] . ' ' . $r1['tahun_dibayar']; ?></td>
                            <td><span class="badge bg-success"><?= $r1['status_verifikasi']; ?></span></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-md-6">
            <h6 class="text-center fw-bold mb-3">Siswa Yang Belum Lunas</h6>
            <div class="table-responsive bg-white p-2 shadow-sm rounded">
                <table class="table table-sm table-bordered align-middle">
                    <thead class="table-secondary">
                        <tr>
                            <th>NISN</th>
                            <th>Nama</th>
                            <th>Kelas</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $q_belum = mysqli_query($koneksi, "SELECT s.*, k.nama_kelas FROM siswa s 
                                                           JOIN kelas k ON s.id_kelas = k.id_kelas 
                                                           WHERE s.nisn NOT IN (SELECT nisn FROM pembayaran)");
                        while ($r2 = mysqli_fetch_array($q_belum)) {
                        ?>
                        <tr>
                            <td><?= $r2['nisn']; ?></td>
                            <td><?= $r2['nama']; ?></td>
                            <td><?= $r2['nama_kelas']; ?></td>
                            <td><span class="badge bg-danger">Belum Lunas</span></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>