<?php 
include 'header.php'; 
include 'sidebar.php'; 

$q_lunas = mysqli_query($koneksi, "SELECT COUNT(DISTINCT nisn) AS total FROM pembayaran");
$d_lunas = mysqli_fetch_assoc($q_lunas);

$q_total_siswa = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM siswa");
$d_total_siswa = mysqli_fetch_assoc($q_total_siswa);

$lunas = $d_lunas['total'] ?? 0;
$total_siswa = $d_total_siswa['total'] ?? 0;
$belum_lunas = $total_siswa - $lunas;
?>

<div class="col-md-10 p-4">
    <h4>Selamat Datang, Wahyu</h4>
    <hr>
    <div class="row mt-4">
        <div class="col-md-6 mb-3">
            <div class="card shadow-sm border-0">
                <div class="card-body rounded bg-light border text-center py-4">
                    <h6 class="card-title text-muted fw-bold">Siswa Yang Sudah Lunas</h6>
                    <h2 class="fw-bold text-success">Total : <?= $lunas ?> Siswa</h2>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card shadow-sm border-0">
                <div class="card-body rounded bg-light border text-center py-4">
                    <h6 class="card-title text-muted fw-bold">Siswa Yang Belum Lunas</h6>
                    <h2 class="fw-bold text-danger">Total : <?= $belum_lunas ?> Siswa</h2>
                </div>
            </div>
        </div>
    </div>
    
    <div class="text-end mt-5">
        <h5 class="fw-bold text-secondary">APLIKASI PEMBAYARAN SPP SEKOLAH</h5>
    </div>
</div>

<?php include 'footer.php'; ?>