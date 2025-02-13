<?php
session_start();
session_destroy(); // Menghapus semua sesi pengguna
header('Location: login.php'); // Redirect ke halaman login
exit;
?>