<?php
session_start();
require_once __DIR__ . '/../app/database.php';
if (!isset($_SESSION['user']) ) {
    header("Location: login.php");
    exit;
  }
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $query_check = "SELECT * FROM users WHERE email = :email";
    $stmt_check = $conn->prepare($query_check);
    $stmt_check->bindParam(':email', $email);
    $stmt_check->execute();

    if ($stmt_check->rowCount() > 0) {
        $error = "Email sudah terdaftar! Silakan gunakan email lain.";
    } else {
        $query = "INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, :role)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':role', $role);

        if ($stmt->execute()) {
            header("Location: login.php");
            exit;
        } else {
            $error = "Registrasi gagal! Coba lagi.";
        }
    }
}
$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Register</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .sidebar {
      width: 250px;
      height: 100vh;
      background-color: #1a202c; /* Warna gelap */
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      position: fixed;
      left: 0;
      top: 0;
    }
    .sidebar .header {
      padding: 1rem;
      text-align: center;
      border-bottom: 1px solid #2d3748;
    }
    .sidebar .header h2 {
      color: rgb(142, 123, 37);
      font-size: 1.5rem;
      font-weight: bold;
    }
    .sidebar nav {
      margin-top: 1rem;
    }
    .sidebar nav a {
      display: block;
      padding: 1rem;
      color: #cbd5e0; /* Warna teks */
      text-decoration: none;
      transition: background 0.3s, color 0.3s;
    }
    .sidebar nav a:hover {
      background-color: rgb(142, 123, 37);
      color: #fff;
    }
    .sidebar nav a.logout:hover {
      background-color: #e53e3e;
      color: #fff;
    }
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .register-container {
            background: rgb(142, 123, 37);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        h2 {
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }
        label {
            display: block;
            font-weight: bold;
        }
        input, select {
            width: 93%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            width: 100%;
            padding: 10px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background: #218838;
        }
        .error {
            color: red;
            margin-bottom: 10px;
        }
        p {
            margin-top: 10px;
        }
        a {
            color: #007bff;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="sidebar">
    <div class="header">
      <h2>Dashboard</h2>
    </div>
    <nav>
      <a href="admin_dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
      <a href="barang.php"><i class="fa-solid fa-box"></i> Pembelian</a>
      <a href="pelanggan.php"><i class="fa-solid fa-person"></i> Pelanggan</a>
      <a href="penjualan.php"><i class="fa-solid fa-cart-shopping"></i> Penjualan</a>
      <a href="laporan.php"><i class="fa-regular fa-file"></i> Laporan</a>
      <a href="register.php"><i class="fa-solid fa-user"></i> User</a>
      <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>
    </div>

    <div class="register-container">
        <h2>Register</h2>
        <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>
        <form method="POST" action="">
            <div class="form-group">
                <label>Username:</label>
                <input type="text" name="username" required>
            </div>

            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" required>
            </div>

            <div class="form-group">
                <label>Password:</label>
                <input type="password" name="password" required>
            </div>

            <div class="form-group">
                <label>Role:</label>
                <select name="role" required>
                    <option value="admin">Admin</option>
                    <option value="petugas">Petugas</option>
                </select>
            </div>

            <button type="submit">Daftar</button>
        </form>
        <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
    </div>
</body>
</html>