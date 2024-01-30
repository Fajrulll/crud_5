<?php
session_start();
$host = "localhost";
$user = "root";
$pass = "";
$db = "jurusan";

$koneksi = mysqli_connect($host, $user, $pass, $db);

function register($data) {
    global $koneksi;

    $username = htmlspecialchars($data['username']);
    $password = password_hash($data['password'], PASSWORD_DEFAULT);

    $query = "INSERT INTO users (username, password) VALUES ('$username', '$password')";
    mysqli_query($koneksi, $query);

    return mysqli_affected_rows($koneksi);
}

function login($data) {
    global $koneksi;

    $username = htmlspecialchars($data['username']);
    $password = htmlspecialchars($data['password']);

    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($koneksi, $query);

    if ($result) {
        $user = mysqli_fetch_assoc($result);
        if ($user && password_verify($password, $user['password'])) {
            return true;
        }
    }

    return false;
}

if (isset($_POST["register"])) {
    $password = htmlspecialchars($_POST['password']);
    $confirm_password = htmlspecialchars($_POST['confirm_password']);

    if ($password === $confirm_password) {
        if (register($_POST) > 0) {
            echo "<script>
                alert('Berhasil menambahkan');
                window.location.href='login.php'; // Redirect ke halaman login setelah registrasi berhasil
                </script>";
        } else {
            echo mysqli_error($koneksi);
        }
    } else {
        echo "<script>
            alert('Konfirmasi password tidak sesuai');
            </script>";
    }
}

if (isset($_POST["login"])) {
    if (login($_POST)) {
        session_start(); // Mulai session
        $_SESSION['username'] = $_POST['username']; // Set session dengan data yang relevan
        echo "<script>
            alert('Berhasil login');
            window.location.href='data_siswa.php';
            </script>";
    } else {
        echo "<script>
            alert('Gagal login, cek kembali username dan password');
            </script>";
    }
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <style>
        body {
            padding: 50px;
            background-color: #f8f9fa;
        }

        .container {
            margin-top: 50px;
        }

        .card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .register-button {
            position: absolute;
            top: 20px;
            right: 20px;
        }

        .mb-4 {
            margin-bottom: 1.5rem;
        }

        .form-label {
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container">
        <a href="register.php" class="btn btn-success register-button">Register</a>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <h1 class="card-header text-center">Login</h1>
                    <div class="card-body">
                        <form action="" method="post">
                            <ul class="list-unstyled">
                                <li class="mb-4">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" name="username" id="username" class="form-control">
                                </li>
                                <li class="mb-4">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" name="password" id="password" class="form-control">
                                </li>
                                <li>
                                    <button type="submit" name="login" class="btn btn-success">Login</button>
                                </li>
                            </ul>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-e6HlGr5czF02rwQn5z2hAPYoO6O+VbZ/6jrnzF5xIKm4WgHdiVgag5S04jbQ"
        crossorigin="anonymous"></script>
</body>

</html>

