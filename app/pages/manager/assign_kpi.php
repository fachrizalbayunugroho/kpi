<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/auth.php';

// Pastikan ada template_id
if (!isset($_GET['template_id'])) {
    die("Template ID tidak ditemukan!");
}
$template_id = (int) $_GET['template_id'];

// Ambil data template
$stmt = $pdo->prepare("SELECT t.*, d.name AS departemen 
                        FROM kpi_templates t
                        LEFT JOIN departments d ON t.departemen_id = d.id
                        WHERE t.id = :id");
$stmt->execute([':id' => $template_id]);
$template = $stmt->fetch(PDO::FETCH_ASSOC);

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
            $check = $pdo->prepare("SELECT COUNT(*) FROM kpi_assignments 
                                     WHERE template_id = :tid AND user_id = :uid");
            $check->execute([':tid' => $template_id, ':uid' => $user_id]);
            $exists = $check->fetchColumn();

            if (!$exists) {
                $stmt = $pdo->prepare("INSERT INTO kpi_assignments (template_id, user_id, assigned_at)
                                        VALUES (:tid, :uid, NOW())");
                $stmt->execute([':tid' => $template_id, ':uid' => $user_id]);
            }
        }
    }
    header("Location: assign_kpi&template_id=$template_id");
    exit;
}

// Ambil user yang sudah diassign
$stmt = $pdo->prepare("SELECT u.* FROM kpi_assignments a
                        JOIN users u ON a.user_id = u.id
                        WHERE a.template_id = :tid");
$stmt->execute([':tid' => $template_id]);
$assigned_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Assign KPI ke User</title>
  <link rel="stylesheet" href="/kpi-app/src/output.css">
</head>
<body class="bg-gray-100 p-6">
  <div class="max-w-4xl mx-auto bg-white p-6 rounded-2xl shadow">

    <h1 class="text-2xl font-bold mb-4">Assign KPI ke User</h1>

    <!-- Detail Template -->
    <div class="mb-6 p-4 border rounded bg-gray-50">
      <p><strong>Nama Template:</strong> <?= htmlspecialchars($template['nama']); ?></p>
      <p><strong>Departemen:</strong> <?= htmlspecialchars($template['departemen']); ?></p>
      <p><strong>Periode:</strong> <?= $template['periode']; ?></p>
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
      <a href="../manager/kpi_templates" class="bg-gray-600 text-white px-4 py-2 rounded">Kembali</a>
    </div>
  </div>
</body>
</html>
