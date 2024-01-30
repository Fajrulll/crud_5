<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <style>
        .navbar {
            background-color: #1e8449; /* Ganti dengan warna hijau yang lebih gelap */
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            color: #ecf0f1;
            font-weight: bold;
            transition: color 0.3s ease;
        }

        .navbar-toggler-icon {
            background-color: #ecf0f1;
            transition: background-color 0.3s ease;
        }

        .navbar-nav {
            margin-left: auto;
        }

        .navbar-nav a {
            color: #ecf0f1;
            margin-right: 15px;
            font-weight: bold;
            transition: color 0.3s ease;
        }

        .navbar-nav a:hover {
            color: #2c3e50;
        }

        .navbar-nav a::after {
            content: '';
            display: block;
            height: 2px;
            width: 0;
            background: #2c3e50;
            transition: width 0.3s ease;
        }

        .navbar-nav a:hover::after {
            width: 100%;
        }

        .mx-auto {
            width: 800px;
        }

        .card {
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <!-- navbar.php -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="background-color: #1e8449;">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Data Siswa</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="data_siswa.php">Data Siswa</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="jurusan.php">Jurusan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="ekskul.php">Ekskul</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="walikelas.php">Wali kelas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="mx-auto">
        <div class="card">
            <div class="card-header">
                create / edit
            </div>
            
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-pzjw8P+L6D5z68uE7TBmGggXNNqFInYzJ3H9ISqEibYO7FsVPKVI3F5LDD3A7x9"
        crossorigin="anonymous"></script>
</body>

</html>
