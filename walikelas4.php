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

$nip = "";
$walikelas = "";
$error = "";
$sukses = "";

if (isset($_GET['op'])) {
    $op = $_GET['op'];
} else {
    $op = "";
}

if ($op == 'delete') {
    $id_walikelas = $_GET['id'];

    // Periksa apakah data sedang digunakan sebelum menghapus
    $check_sql = "SELECT * FROM siswa WHERE id_walikelas = '$id_walikelas'";
    $check_query = mysqli_query($koneksi, $check_sql);

    if (mysqli_num_rows($check_query) > 0) {
        // Data sedang digunakan, tampilkan pesan kesalahan
        $error = "Data sedang digunakan oleh siswa. Tidak dapat dihapus.";
    } else {
        // Nonaktifkan foreign key checks sementara
        mysqli_query($koneksi, "SET foreign_key_checks = 0");

        // Hapus data jika tidak digunakan
        $delete_sql = "DELETE FROM walikelas WHERE id_walikelas = '$id_walikelas'";
        $delete_query = mysqli_query($koneksi, $delete_sql);

        // Aktifkan kembali foreign key checks
        mysqli_query($koneksi, "SET foreign_key_checks = 1");

        if ($delete_query) {
            $sukses = "Data berhasil dihapus";
            header("Location: walikelas.php");
            exit();
        } else {
            $error = "Gagal menghapus data: " . mysqli_error($koneksi);
        }
    }
}

if ($op == 'edit') {
    $id_walikelas = $_GET['id'];
    $sql2         = "SELECT * FROM walikelas WHERE id_walikelas = '$id_walikelas'";
    $q2           = mysqli_query($koneksi, $sql2);

    if (!$q2) {
        die("Query error: " . mysqli_error($koneksi));
    }

    $r2     = mysqli_fetch_array($q2);

    if (!$r2) {
        $error = "Data tidak ditemukan";
    } else {
        $nip = $r2['nip'];
        $walikelas = $r2['walikelas'];
    }
}

if (isset($_POST['simpan'])) {
    $nip = $_POST['nip'];
    $walikelas = $_POST['walikelas'];

    if ($nip && $walikelas) {
        // Validasi NIP harus 10 digit
        if (strlen($nip) !== 10) {
            $error = "NIP harus diisi dengan 10 digit.";
        } else {
            if ($op == 'edit') {
                $sql2 = "UPDATE walikelas SET nip = '$nip', walikelas = '$walikelas' WHERE id_walikelas = '$id_walikelas'";
                $q2   = mysqli_query($koneksi, $sql2);
                if ($q2) {
                    $sukses = "Data berhasil diupdate";
                    header("Location: walikelas.php");
                    exit();
                } else {
                    $error = "Data gagal diupdate: " . mysqli_error($koneksi);
                }
            } else {
                $sql2 = "INSERT INTO walikelas (nip, walikelas) VALUES ('$nip', '$walikelas')";
                $q2   = mysqli_query($koneksi, $sql2);
                if ($q2) {
                    $sukses = "Berhasil memasukkan data baru";
                    header("Location: walikelas.php");
                    exit();
                } else {
                    $error = "Gagal memasukkan data: " . mysqli_error($koneksi);
                }
            }
        }
    } else {
        $error = "Silakan masukkan semua data";
    }
}
?>

<!-- HTML Form -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah walikelas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>

<body>
    <?php include 'navbar.php'; ?>
    <div class="mx-auto">
        <div class="card">
            <div class="card-header">
                Create / Edit
            </div>
            <div class="card-body">
                <?php
                if (isset($error) && $error) {
                ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $error; ?>
                    </div>
                <?php
                }
                ?>

                <?php
                if (isset($sukses) && $sukses) {
                ?>
                    <div class="alert alert-success" role="alert">
                        <?php echo $sukses; ?>
                    </div>
                <?php
                }
                ?>
                <form action="" method="POST">
                    <div class="mb-3 row">
                        <label for="nip" class="col-sm-2 col-form-label">NIP</label>
                        <div class="col-sm-10">
                        <input type="text" class="form-control" id="nip" name="nip" value="<?php echo $nip; ?>" maxlength="10">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="walikelas" class="col-sm-2 col-form-label">Wali Kelas</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="walikelas" name="walikelas" value="<?php echo $walikelas; ?>">
                        </div>
                    </div>
                    <div class="col-12">
                        <input type="submit" name="simpan" value="<?php echo ($op == 'edit') ? 'Update data' : 'Simpan data'; ?>" class="btn btn-success">
                        <a href="walikelas.php" class="btn btn-success" style="margin-left: 20px;">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
