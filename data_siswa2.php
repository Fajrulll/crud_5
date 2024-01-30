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

$nis            = "";
$nama           = "";
$id_ekskul      = "";
$id_jurusan     = "";
$id_walikelas   = "";
$foto_siswa_db  = ""; // Inisialisasi $foto_siswa_db
$sukses         = "";
$error          = "";
$foto_siswa     = ""; // Inisialisasi $foto_siswa di luar kondisi

if (isset($_GET['op'])) {
    $op = $_GET['op'];
} else {
    $op = "";
}

$fotoToDelete = "";

if ($op == 'delete') {
    $id     = $_GET['id'];
    $sql1   = "SELECT * FROM siswa WHERE id = '$id'";
    $q1     = mysqli_query($koneksi, $sql1);
    $r1     = mysqli_fetch_array($q1);
    $fotoToDelete = $r1['foto_siswa'];

    $sql2   = "DELETE FROM siswa WHERE id = '$id'";
    $q2     = mysqli_query($koneksi, $sql2);
    // Setelah operasi delete berhasil
    if ($op == 'delete' && $q2) {
        $sukses = "Berhasil mendelete data";
        header("Location: data_siswa.php");
        exit();
    } else {
        $error = "Gagal menghapus data";
    }
}

if ($op == 'edit') {
    $id     = mysqli_real_escape_string($koneksi, $_GET['id']);
    $sql1   = "SELECT * FROM siswa WHERE id = '$id' LIMIT 1";
    $q1     = mysqli_query($koneksi, $sql1);
    $r1     = mysqli_fetch_array($q1);
    $nis    = $r1['nis'];
    $nama   = $r1['nama'];
    $id_ekskul    = $r1['id_ekskul'];
    $id_jurusan   = $r1['id_jurusan'];
    $id_walikelas = $r1['id_walikelas'];
    $foto_siswa_db   = $r1['foto_siswa']; // Simpan nama gambar lama dari database

    if ($nis == '') {
        $error = "Data tidak ditemukan";
    }

    // Tambahkan inisialisasi $foto_siswa untuk menghindari pesan kesalahan
    $foto_siswa = "";
}

