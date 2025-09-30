<?php
require __DIR__ . '/../../app/config/database.php';

$user_id = $_SESSION['user']['id'] ?? null;
$message = "";

if (!$user_id) {
    die("Akses tidak valid.");
}

// Jika form disubmit
if (isset($_POST['change'])) {
    $old_pass = $_POST['old_password'] ?? '';
    $new_pass = $_POST['new_password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    // Ambil password lama dari DB
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($old_pass, $user['password'])) {
        $message = "❌ Password lama salah!"; }
    elseif ($new_pass !== $confirm) {
        $message = "❌ Password baru tidak sama!";
    } else {
        $hash = password_hash($new_pass, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $success = $stmt->execute([$hash, $user_id]);

        if ($success) {
            $message = "✅ Password berhasil diubah!";
        } else {
            $message = "❌ Gagal mengubah password.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ganti Password</title>
  <link rel="stylesheet" href="/kpi-app/src/output.css">
</head>
<body class="bg-gray-100 p-6">
<div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow">
  <h3 class="text-xl font-semibold mb-4 text-gray-800">Ubah Password</h3>

  <?php if ($message): ?>
    <div class="mb-4 px-4 py-2 rounded bg-blue-100 text-blue-700 text-sm">
      <?= $message; ?>
    </div>
  <?php endif; ?>

  <form method="POST" class="space-y-4">
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Password Lama</label>
      <input type="password" name="old_password"
             class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 p-2"
             required>
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
      <input type="password" name="new_password"
             class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 p-2"
             required>
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
      <input type="password" name="confirm_password"
             class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 p-2"
             required>
    </div>

    <button type="submit" name="change"
            class="w-full bg-blue-600 text-white font-medium py-2 px-4 rounded-md shadow hover:bg-blue-700 transition">
      Simpan
    </button>
  </form>
</div>
<div class="mt-4">
    <a href="/kpi-app/public/dashboard" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
            ⬅ Kembali ke Dashboard
    </a>
</div>
</body>
</html>