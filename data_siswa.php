<?php
// Mulai session
session_start();

// Periksa apakah session 'username' telah diatur (sesuai dengan apa yang Anda gunakan untuk menyimpan username setelah login)
if (!isset($_SESSION['username'])) {
    // Jika tidak, arahkan kembali ke halaman login
    header("Location: login.php");
    exit(); // Pastikan untuk keluar setelah mengarahkan pengguna
}

$host = "localhost";
$user = "root";
$pass = "";
$db = "jurusan";

$koneksi = mysqli_connect($host, $user, $pass, $db);
if (!$koneksi) {
    die("Tidak bisa terkoneksi dengan database");
}

// Fungsi untuk menghindari SQL Injection
function escape($value) {
    global $koneksi;
    return mysqli_real_escape_string($koneksi, $value);
}

// Proses Pencarian
$q = '';
if (isset($_GET['q'])) {
    $q = escape($_GET['q']);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <style>
        .mx-auto {
            width: 800px;
        }

        .card {
            margin-top: 200px;
        }

        .btn-margin {
            margin-right: 10px;
        }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>
    <div class="mx-auto">
        <div class="card">
            <div class="card-header">
                Data Siswa
                <a href="data_siswa2.php" class="btn btn-success btn-margin">+ Data Siswa</a>
            </div>
            <div class="card-body">
                <!-- Formulir Pencarian -->
                <div class="mb-3">
                    <form method="get" action="">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Cari berdasarkan NIS atau Nama" name="q" value="<?php echo $q; ?>">
                            <button class="btn btn-outline-secondary" type="submit">Cari</button>
                        </div>
                    </form>
                </div>

                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">NIS</th>
                            <th scope="col">Nama</th>
                            <th scope="col">Ekskul</th>
                            <th scope="col">Jurusan</th>
                            <th scope="col">Wali Kelas</th>
                            <th scope="col">Foto Siswa</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Query SQL dengan penyesuaian pencarian
                        $sql2   = "SELECT siswa.*, jurusan.jurusan, ekskul.ekskul AS nama_ekskul, walikelas.walikelas, walikelas.nip
                                   FROM siswa 
                                   LEFT JOIN jurusan ON siswa.id_jurusan = jurusan.id_jurusan 
                                   LEFT JOIN ekskul ON siswa.id_ekskul = ekskul.id_ekskul 
                                   LEFT JOIN walikelas ON siswa.id_walikelas = walikelas.id_walikelas";

                        // Tambahkan klausa WHERE jika terdapat parameter pencarian
                        if (!empty($q)) {
                            $sql2 .= " WHERE siswa.nis LIKE '%$q%' OR siswa.nama LIKE '%$q%'";
                        }

                        $sql2 .= " ORDER BY siswa.id DESC";

                        $q2     = mysqli_query($koneksi, $sql2);
                        $urut   = 1;
                        while ($r2 = mysqli_fetch_array($q2)) {
                            $id          = $r2['id'];
                            $nis         = $r2['nis'];
                            $nama        = $r2['nama'];
                            $nama_ekskul = $r2['nama_ekskul'];
                            $jurusan     = $r2['jurusan'];
                            $wali_kelas  = $r2['walikelas'];
                            $nip         = $r2['nip'];
                            $foto_siswa  = $r2['foto_siswa'];
                        ?>
                            <tr>
                                <th scope="row"><?php echo $urut++ ?></th>
                                <td><?php echo $nis ?></td>
                                <td><?php echo $nama ?></td>
                                <td><?php echo $nama_ekskul ?></td>
                                <td><?php echo $jurusan ?></td>
                                <td><?php echo $wali_kelas ?></td>
                                <td>
                                    <img src="uploads/<?php echo $foto_siswa; ?>" alt="Foto Siswa" width="50">
                                </td>
                                <td>
                                    <a href="data_siswa2.php?op=edit&id=<?php echo $id ?>"><button type="button" class="btn btn-warning">Edit</button></a>
                                    <a href="data_siswa2.php?op=delete&id=<?php echo $id ?>" onclick="return confirm('Yakin mau menghapus data ini?')"><button type="button" class="btn btn-danger">Delete</button></a>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-e6HlGr5czF02rwQn5z2hAPYoO6O+VbZ/6jrnzF5xIKm4WgHdiVgag5S04jbQ" crossorigin="anonymous"></script>
</body>

</html>


