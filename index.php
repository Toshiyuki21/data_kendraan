<?php
require_once 'config.php';

// Proses input data
if(isset($_POST['submit'])) {
    $no_plat = $_POST['no_plat'];
    $tipe = $_POST['tipe_kendaraan'];
    $status = $_POST['status'];
    $nama = $_POST['nama_pegawai'];
    $tujuan = $_POST['tujuan'];
    
    if($status == 'Masuk') {
        $query = "INSERT INTO kendaraan (no_plat, tipe_kendaraan, nama_pegawai, tujuan) 
                  VALUES ('$no_plat', '$tipe', '$nama', '$tujuan')";
    } else {
        $query = "UPDATE kendaraan SET status='Keluar', waktu_keluar=NOW() 
                  WHERE no_plat='$no_plat' AND status='Masuk'";
    }
    
    mysqli_query($conn, $query);
    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Kendaraan Kantor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .custom-card {
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .stats-card {
            transition: transform 0.3s;
        }
        .stats-card:hover {
            transform: translateY(-5px);
        }
        .table-custom {
            background: white;
            border-radius: 10px;
            overflow: hidden;
        }
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-primary">
        <div class="container">
            <span class="navbar-brand mb-0 h1">Sistem Data Kendaraan Kantor</span>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Statistik -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card custom-card bg-primary text-white stats-card">
                    <div class="card-body">
                        <h6 class="card-title">Kendaraan di Dalam</h6>
                        <?php
                        $query = mysqli_query($conn, "SELECT COUNT(*) as total FROM kendaraan WHERE status='Masuk'");
                        $data = mysqli_fetch_assoc($query);
                        ?>
                        <h2><?= $data['total'] ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card custom-card bg-success text-white stats-card">
                    <div class="card-body">
                        <h6 class="card-title">Total Hari Ini</h6>
                        <?php
                        $query = mysqli_query($conn, "SELECT COUNT(*) as total FROM kendaraan WHERE DATE(waktu_masuk) = CURDATE()");
                        $data = mysqli_fetch_assoc($query);
                        ?>
                        <h2><?= $data['total'] ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Form Input -->
            <div class="col-md-4 mb-4">
                <div class="card custom-card">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Input Data Kendaraan</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Nomor Plat</label>
                                <input type="text" name="no_plat" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tipe Kendaraan</label>
                                <select name="tipe_kendaraan" class="form-select" required>
                                    <option value="Mobil">Mobil</option>
                                    <option value="Motor">Motor</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select" required>
                                    <option value="Masuk">Masuk</option>
                                    <option value="Keluar">Keluar</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nama Pegawai</label>
                                <input type="text" name="nama_pegawai" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tujuan</label>
                                <textarea name="tujuan" class="form-control" rows="2" required></textarea>
                            </div>
                            <button type="submit" name="submit" class="btn btn-primary w-100">Simpan Data</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Tabel Data -->
            <div class="col-md-8">
                <div class="card custom-card">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Data Kendaraan Hari Ini</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>No Plat</th>
                                        <th>Tipe</th>
                                        <th>Waktu Masuk</th>
                                        <th>Waktu Keluar</th>
                                        <th>Status</th>
                                        <th>Pegawai</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $query = mysqli_query($conn, "SELECT * FROM kendaraan 
                                              WHERE DATE(waktu_masuk) = CURDATE() 
                                              ORDER BY waktu_masuk DESC");
                                    while($row = mysqli_fetch_assoc($query)) : ?>
                                    <tr>
                                        <td><?= $row['no_plat'] ?></td>
                                        <td><?= $row['tipe_kendaraan'] ?></td>
                                        <td><?= date('H:i', strtotime($row['waktu_masuk'])) ?></td>
                                        <td><?= $row['waktu_keluar'] ? date('H:i', strtotime($row['waktu_keluar'])) : '-' ?></td>
                                        <td>
                                            <span class="badge bg-<?= $row['status'] == 'Masuk' ? 'success' : 'danger' ?>">
                                                <?= $row['status'] ?>
                                            </span>
                                        </td>
                                        <td><?= $row['nama_pegawai'] ?></td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>