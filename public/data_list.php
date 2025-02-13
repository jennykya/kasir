<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
  header("Location: login.php");
  exit;
}

require_once __DIR__ . '/../app/database.php';

$page = $_GET['page'] ?? 'barang';
$action = $_GET['action'] ?? null;
$id = $_GET['id'] ?? null;

// Daftar tabel yang valid
$tables = [
  'barang' => 'Barang',
  'pelanggan' => 'Pelanggan',
  'users' => 'Users'
];

if (!isset($tables[$page])) {
  die("Halaman tidak ditemukan.");
}

$title = $tables[$page];

// Ambil primary key secara dinamis
$stmt = $conn->prepare("SHOW KEYS FROM $page WHERE Key_name = 'PRIMARY'");
$stmt->execute();
$primaryKey = $stmt->fetch(PDO::FETCH_ASSOC)['Column_name'] ?? 'id';

// **EDIT DATA**
if ($action === 'edit' && $id) {
  $stmt = $conn->prepare("SELECT * FROM $page WHERE $primaryKey = :id");
  $stmt->execute(['id' => $id]);
  $editData = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$editData) {
    die("Data tidak ditemukan.");
  }

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fields = array_keys($editData);
    $setClause = implode(", ", array_map(fn($f) => "$f = :$f", $fields));

    $updateStmt = $conn->prepare("UPDATE $page SET $setClause WHERE $primaryKey = :id");
    foreach ($fields as $field) {
      $updateStmt->bindValue(":$field", $_POST[$field]);
    }
    $updateStmt->bindValue(":id", $id);

    if ($updateStmt->execute()) {
      header("Location: data_list.php?page=$page");
      exit;
    } else {
      echo "Gagal mengupdate data.";
    }
  }
}

// **HAPUS DATA**
if ($action === 'delete' && $id) {
  $stmt = $conn->prepare("DELETE FROM $page WHERE $primaryKey = :id");
  $stmt->execute(['id' => $id]);
  header("Location: data_list.php?page=$page");
  exit;
}

// **AMBIL DATA DARI DATABASE**
$stmt = $conn->prepare("SELECT * FROM $page");
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $title; ?></title>
  <style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { border: 1px solid #2d3748; padding: 10px; text-align: left; }
    th { background-color: rgb(142, 123, 37); }
    .btn { padding: 5px 10px; text-decoration: none; margin-right: 5px; }
    .edit { background-color: #4CAF50; color: white; }
    .delete { background-color: #f44336; color: white; }
  </style>
</head>
<body>
  <h1>Data <?= $title; ?></h1>

  <?php if ($action === 'edit' && isset($editData)): ?>
    <h2>Edit Data</h2>
    <form method="POST">
      <?php foreach ($editData as $field => $value): ?>
        <label><?= ucfirst($field); ?>:</label>
        <input type="text" name="<?= $field; ?>" value="<?= htmlspecialchars($value); ?>" required><br><br>
      <?php endforeach; ?>
      <button type="submit">Simpan</button>
      <a href="data_list.php?page=<?= $page; ?>">Batal</a>
    </form>
  <?php else: ?>
    <table>
      <thead>
        <tr>
          <?php if (!empty($data)): ?>
            <?php foreach (array_keys($data[0]) as $col): ?>
              <th><?= $col; ?></th>
            <?php endforeach; ?>
            <th>Aksi</th>
          <?php endif; ?>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($data)): ?>
          <?php foreach ($data as $row): ?>
            <tr>
              <?php foreach ($row as $key => $val): ?>
                <td><?= htmlspecialchars($val); ?></td>
              <?php endforeach; ?>
              <td>
                <a href="data_list.php?page=<?= $page; ?>&action=edit&id=<?= $row[$primaryKey]; ?>" class="btn edit">Edit</a>
                <a href="data_list.php?page=<?= $page; ?>&action=delete&id=<?= $row[$primaryKey]; ?>" class="btn delete" onclick="return confirm('Yakin ingin menghapus?');">Hapus</a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="100%">Tidak ada data tersedia.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  <?php endif; ?>
</body>
</html>