<?php
$page_title = "Manajemen User";

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/auth.php';
include_once __DIR__ . '/../include/header.php';

// Hanya admin yang boleh akses
checkRole(['admin']);

// CREATE
if (isset($_POST['add_user'])) {
    $name = $_POST['nama'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    $departemen_id = $_POST['departemen_id'];

    $check = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
    $check->execute([':email' => $email]);
    $exists = $check->fetchColumn();

    if ($exists > 0) {
       echo "<script>alert('Email sudah terdaftar, silakan gunakan email lain.'); window.location.href='/kpi-app/public/users/1';</script>";
        exit;
    }

    $sql = "INSERT INTO users (nama, email, password, role, departemen_id) 
        VALUES (?, ?, ?, ?, ?)";
	$stmt = $pdo->prepare($sql);
	$stmt->execute([$name, $email, $password, $role, $departemen_id]);
    
    echo "<script>alert('User berhasil ditambahkan'); window.location.href='/kpi-app/public/users/1';</script>";
    exit;
}

// DELETE
if ($action === 'delete' && $param) {
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
    $stmt->execute([':id' => $param]);

   echo "<script>alert('User berhasil dihapus'); window.location.href='/kpi-app/public/users/1';</script>";
   exit;
}

// READ departemen
$departemen = $pdo->query("SELECT * FROM departments");

// READ users
$total_users = $pdo->query("SELECT COUNT(*) 
    FROM users u 
    LEFT JOIN departments d ON u.departemen_id = d.id")->fetchColumn();
$total = (int)$total_users;

// Pagination setup
if ($action === 'page' && $param) {
    $page = (int)$param;
} else {
    $page = 1;
}
$limit = 3; // number of rows per page
$offset = ($page - 1) * $limit;

// Fetch users with pagination
$stmt = $pdo->prepare("
    SELECT u.*, d.name AS departemen 
    FROM users u 
    LEFT JOIN departments d ON u.departemen_id = d.id 
    ORDER BY u.id 
    LIMIT :limit OFFSET :offset
");
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->execute();
$users = $stmt;

$totalPages = ceil($total / $limit);

?>

 <h1 class="text-2xl font-bold text-center mt-4 mb-6">Manajemen User</h1>
  <div class="max-w-5xl mx-auto bg-white p-6 rounded-2xl shadow">  

    <!-- Form Tambah User -->
  <form method="POST" class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
      <input type="text" name="nama" placeholder="Nama" required
             class="p-2 border rounded w-full">
      <input type="email" name="email" placeholder="Email" required
             class="p-2 border rounded w-full">
      <input type="password" name="password" placeholder="Password" required
             class="p-2 border rounded w-full">

      <select name="role" class="p-2 border rounded w-full" required>
        <option value="">-- Pilih Role --</option>
        <option value="admin">Admin</option>
        <option value="manager">Manager</option>
        <option value="user">User</option>
      </select>

      <select name="departemen_id" class="p-2 border rounded w-full" required>
        <option value="">-- Pilih Departemen --</option>
        <?php while($d = $departemen->fetch(PDO::FETCH_ASSOC)): ?>
          <option value="<?= $d['id']; ?>"><?= $d['name']; ?></option>
        <?php endwhile; ?>
      </select>

      <button type="submit" name="add_user" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Tambah
        </button>
  </form>

<!-- Tabel User -->
<div class="overflow-x-auto">
  <table class="w-full border text-sm">
    <thead class="bg-gray-200">
      <tr>
        <th class="border p-2">Nama</th>
        <th class="border p-2">Email</th>
        <th class="border p-2">Role</th>
        <th class="border p-2">Departemen</th>
        <th class="border p-2">Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php 
      while($u = $users->fetch(PDO::FETCH_ASSOC)): ?>
      <tr>
        <td class="border p-2"><?= htmlspecialchars($u['nama']); ?></td>
        <td class="border p-2"><?= htmlspecialchars($u['email']); ?></td>
        <td class="border p-2"><?= ucfirst($u['role']); ?></td>
        <td class="border p-2"><?= $u['departemen'] ?: '-'; ?></td>
        <td class="border p-2">
          <a href="/kpi-app/public/users/delete/<?= $u['id'] ?>" 
             onclick="return confirm('Hapus user ini?')"
             class="bg-red-500 text-white px-2 py-1 rounded">
            Hapus
          </a> 
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
    <tbody>
        <?php 
        foreach ($users as $user): ?>
            <tr>
                <td class="border px-4 py-2"><?= $no++; ?></td>
                <td class="border px-4 py-2"><?= htmlspecialchars($user['nama']) ?></td>
                <td class="border px-4 py-2"><?= htmlspecialchars($user['email']) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
  </table>
</div>

<!-- Pagination Links -->
<div class="flex gap-2 mt-4">
  <?php for ($i = 1; $i <= $totalPages; $i++): ?>
    <a href="/kpi-app/public/users/page/<?= $i ?>"
       class="px-3 py-1 rounded <?= $i == $pageNumber ? 'bg-blue-600 text-white' : 'bg-gray-200' ?>">
      <?= $i ?>
    </a>
  <?php endfor; ?>
  <?php if ($page > 1): ?>
  <a href="/kpi-app/public/users/page/<?= $page - 1 ?>" class="px-3 py-1 bg-gray-200 rounded">Prev</a>
  <?php endif; ?>

  <a href="/kpi-app/public/users/page/<?= $page + 1 ?>" class="px-3 py-1 bg-gray-200 rounded">Next</a>
</div>

        <!-- Tombol kembali -->
    <div class="mt-4">
        <a href="/kpi-app/public/dashboard" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
            ⬅ Kembali ke Dashboard
        </a>
    </div>

  </div>
</body>
</html>