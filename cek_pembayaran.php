<?php 
include 'header.php'; 
include 'sidebar.php'; 
?>

<div class="col-md-10 p-4">
    <h4>Cek Pembayaran</h4>
    
    <!-- Form Pencarian NISN -->
    <div class="card p-3 my-3 shadow-sm border-0">
        <h6>Cek NISN dengan Verifikasi Otomatis</h6>
        <form method="GET" action="cek_pembayaran.php" class="row g-2 align-items-center">
            <div class="col-auto">
                <input type="text" name="nisn" class="form-control" placeholder="Masukkan NISN Siswa" value="<?= htmlspecialchars($_GET['nisn'] ?? ''); ?>">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-secondary">Cek Pembayaran</button>
            </div>
            <?php if (isset($_GET['nisn']) && $_GET['nisn'] != ''): ?>
            <div class="col-auto">
                <a href="cek_pembayaran.php" class="btn btn-outline-secondary">Reset Filter</a>
            </div>
            <?php endif; ?>
        </form>
    </div>

    <div class="row">
        <!-- Siswa Yang Sudah Lunas -->
        <div class="col-md-6 mb-4">
            <h6 class="text-center fw-bold mb-3 text-success">Siswa Yang Sudah Lunas</h6>
            <div class="table-responsive bg-white p-2 shadow-sm rounded">
                <table class="table table-sm table-bordered align-middle m-0">
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
                        $nisn_filter = isset($_GET['nisn']) && !empty($_GET['nisn']) ? mysqli_real_escape_string($koneksi, $_GET['nisn']) : '';
                        $where_lunas = $nisn_filter ? "WHERE p.nisn = '$nisn_filter'" : "";

                        // Query JOIN langsung ke tabel pembayaran & siswa tanpa dependensi tabel cek_pembayaran yang error
                        $q_lunas = mysqli_query($koneksi, "SELECT p.*, s.nama 
                                                          FROM pembayaran p 
                                                          JOIN siswa s ON p.nisn = s.nisn 
                                                          $where_lunas 
                                                          ORDER BY p.id_pembayaran DESC");

                        if (mysqli_num_rows($q_lunas) > 0) {
                            while ($r1 = mysqli_fetch_array($q_lunas)) {
                            ?>
                            <tr>
                                <td><?= $r1['nisn']; ?></td>
                                <td><?= $r1['tgl_bayar']; ?></td>
                                <td><?= $r1['bulan_dibayar'] . ' ' . $r1['tahun_dibayar']; ?></td>
                                <td><span class="badge bg-success">Lunas</span></td>
                            </tr>
                            <?php 
                            }
                        } else {
                            echo "<tr><td colspan='4' class='text-center text-muted'>Tidak ada data pembayaran lunas.</td></tr>";
                        } 
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Siswa Yang Belum Lunas -->
        <div class="col-md-6 mb-4">
            <h6 class="text-center fw-bold mb-3 text-danger">Siswa Yang Belum Lunas</h6>
            <div class="table-responsive bg-white p-2 shadow-sm rounded">
                <table class="table table-sm table-bordered align-middle m-0">
                    <thead class="table-secondary">
                        <tr>
                            <th>NISN</th>
                            <th>Nama</th>
                            <th>Kelas</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $where_belum = $nisn_filter ? "WHERE s.nisn = '$nisn_filter' AND s.nisn NOT IN (SELECT nisn FROM pembayaran)" : "WHERE s.nisn NOT IN (SELECT nisn FROM pembayaran)";

                        $q_belum = mysqli_query($koneksi, "SELECT s.*, k.nama_kelas 
                                                           FROM siswa s 
                                                           JOIN kelas k ON s.id_kelas = k.id_kelas 
                                                           $where_belum 
                                                           ORDER BY s.nama ASC");

                        if (mysqli_num_rows($q_belum) > 0) {
                            while ($r2 = mysqli_fetch_array($q_belum)) {
                            ?>
                            <tr>
                                <td><?= $r2['nisn']; ?></td>
                                <td><?= $r2['nama']; ?></td>
                                <td><?= $r2['nama_kelas']; ?></td>
                                <td class="text-center">
                                    <a href="pembayaran.php?nisn=<?= $r2['nisn']; ?>" class="btn btn-sm btn-primary py-0 px-2" title="Bayar Sekarang">
                                        <i class="bi bi-cash me-1"></i>Bayar
                                    </a>
                                </td>
                            </tr>
                            <?php 
                            }
                        } else {
                            echo "<tr><td colspan='4' class='text-center text-muted'>Tidak ada data siswa belum lunas.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>