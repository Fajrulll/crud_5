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

$ekskul = "";
$ket_eks = ""; // Inisialisasi variabel $ket_eks di sini
$error = "";
$sukses = "";

// Pengecekan operasi
if (isset($_GET['op'])) {
    $op = $_GET['op'];
} else {
    $op = "";
}

if ($op == 'delete') {
    $id_ekskul = $_GET['id'];
    
    // Periksa apakah data sedang digunakan sebelum menghapus
    $check_sql = "SELECT * FROM siswa WHERE id_ekskul = ?";
    $check_statement = mysqli_prepare($koneksi, $check_sql);
    mysqli_stmt_bind_param($check_statement, "i", $id_ekskul);
    mysqli_stmt_execute($check_statement);
    $check_result = mysqli_stmt_get_result($check_statement);

    if (mysqli_num_rows($check_result) > 0) {
        // Data sedang digunakan, tampilkan pesan kesalahan
        $error = "Data sedang digunakan oleh siswa. Tidak dapat dihapus.";
    } else {
        // Nonaktifkan foreign key checks sementara
        mysqli_query($koneksi, "SET foreign_key_checks = 0");

        // Hapus data jika tidak digunakan
        $delete_sql = "DELETE FROM ekskul WHERE id_ekskul = ?";
        $delete_statement = mysqli_prepare($koneksi, $delete_sql);
        mysqli_stmt_bind_param($delete_statement, "i", $id_ekskul);
        $delete_query = mysqli_stmt_execute($delete_statement);

        // Aktifkan kembali foreign key checks
        mysqli_query($koneksi, "SET foreign_key_checks = 1");

        if ($delete_query) {
            $sukses = "Data berhasil dihapus";
            header("Location: ekskul.php");
            exit();
        } else {
            $error = "Gagal menghapus data: " . mysqli_error($koneksi);
        }
    }
}

if ($op == 'edit') {
    $id_ekskul = $_GET['id'];
    $select_sql = "SELECT * FROM ekskul WHERE id_ekskul = ?";
    $select_statement = mysqli_prepare($koneksi, $select_sql);
    mysqli_stmt_bind_param($select_statement, "i", $id_ekskul);
    mysqli_stmt_execute($select_statement);
    $select_result = mysqli_stmt_get_result($select_statement);

    if ($select_result) {
        $row = mysqli_fetch_array($select_result);

        if (!$row) {
            $error = "Data tidak ditemukan";
        } else {
            $ekskul = $row['ekskul'];
            $ket_eks = $row['ket_eks']; // Ambil nilai kolom keterangan
        }
    } else {
        die("Query error: " . mysqli_error($koneksi));
    }
}

if (isset($_POST['simpan'])) {
    $ekskul = isset($_POST['ekskul']) ? $_POST['ekskul'] : '';
    $ket_eks = isset($_POST['ket_eks']) ? $_POST['ket_eks'] : ''; // Ambil nilai keterangan

    // Validasi input kosong
    if (empty($ekskul) && empty($ket_eks)) {
        $error = "Lengkapi semua data";
    } elseif (empty($ekskul)) {
        $error = "Mohon isi ekskul";
    } elseif (empty($ket_eks)) {
        $error = "Mohon isi keterangan";
    } else {
        if ($op == 'edit') {
            // Prepared statement untuk UPDATE
            $update_sql = "UPDATE ekskul SET ekskul = ?, ket_eks = ? WHERE id_ekskul = ?";
            $update_statement = mysqli_prepare($koneksi, $update_sql);
            mysqli_stmt_bind_param($update_statement, "ssi", $ekskul, $ket_eks, $id_ekskul);
            $update_result = mysqli_stmt_execute($update_statement);

            if ($update_result) {
                $sukses = "Data berhasil diupdate";
                echo '<script>alert("Data berhasil diupdate!");</script>';
                header("Location: ekskul.php");
                exit();
            } else {
                $error = "Data gagal diupdate: " . mysqli_error($koneksi);
            }
        } else {
            // Prepared statement untuk INSERT
            $insert_sql = "INSERT INTO ekskul (ekskul, ket_eks) VALUES (?, ?)";
            $insert_statement = mysqli_prepare($koneksi, $insert_sql);
            mysqli_stmt_bind_param($insert_statement, "ss", $ekskul, $ket_eks);
            $insert_result = mysqli_stmt_execute($insert_statement);

            if ($insert_result) {
                $sukses = "Berhasil memasukkan data baru";
                echo '<script>alert("Data berhasil ditambahkan!");</script>';
                header("Location: ekskul.php");
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
    <title>Tambah Ekstrakurikuler</title>
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
                        <label for="ekskul" class="col-sm-2 col-form-label">Ekskul</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="ekskul" name="ekskul" value="<?php echo $ekskul; ?>">
                        </div>
                    </div>

                    <!-- Input untuk keterangan -->
                    <div class="mb-3 row">
                        <label for="keterangan" class="col-sm-2 col-form-label">Keterangan</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" id="ket_eks" name="ket_eks"><?php echo $ket_eks; ?></textarea>
                        </div>
                    </div>
                    
                    <div class="col-12">
                        <input type="submit" name="simpan" value="<?php echo ($op == 'edit') ? 'Update data' : 'Simpan data'; ?>" class="btn btn-success">
                        <a href="ekskul.php" class="btn btn-success" style="margin-left: 20px;">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
