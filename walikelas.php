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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Wali Kelas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <style>
        .mx-auto {
            width: 800px;
        }

        .card {
            margin-top: 10px;
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
                Data wali kelas
                <a href="walikelas4.php" class="btn btn-success btn-margin">+ Wali Kelas</a>
                <a href="data_siswa.php">
                    <button class="btn btn-success">Kembali</button>
                </a>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">NIP</th>
                            <th scope="col">Wali kelas</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql2   = "SELECT * FROM walikelas ORDER BY id_walikelas DESC";
                        $q2     = mysqli_query($koneksi, $sql2);
                        $urut   = 1;
                        while ($r2 = mysqli_fetch_array($q2)) {
                            $id      = $r2['id_walikelas'];
                            $nip     = $r2['nip'];
                            $walikelas = $r2['walikelas'];
                        ?>
                            <tr>
                                <th scope="row"><?php echo $urut++ ?></th>
                                <td><?php echo $nip ?></td>
                                <td><?php echo $walikelas ?></td>
                                <td>
                                    <a href="walikelas4.php?op=edit&id=<?php echo $id ?>"><button type="button" class="btn btn-warning">Edit</button></a>
                                    <a href="walikelas4.php?op=delete&id=<?php echo $id ?>" onclick="return confirm('Yakin mau menghapus data ini?')"><button type="button" class="btn btn-danger">Delete</button></a>
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
</body>
</html>
