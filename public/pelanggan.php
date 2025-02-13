<?php
session_start();
require_once __DIR__ . '/../app/database.php';
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_pelanggan = $_POST['nama_pelanggan'];
    $alamat        = $_POST['alamat'];
    $no_hp         = $_POST['no_hp'];
    
    // Gunakan parameter binding untuk keamanan
    $query = "INSERT INTO pelanggan ( nama_pelanggan, alamat, no_hp) 
              VALUES ( :nama_pelanggan, :alamat, :no_hp)";
    
    $stmt = $conn->prepare($query);
    $params = [
        ':nama_pelanggan' => $nama_pelanggan,
        ':alamat'        => $alamat,
        ':no_hp'         => $no_hp
    ];
    if ($stmt->execute($params)) {
        echo "<script>alert('Pelanggan added successfully!');</script>";
    }
}

$user = $_SESSION['user'];

$query = "SELECT * FROM pelanggan";
$stmt = $conn->prepare($query);
$stmt->execute();
$pelanggan = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Pelanggan</title>
  <!-- Masih menggunakan Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }
    table, th, td {
      border: 1px solid #ddd;
    }
    th, td {
      padding: 10px;
      text-align: left;
    }
    th {
      background-color: #1a202c;
      color: white;
    }
    /* Reset dasar */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    body {
      font-family: sans-serif;
      background-color: #f4f6f9;
      line-height: 1.6;
    }
    /* Sidebar */
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
    /* Konten Utama */
    .content {
      margin-left: 250px;
      padding: 20px;
      transition: margin-left 0.3s;
    }
    /* Container Form */
    .container {
      width: 50%;
      margin: 20px auto;
      padding: 20px;
      background-color: rgb(142, 123, 37);
      border-radius: 8px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
    .container h2 {
      color: #1a202c;
      margin-bottom: 15px;
      text-align: center;
    }
    .form-group {
      margin-bottom: 15px;
    }
    .form-group label {
      display: block;
      margin-bottom: 5px;
      color: #1a202c;
      font-weight: bold;
    }
    .form-group input[type="text"],
    .form-group input[type="number"],
    .form-group textarea {
      width: 100%;
      padding: 8px;
      border: 1px solid #ddd;
      border-radius: 4px;
      font-size: 1rem;
    }
    button {
      padding: 10px 20px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-weight: bold;
      text-transform: uppercase;
      letter-spacing: 1px;
      background-color: #1a202c;
      color: #fff;
      box-shadow: 0 2px 4px rgba(0,0,0,0.2);
      transition: background-color 0.3s, box-shadow 0.3s;
    }
    button:hover {
      background-color: #fff;
      color: #1a202c;
      box-shadow: 0 4px 8px rgba(0,0,0,0.3);
    }
  </style>
</head>
<body>
  <!-- Sidebar -->
  <div class="sidebar">
    <div class="header">
      <h2>Dashboard</h2>
    </div>
    <nav>
    <?php if ($user['role'] === 'admin') : ?>
        <a href="admin_dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
    <?php else : ?>
        <a href="petugas_dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
    <?php endif; ?>

      <a href="barang.php"><i class="fa-solid fa-box"></i> Pembelian</a>
      <a href="pelanggan.php"><i class="fa-solid fa-person"></i> Pelanggan</a>
      <a href="penjualan.php"><i class="fa-solid fa-cart-shopping"></i> Penjualan</a>
      <a href="laporan.php"><i class="fa-regular fa-file"></i> Laporan</a>

      <?php if ($user['role'] === 'admin') : ?>
      <a href="register.php"><i class="fa-solid fa-user"></i> User</a>
      <?php endif; ?>

      <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>
    </div>

  <!-- Konten Utama -->
  <div class="content">
    <div class="container">
      <h2>Masukkan Data Pelanggan</h2>
      <form method="POST" action="">
        <div class="form-group">
          <label>Nama Pelanggan:</label>
          <textarea name="nama_pelanggan" rows="4" required></textarea>
        </div>
        <div class="form-group">
          <label>Alamat:</label>
          <input type="text" name="alamat" required>
        </div>
        <div class="form-group">
          <label>Nomor Handphone:</label>
          <input type="number" name="no_hp" required>
        </div>
        <button type="submit">Simpan</button>
      </form>
    </div>
  </div>

  <div class="content">
    <div class="container">
      <h2>Daftar Pelanggan</h2>
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Nama Pelanggan</th>
            <th>Alamat</th>
            <th>Nomor Handphone</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($pelanggan as $item): ?>
          <tr>
            <td><?= htmlspecialchars($item['id_pelanggan']); ?></td>
            <td><?= htmlspecialchars($item['nama_pelanggan']); ?></td>
            <td><?= htmlspecialchars($item['alamat']); ?></td>
            <td><?= htmlspecialchars($item['no_hp']); ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>