<?php
session_start();
require_once __DIR__ . '/../app/database.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_barang = $_POST['id_barang'];
    $id_pelanggan = $_POST['id_pelanggan'];
    $stok = $_POST['stok'];
    $harga_jual = $_POST['harga_jual'];
    $tanggal_penjualan = $_POST['tanggal_penjualan'];

    // Cek stok barang sebelum penjualan
    $queryStok = "SELECT stok FROM barang WHERE id_barang = :id_barang";
    $stmt = $conn->prepare($queryStok);
    $stmt->execute([':id_barang' => $id_barang]);
    $stokBarang = $stmt->fetchColumn();

    if ($stokBarang < $stok) {
        echo "<script>alert('Stok tidak mencukupi!');</script>";
    } else {
        // Insert data penjualan ke database
        $query = "INSERT INTO penjualan (id_barang, id_pelanggan, stok, harga_jual, tanggal_penjualan) VALUES (:id_barang, :id_pelanggan, :stok, :harga_jual, :tanggal_penjualan)";
        $stmt = $conn->prepare($query);
        $stmt->execute([
            ':id_barang' => $id_barang,
            ':id_pelanggan' => $id_pelanggan,
            ':stok' => $stok,
            ':harga_jual' => $harga_jual,
            ':tanggal_penjualan' => $tanggal_penjualan
        ]);

        // Kurangi stok barang
        $updateQuery = "UPDATE barang SET stok = stok - :stok WHERE id_barang = :id_barang";
        $stmt = $conn->prepare($updateQuery);
        $stmt->execute([
            ':stok' => $stok,
            ':id_barang' => $id_barang
        ]);

        echo "<script>alert('Penjualan berhasil ditambahkan!');</script>";
    }
}

// Ambil daftar barang dan pelanggan
$queryBarang = "SELECT * FROM barang";
$stmtBarang = $conn->prepare($queryBarang);
$stmtBarang->execute();
$barang = $stmtBarang->fetchAll(PDO::FETCH_ASSOC);

$queryPelanggan = "SELECT * FROM pelanggan";
$stmtPelanggan = $conn->prepare($queryPelanggan);
$stmtPelanggan->execute();
$pelanggan = $stmtPelanggan->fetchAll(PDO::FETCH_ASSOC);

$user = $_SESSION['user'];

$query = "SELECT * FROM penjualan";
$stmt = $conn->prepare($query);
$stmt->execute();
$penjualan = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Penjualan</title>
  <!-- Hapus Tailwind CSS -->
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
      background-color: #1a202c; /* bg-gray-900 */
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
      color: #cbd5e0; /* text-gray-300 */
      text-decoration: none;
      transition: background 0.3s, color 0.3s;
    }
    .sidebar nav a:hover {
      background-color:rgb(142, 123, 37); /* hover:bg-gray-800 */
      color: #fff;
    }
    .sidebar nav a.logout:hover {
      background-color: #e53e3e; /* hover:bg-red-600 */
      color: #fff;
    }
    /* Content */
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
      background-color: #1a202c; /* Sesuaikan dengan tema */
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

    <div class="content">
    <div class="container">
    <h2>Form Penjualan Barang</h2>
    <form method="POST" action="">
        <label>Pelanggan:</label>
        <select name="id_pelanggan" required>
            <?php foreach ($pelanggan as $item): ?>
                <option value="<?= $item['id_pelanggan']; ?>"><?= htmlspecialchars($item['nama_pelanggan']); ?></option>
            <?php endforeach; ?>
        </select>
        <label>Barang:</label>
        <select name="id_barang" required>
            <?php foreach ($barang as $item): ?>
                <option value="<?= $item['id_barang']; ?>"><?= htmlspecialchars($item['nama_barang']); ?></option>
            <?php endforeach; ?>
        </select>
        <label>Terjual:</label>
        <input type="number" name="stok" required>
        <label>Harga Jual:</label>
        <input type="number" name="harga_jual" required>
        <label>Tanggal Penjualan:</label>
        <input type="date" name="tanggal_penjualan" required>
        <button type="submit">Simpan</button>
    </form>
    </div>
    </div>

    <div class="content">
    <div class="container">
      <h2>Daftar Penjualan</h2>
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>ID Barang</th>
            <th>ID Pelanggan</th>
            <th>Terjual</th>
            <th>Harga Jual</th>
            <th>Tanggal Penjualan</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($penjualan as $item): ?>
          <tr>
            <td><?= htmlspecialchars($item['id_penjualan']); ?></td>
            <td><?= htmlspecialchars($item['id_barang']); ?></td>
            <td><?= htmlspecialchars($item['id_pelanggan']); ?></td>
            <td><?= htmlspecialchars($item['stok']); ?></td>
            <td><?= htmlspecialchars($item['harga_jual']); ?></td>
            <td><?= htmlspecialchars($item['tanggal_penjualan']); ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