if (isset($_POST['simpan'])) {
    $nis          = $_POST['nis'];
    $nama         = $_POST['nama'];
    $id_ekskul    = $_POST['id_ekskul'];
    $id_jurusan   = $_POST['id_jurusan'];
    $id_walikelas = $_POST['id_walikelas'];

    // Hanya lakukan validasi foto jika ada file baru dipilih
    if (!empty($_FILES['foto_siswa']['name'])) {
        // Hapus file gambar lama jika ada
        if ($foto_siswa_db != '' && file_exists("uploads/$foto_siswa_db")) {
            unlink("uploads/$foto_siswa_db");
        }

        // Generate a unique ID based on the current time
        $uniqueID = uniqid();

        // Mendapatkan informasi file yang diunggah
        $foto_siswa = $uniqueID . '_' . $_FILES['foto_siswa']['name'];
        $tmp_name   = $_FILES['foto_siswa']['tmp_name'];
        $uploadsDir = "uploads/";

        // Pindahkan file yang diunggah ke folder "uploads"
        if (move_uploaded_file($tmp_name, $uploadsDir . $foto_siswa)) {
            // File berhasil diunggah
        } else {
            $error = "Gagal mengunggah file";
        }
    } else {
        // Jika tidak ada file yang diunggah, gunakan gambar sebelumnya atau tampilkan pesan error
        if ($op == 'add') {
            $errors[] = "- Foto siswa harus diisi";
        } elseif ($op == 'edit' && empty($foto_siswa_db)) {
            $errors[] = "- Foto siswa harus diisi";
        } else {
            // Gunakan foto yang sudah ada jika tersedia
            $foto_siswa = $foto_siswa_db;
        }
    }

    // Validasi input
    $errors = array();

    // Validasi NIS harus diisi dengan angka 4 digit
    if (!preg_match('/^[0-9]{4}$/', $nis)) {
        $errors[] = "- NIS harus diisi dengan angka 4 digit";
    }

    if (empty($nis)) {
        $errors[] = "- NIS harus diisi";
    }

    if (empty($nama)) {
        $errors[] = "- Nama harus diisi";
    }

    if (empty($id_ekskul)) {
        $errors[] = "- Ekstrakurikuler harus diisi";
    }

    if (empty($id_jurusan)) {
        $errors[] = "- Jurusan harus diisi";
    }

    if (empty($id_walikelas)) {
        $errors[] = "- Wali Kelas harus diisi";
    }

    if (empty($foto_siswa)) {
        $errors[] = "- foto siswa harus diisi";
    }

    // Hanya lakukan validasi foto jika ada file baru yang diunggah pada mode tambah atau edit
    if (empty($foto_siswa) && empty($_FILES['foto_siswa']['name']) && ($op == 'simpan' || $op == 'edit')) {
        $errors[] = "- Foto siswa harus diisi";
    }

    // Menampilkan pesan error jika ada
    if (!empty($errors)) {
        // Menggabungkan pesan-pesan kesalahan menjadi satu pesan
        $error = implode('<br>', $errors);
    } else {
        // Lanjutkan dengan proses simpan data siswa
        if ($nis && $nama && $id_ekskul && $id_jurusan && $id_walikelas) {
            if ($op == 'edit') {
                $sql1 = "UPDATE siswa SET nis = '$nis', nama = '$nama', id_ekskul = '$id_ekskul', id_jurusan = '$id_jurusan', id_walikelas = '$id_walikelas', foto_siswa = '$foto_siswa' WHERE id = '$id'";
            } else {
                $sql1 = "INSERT INTO siswa (nis, nama, id_ekskul, id_jurusan, id_walikelas, foto_siswa) VALUES ('$nis', '$nama', '$id_ekskul', '$id_jurusan', '$id_walikelas', '$foto_siswa')";
            }

            $q1 = mysqli_query($koneksi, $sql1);
            if ($q1) {
                $sukses = ($op == 'edit') ? "Berhasil mengedit data" : "Berhasil menambahkan data";
                echo '<script>alert("' . $sukses . '"); window.location.href = "data_siswa.php";</script>';
                exit();
            } else {
                if (mysqli_errno($koneksi) == 1062) {
                    // Kode error 1062 menunjukkan duplikasi kunci (nis)
                    $error = "NIS ini sudah terdaftar. Silakan gunakan NIS yang berbeda.";
                } else {
                    $error = "Gagal memproses data: " . mysqli_error($koneksi);
                }
            }
            
        } else {
            $error = "Silakan masukkan semua data";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Data siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
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
                create / edit
            </div>
            <div class="card-body">
            <?php
                if ($error) {
                    echo '<div class="alert alert-danger" role="alert">' . $error . '</div>';
                }

                if ($sukses) {
                    echo '<div class="alert alert-success" role="alert">' . $sukses . '</div>';
                }
                ?>

                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="mb-3 row">
                        <label for="nis" class="col-sm-2 col-form-label">NIS</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="nis" name="nis" value="<?php echo $nis ?>">
                        </div>
                    </div>

                        <div class="mb-3 row">
                            <label for="nama" class="col-sm-2 col-form-label">Nama</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="nama" id="nama" value="<?php echo $nama ?>">
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="id_walikelas" class="col-sm-2 col-form-label">Wali Kelas</label>
                            <div class="col-sm-10">
                                <select class="form-control" name="id_walikelas" id="id_walikelas">
                                    <option value="">- Pilih Wali Kelas -</option>
                                    <?php
                                    $sql_walikelas = "SELECT * FROM walikelas";
                                    $result_walikelas = mysqli_query($koneksi, $sql_walikelas);

                                    while ($row_walikelas = mysqli_fetch_assoc($result_walikelas)) {
                                        $selected = ($id_walikelas == $row_walikelas['id_walikelas']) ? "selected" : "";
                                        echo "<option value='{$row_walikelas['id_walikelas']}' $selected>{$row_walikelas['walikelas']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="id_ekskul" class="col-sm-2 col-form-label">Ekstrakurikuler</label>
                            <div class="col-sm-10">
                                <select class="form-control" name="id_ekskul" id="id_ekskul">
                                    <option value="">- Pilih ekskul -</option>
                                    <?php
                                    $sql_ekskul = "SELECT * FROM ekskul";
                                    $result_ekskul = mysqli_query($koneksi, $sql_ekskul);

                                    while ($row_ekskul = mysqli_fetch_assoc($result_ekskul)) {
                                        $selected = ($id_ekskul == $row_ekskul['id_ekskul']) ? "selected" : "";
                                        echo "<option value='{$row_ekskul['id_ekskul']}' $selected>{$row_ekskul['ekskul']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="id_jurusan" class="col-sm-2 col-form-label">Jurusan</label>
                            <div class="col-sm-10">
                                <select class="form-control" name="id_jurusan" id="id_jurusan">
                                    <option value="">- Pilih Jurusan -</option>
                                    <?php
                                    $sql_jurusan = "SELECT * FROM jurusan";
                                    $result_jurusan = mysqli_query($koneksi, $sql_jurusan);

                                    while ($row_jurusan = mysqli_fetch_assoc($result_jurusan)) {
                                        $selected = ($id_jurusan == $row_jurusan['id_jurusan']) ? "selected" : "";
                                        echo "<option value='{$row_jurusan['id_jurusan']}' $selected>{$row_jurusan['jurusan']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="foto_siswa" class="col-sm-2 col-form-label">Foto Siswa</label>
                            <div class="col-sm-10">
                                <?php
                                // Ini kondisi foto sebelumnya hanya ada di mode edit dan bukan di mode tambah
                                if ($op != 'add' && $foto_siswa_db != '') {
                                    echo '<p>Foto Sebelumnya:</p>';
                                    echo '<img src="uploads/' . $foto_siswa_db . '" alt="Current Photo" style="max-width: 200px; max-height: 200px;">';
                                    echo '<br><br>';
                                }
                                ?>
                                <input type="file" class="form-control" name="foto_siswa" id="foto_siswa">
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="mb-3">
                                <button type="submit" name="simpan" class="btn btn-success">Simpan data</button>
                                <a href="data_siswa.php" class="btn btn-success" style="margin-left: 20px;">Kembali</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </body>

    </html>