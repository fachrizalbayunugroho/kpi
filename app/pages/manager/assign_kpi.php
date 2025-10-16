<?php
$page_title = "Assign KPI ke User";

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/auth.php';
include_once __DIR__ . '/../include/header.php';
$tahun = 2025;
$bulan = 9;

// Ambil data template
if ($action === 'assign' && $param) {
$stmt = $pdo->prepare("SELECT t.*, d.name AS departemen 
                        FROM kpi_template t
                        LEFT JOIN departments d ON t.departemen_id = d.id
                        WHERE t.id = :id");
$stmt->execute([':id' => $param]);
$template = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (!$template) {
    die("Template tidak ditemukan!");
}

// Ambil daftar user di departemen template
$stmt = $pdo->prepare("SELECT * FROM users 
                        WHERE departemen_id = :dept_id 
                        AND role = 'user'
                        ORDER BY nama ASC");
$stmt->execute([':dept_id' => $template['departemen_id']]);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Jika submit assign
if (isset($_POST['assign'])) {
    if (isset($_POST['user_ids'])) {
        foreach ($_POST['user_ids'] as $user_id) {
            // Cek apakah sudah ada assignment
            $check = $pdo->prepare("SELECT COUNT(*) FROM kpi_user
                                     WHERE template_id = :tid AND user_id = :uid");
            $check->execute([':tid' => $param, ':uid' => $user_id]);
            $exists = $check->fetchColumn();

            if (!$exists) {
                $stmt = $pdo->prepare("INSERT INTO kpi_user (template_id, user_id, tahun, bulan, assigned_at)
                                        VALUES (:tid, :uid, :tahun, :bulan, NOW())");
                $stmt->execute([':tid' => $param, 
                				':uid' => $user_id,
                				':tahun' => $tahun,
                				':bulan' => $bulan]);
            }
        }
    }
    echo "<script>alert('Assigned!'); window.location.href='/kpi-app/public/kpi_templates/assign/$param';</script>";
    exit;
}

// Ambil user yang sudah diassign
$stmt = $pdo->prepare("SELECT u.* FROM kpi_user a
                        JOIN users u ON a.user_id = u.id
                        WHERE a.template_id = :tid");
$stmt->execute([':tid' => $param]);
$assigned_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
  <div class="max-w-4xl mx-auto bg-white p-6 rounded-2xl shadow mt-4">

    <h1 class="text-2xl font-bold mb-4">Assign KPI ke User</h1>

    <!-- Detail Template -->
    <div class="mb-6 p-4 border rounded bg-gray-50">
      <p><strong>Nama Template:</strong> <?= htmlspecialchars($template['nama_template']); ?></p>
      <p><strong>Departemen:</strong> <?= htmlspecialchars($template['departemen']); ?></p>
    </div>

    <!-- Form Assign -->
    <form method="POST" class="mb-6">
      <h2 class="font-semibold mb-2">Pilih User</h2>
      <div class="space-y-2">
        <?php foreach ($users as $u): ?>
          <label class="flex items-center space-x-2">
            <input type="checkbox" name="user_ids[]" value="<?= $u['id']; ?>"
              <?= in_array($u['id'], array_column($assigned_users, 'id')) ? 'checked disabled' : ''; ?>>
            <span><?= htmlspecialchars($u['nama']); ?> (<?= $u['email']; ?>)</span>
          </label>
        <?php endforeach; ?>
      </div>
      <button type="submit" name="assign"
              class="mt-4 bg-blue-600 text-white px-4 py-2 rounded">
        Assign
      </button>
    </form>

    <!-- List User yang sudah diassign -->
    <h2 class="font-semibold mb-2">User yang sudah diassign</h2>
    <ul class="list-disc ml-6">
      <?php foreach ($assigned_users as $au): ?>
        <li><?= htmlspecialchars($au['nama']); ?> (<?= $au['email']; ?>)</li>
      <?php endforeach; ?>
    </ul>

    <div class="mt-4">
      <a href="/kpi-app/public/kpi_templates" class="bg-gray-600 text-white px-4 py-2 rounded">Kembali</a>
    </div>
  </div>
</body>
</html>
