<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
  header("Location: login.php");
  exit;
}

$user = $_SESSION['user'];

require_once __DIR__ . '/../app/database.php'; // Pastikan path benar

// Ambil total pelanggan
$stmt = $conn->prepare("SELECT COUNT(*) AS total_pelanggan FROM pelanggan");
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$total_pelanggan = $row['total_pelanggan'];

// Ambil total barang
$stmt = $conn->prepare("SELECT COUNT(*) AS total_barang FROM barang");
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$total_barang = $row['total_barang'];

// Ambil total users
$stmt = $conn->prepare("SELECT COUNT(*) AS total_users FROM users");
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$total_users = $row['total_users'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    /* Reset dan base styles */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    body {
      background-color: #f8fafc; /* mirip bg-gray-50 */
      font-family: sans-serif;
      line-height: 1.6;
    }
    .flex {
      display: flex;
    }
    /* Sidebar */
    .sidebar {
      width: 250px; /* sebanding dengan w-64 */
      height: 100vh; /* h-screen */
      background-color: #1a202c; /* bg-gray-900 */
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      position: fixed;
    }
    .sidebar .p-4 {
      padding: 1rem;
    }
    .sidebar h2 {
      color: rgb(142, 123, 37);
      font-size: 1.5rem;
      font-weight: bold;
      text-align: center;
      margin-bottom: 1rem;
    }
    .sidebar nav a {
      display: block;
      padding: 1rem;
      color: #e2e8f0; /* text-gray-300 */
      text-decoration: none;
      transition: background-color 0.3s, color 0.3s;
    }
    .sidebar nav a:hover {
      background-color: rgb(142, 123, 37); /* hover:bg-gray-800 */
      color: #ffffff;
    }
    .sidebar nav a.logout {
      transition: background-color 0.3s, color 0.3s;
    }
    .sidebar nav a.logout:hover {
      background-color: #c53030; /* warna merah yang lebih gelap */
      color: #ffffff;
    }
    /* Main Content */
    .main-content {
      margin-left: 250px; /* sejajar dengan lebar sidebar */
      padding: 2rem; /* p-8 */
      width: 100%;
    }
    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 2rem;
      background-color: #ffffff;
      padding: 1rem;
      border-radius: 8px;
      box-shadow: 0 1px 3px rgb(142, 123, 37);
    }
    .header h1 {
      font-size: 1.5rem;
      font-weight: bold;
      color: #2d3748;
    }
    .header .user-info {
      display: flex;
      align-items: center;
    }
    .header .user-info span {
      margin-right: 0.5rem;
      color: #2d3748; /* text-gray-600 */
    }
    .header .user-info img {
      width: 40px;
      height: 40px;
      border-radius: 50%;
    }
    /* Stats Cards */
    .stats-cards {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 1.5rem;
      margin-bottom: 2rem;
    }
    .stat-card {
      background-color: #ffffff;
      padding: 1.5rem;
      border-radius: 8px;
      box-shadow: 0 1px 3px rgb(142, 123, 37);
      border-left: 4px solidrgb(255, 244, 123); /* border-l-4 border-gray-800 */
      display: flex;
      align-items: center;
    }
    .stat-card i {
      font-size: 2rem; /* text-3xl */
      color: rgb(142, 123, 37);
    }
    .stat-card .stat-info {
      margin-left: 1rem;
    }
    .stat-card .stat-info h3 {
      margin: 0;
      font-size: 1rem;
      color: #2d3748; /* text-gray-500 */
    }
    .stat-card .stat-info p {
      margin: 0;
      font-size: 1.5rem; /* text-2xl */
      font-weight: bold;
      color: #2d3748;
    }
    .stat-card {
     transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .stat-card:hover {
    transform: translateY(-5px); /* Mengangkat card sedikit */
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2); /* Efek bayangan lebih besar */
    cursor: pointer;
    }
  </style>
</head>
<body>
  <div class="flex">
    <!-- Sidebar -->
    <div class="sidebar">
      <div class="p-4">
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

    <!-- Main Content -->
    <div class="main-content">
      <div class="header">
        <h1>Overview</h1>
        <div class="user-info">
          <span>Selamat datang admin, <?php echo htmlspecialchars($user['username']); ?></span>
          <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user['username']); ?>&background=868e96&color=fff" alt="User Avatar">
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="stats-cards">
      <div class="stats-cards">
        <div class="stat-card" onclick="location.href='data_list.php?page=pelanggan'">
          <i class="fa-solid fa-person"></i>
          <div class="stat-info">
            <h3>Total Pelanggan</h3>
            <p><?= $total_pelanggan; ?></p>
          </div>
        </div>
        <div class="stat-card" onclick="location.href='data_list.php?page=barang'">
          <i class="fa-solid fa-box"></i>
          <div class="stat-info">
            <h3>Total Produk</h3>
            <p><?= $total_barang; ?></p>
          </div>
        </div>
        <div class="stat-card" onclick="location.href='data_list.php?page=users'">
          <i class="fas fa-users"></i>
          <div class="stat-info">
            <h3>Total Users</h3>
            <p><?= $total_users; ?></p>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>