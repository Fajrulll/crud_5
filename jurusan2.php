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

$jurusan = "";
$ket_jur = ""; // Inisialisasi variabel keterangan

if (isset($_GET['op'])) {
    $op = $_GET['op'];
} else {
    $op = "";
}

if ($op == 'delete') {
    $id_jurusan = $_GET['id'];

    
    // Periksa apakah data sedang digunakan sebelum menghapus
    $check_sql = "SELECT * FROM siswa WHERE id_jurusan = ?";
    $check_statement = mysqli_prepare($koneksi, $check_sql);
    mysqli_stmt_bind_param($check_statement, "i", $id_jurusan);
    mysqli_stmt_execute($check_statement);
    $check_result = mysqli_stmt_get_result($check_statement);

    if (mysqli_num_rows($check_result) > 0) {
        // Data sedang digunakan, tampilkan pesan kesalahan
        $error = "Data sedang digunakan oleh siswa. Tidak dapat dihapus.";
    } else {
        // Nonaktifkan foreign key checks sementara
        mysqli_query($koneksi, "SET foreign_key_checks = 0");

        // Hapus data jika tidak digunakan
        $delete_sql = "DELETE FROM jurusan WHERE id_jurusan = ?";
        $delete_statement = mysqli_prepare($koneksi, $delete_sql);
        mysqli_stmt_bind_param($delete_statement, "i", $id_jurusan);
        $delete_query = mysqli_stmt_execute($delete_statement);

        // Aktifkan kembali foreign key checks
        mysqli_query($koneksi, "SET foreign_key_checks = 1");

        if ($delete_query) {
            $sukses = "Data berhasil dihapus";
            header("Location: jurusan.php");
            exit();
        } else {
            $error = "Gagal menghapus data: " . mysqli_error($koneksi);
        }
    }
}



if ($op == 'edit') {
    $id_jurusan = $_GET['id'];
    
    // Prepared statement untuk SELECT
    $selectQuery = "SELECT * FROM jurusan WHERE id_jurusan = ?";
    $selectStatement = mysqli_prepare($koneksi, $selectQuery);
    mysqli_stmt_bind_param($selectStatement, "i", $id_jurusan);
    $selectResult = mysqli_stmt_execute($selectStatement);

    if ($selectResult) {
        $r2 = mysqli_stmt_get_result($selectStatement);
        $row = mysqli_fetch_array($r2);

        if (!$row) {
            $error = "Data tidak ditemukan";
        } else {
            $jurusan = $row['jurusan'];
            $ket_jur = $row['ket_jur']; // Ambil nilai kolom keterangan
        }
    } else {
        die("Query error: " . mysqli_error($koneksi));
    }
}

if (isset($_POST['simpan'])) {
    $jurusan = isset($_POST['jurusan']) ? $_POST['jurusan'] : '';
    $ket_jur = isset($_POST['ket_jur']) ? $_POST['ket_jur'] : ''; // Ambil nilai keterangan

    // Validasi input kosong
    if (empty($jurusan) && empty($ket_jur)) {
        $error = "Lengkapi semua data";
    } elseif (empty($ket_jur)) {
        $error = "Mohon isi keterangan";
    } elseif (empty($jurusan)) {
        $error = "Mohon isi jurusan";
    } else {
        if ($op == 'edit') {
            // Prepared statement untuk UPDATE
            $updateQuery = "UPDATE jurusan SET jurusan = ?, ket_jur = ? WHERE id_jurusan = ?";
            $updateStatement = mysqli_prepare($koneksi, $updateQuery);
            mysqli_stmt_bind_param($updateStatement, "ssi", $jurusan, $ket_jur, $id_jurusan);
            $updateResult = mysqli_stmt_execute($updateStatement);

            if ($updateResult) {
                $sukses = "Data berhasil diupdate";
                echo '<script>alert("Data berhasil diupdate!");</script>';
                header("Location: jurusan.php");
                exit();
            } else {
                $error = "Data gagal diupdate: " . mysqli_error($koneksi);
            }
        } else {
            // Prepared statement untuk INSERT
            $insertQuery = "INSERT INTO jurusan (jurusan, ket_jur) VALUES (?, ?)";
            $insertStatement = mysqli_prepare($koneksi, $insertQuery);
            mysqli_stmt_bind_param($insertStatement, "ss", $jurusan, $ket_jur);
            $insertResult = mysqli_stmt_execute($insertStatement);

            if ($insertResult) {
                $sukses = "Berhasil memasukkan data baru";
                echo '<script>alert("Data berhasil ditambahkan!");</script>';
                header("Location: jurusan.php");
                exit();
            } else {
                $error = "Gagal memasukkan data: " . mysqli_error($koneksi);
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Jurusan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <style>
        .mx-auto {
            width: 800px;
        }

        .card {
            margin-top: 10px;
        }
    </style>
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
                        <label for="Jurusan" class="col-sm-2 col-form-label">Jurusan</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="jurusan" name="jurusan" value="<?php echo $jurusan; ?>">
                        </div>
                    </div>

                    <!-- Input untuk keterangan -->
                    <div class="mb-3 row">
                        <label for="Keterangan" class="col-sm-2 col-form-label">Keterangan</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" id="ket_jur" name="ket_jur"><?php echo $ket_jur; ?></textarea>
                        </div>
                    </div>

                    <div class="col-12">
                        <input type="submit" name="simpan" value="<?php echo ($op == 'edit') ? 'Update data' : 'Simpan data'; ?>" class="btn btn-success">
                        <a href="jurusan.php" class="btn btn-success" style="margin-left: 20px;">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
