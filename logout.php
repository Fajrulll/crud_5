

<?php
// start session
session_start();

// Hapus semua data sesi
session_destroy();

// beralih ke halaman login
header("Location: login.php");
exit();
?>
