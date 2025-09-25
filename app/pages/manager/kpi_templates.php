<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/auth.php';

checkRole(['manager']);

if (isset($_POST['add_template'])) {
    $nama = $_POST['nama'];
    $deskripsi = $_POST['deskripsi'];
    $departemen_id = $_POST['departemen_id'];
    $created_by = $_SESSION['user']['id'];

    $stmt = $pdo->prepare("INSERT INTO kpi_template (departemen_id, nama_template, deskripsi, dibuat_oleh)
                            VALUES (:departemen_id, :nama, :deskripsi, :created_by)");
    $stmt->execute([
        ':departemen_id' => $departemen_id,
        ':nama' => $nama,
        ':deskripsi' => $deskripsi,
        ':created_by' => $created_by
    ]);
    header("Location: kpi_templates");
    exit;
}

if ($action === 'delete' && $param) {
    $stmt = $pdo->prepare("DELETE FROM kpi_template WHERE id = :id");
    $stmt->execute([':id' => $param]);

  echo "<script>alert('Template berhasil dihapus'); window.location.href='/kpi-app/public/kpi_templates';</script>";
  exit;
}

$departemen = $pdo->query("SELECT * FROM departments");

$templates = $pdo->query("SELECT t.*, d.name AS departemen 
                           FROM kpi_template t 
                           LEFT JOIN departments d ON t.departemen_id = d.id
                           ORDER BY t.created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Template KPI</title>
  <link rel="stylesheet" href="/kpi-app/src/output.css">
</head>
<body class="bg-gray-100 p-6">
  <div class="max-w-6xl mx-auto bg-white p-6 rounded-2xl shadow">

    <h1 class="text-2xl font-bold mb-4">Template KPI</h1>

    <!-- Form Tambah Template -->
    <form method="POST" class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
      <input type="text" name="nama" placeholder="Nama Template" required
             class="p-2 border rounded w-full">
      <textarea name="deskripsi" placeholder="Deskripsi"
                class="p-2 border rounded w-full col-span-1 md:col-span-2"></textarea>

      <select name="departemen_id" class="p-2 border rounded w-full" required>
        <option value="">-- Pilih Departemen --</option>
        <?php while($d = $departemen->fetch(PDO::FETCH_ASSOC)): ?>
          <option value="<?= $d['id']; ?>"><?= htmlspecialchars($d['name']); ?></option>
        <?php endwhile; ?>
      </select>

      <button type="submit" name="add_template"
              class="bg-green-600 text-white p-2 rounded col-span-1 md:col-span-2">
        Tambah Template
      </button>
    </form>

    <!-- Tabel Template KPI -->
    <div class="overflow-x-auto">
    <table class="w-full border text-sm">
      <thead class="bg-gray-200">
        <tr>
          <th class="border p-2">Nama</th>
          <th class="border p-2">Departemen</th>
          
          <th class="border p-2">Deskripsi</th>
          <th class="border p-2">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php while($t = $templates->fetch(PDO::FETCH_ASSOC)): ?>
        <tr>
          <td class="border p-2"><?= htmlspecialchars($t['nama_template']); ?></td>
          <td class="border p-2"><?= $t['departemen']; ?></td>
          
          <td class="border p-2"><?= htmlspecialchars($t['deskripsi']); ?></td>
          <td class="border p-2">
  			<div class="flex flex-col sm:flex-row gap-2">
    		<a href="/kpi-app/public/kpi_templates/detail/<?= $t['id']; ?>"
       class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded text-sm text-center">
       Kelola Item
   			</a>

    		<a href="/kpi-app/public/kpi_templates/delete/<?= $t['id'] ?>"
       onclick="return confirm('Hapus template ini?')"
       class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded text-sm text-center">
       Hapus
    		</a>

    		<a href="/kpi-app/public/kpi_templates/assign/<?= $t['id']; ?>"
       class="bg-orange-500 hover:bg-orange-600 text-white px-3 py-2 rounded text-sm text-center">
       Assign
    		</a>
  			</div>
		</td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
	</div>

    <div class="mt-4">
        <a href="/kpi-app/public/dashboard" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
            ⬅ Kembali ke Dashboard
        </a>
    </div>

  </div>
</body>
</html>