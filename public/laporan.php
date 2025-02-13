<?php
session_start();
require_once __DIR__ . '/../app/database.php';
if (!isset($_SESSION['user'])) {
  header('Location: login.php');
  exit;
}

$query = "
SELECT 
    t.id_penjualan,
    t.tanggal_penjualan,
    t.harga_jual,
    b.id_barang,
    b.nama_barang,
    b.harga_beli,
    b.stok,
    s.id_pelanggan,
    s.nama_pelanggan,
    s.alamat,
    s.no_hp
FROM penjualan t
JOIN barang b ON t.id_barang = b.id_barang
JOIN pelanggan s ON t.id_pelanggan = s.id_pelanggan
";

// Siapkan statement dan eksekusi query
$stmt = $conn->prepare($query);
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
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
        .content {
            margin-left: 250px;
            padding: 20px;
            transition: margin-left 0.3s;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        table, th, td {
            border: 1px solid #000;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: rgb(142, 123, 37);
        }
        /* CSS untuk mode cetak */
        .print-btn { margin: 10px 0; padding: 10px; background: green; color: white; border: none; cursor: pointer; }
        .print-btn:hover { background: darkgreen; }
    @media print {
        .sidebar, .print-button {
            display: none; /* Sembunyikan sidebar dan tombol cetak */
        }
        .content {
            margin-left: 0; /* Pastikan konten memenuhi halaman */
        }
    }
    </style>
</head>
<body>
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
        <h1>Laporan Penjualan</h1>
        <button class="print-btn" onclick="window.print()"><i class="fas fa-print"></i> Cetak Laporan</button>
        <table>
        <thead>
            <tr>
                <th>ID Penjualan</th>
                <th>Tanggal Penjualan</th>
                <th>Harga Jual</th>
                <th>ID Barang</th>
                <th>Nama Barang</th>
                <th>Harga Beli</th>
                <th>Stok</th>
                <th>ID Pelanggan</th>
                <th>Nama Pelanggan</th>
                <th>Alamat</th>
                <th>No. HP</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['id_penjualan']) ?></td>
                    <td><?= htmlspecialchars($row['tanggal_penjualan']) ?></td>
                    <td>Rp <?= htmlspecialchars(number_format($row['harga_jual'], 2, ',', '.')) ?></td>
                    <td><?= htmlspecialchars($row['id_barang']) ?></td>
                    <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                    <td>Rp <?= htmlspecialchars(number_format($row['harga_beli'], 2, ',', '.')) ?></td>
                    <td><?= htmlspecialchars($row['stok']) ?></td>
                    <td><?= htmlspecialchars($row['id_pelanggan']) ?></td>
                    <td><?= htmlspecialchars($row['nama_pelanggan']) ?></td>
                    <td><?= htmlspecialchars($row['alamat']) ?></td>
                    <td><?= htmlspecialchars($row['no_hp']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</body>
</html>