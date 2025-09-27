<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/auth.php';

$managerDept = $_SESSION['user']['departemen_id'];

// ambil laporan per user di departemen manager
$sql = "SELECT u.id as user_id, u.nama as user_name, r.assignment_id as assignment_id,
               t.id as template_id, t.nama_template, d.name AS departemen, ROUND(SUM(CASE 
            WHEN i.tipe = 'normal' AND r.realisasi IS NOT NULL AND i.target > 0
                THEN (r.realisasi / i.target) * i.bobot
            WHEN i.tipe = 'inverse' AND r.realisasi IS NOT NULL AND r.realisasi > 0
                THEN (i.target / r.realisasi) * i.bobot
            ELSE 0
        END), 2) AS skor_akhir
        FROM kpi_user ku
        JOIN users u ON ku.user_id = u.id
        JOIN kpi_template t ON ku.template_id = t.id
        JOIN kpi_item i ON i.template_id = t.id
        LEFT JOIN kpi_realisasi r 
            ON r.item_id = i.id AND r.user_id = u.id AND r.template_id = t.id
        JOIN departments d ON u.departemen_id = d.id
    	WHERE d.id = :dept
        GROUP BY u.id, t.id
        ORDER BY u.nama, t.nama_template";
$stmt = $pdo->prepare($sql);
$stmt->execute([':dept' => $managerDept]);
$laporan = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Laporan KPI Departemen</title>
  <link rel="stylesheet" href="/kpi-app/src/output.css">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body class="bg-gray-100 p-6">
<div class="max-w-6xl mx-auto bg-white p-6 rounded-lg shadow">
  <h1 class="text-2xl font-bold mb-4">Laporan KPI</h1>

  <div class="overflow-x-auto">
    <table class="w-full border text-sm">
      <thead class="bg-gray-200">
        <tr>
          <th class="border p-2">Nama User</th>
          <th class="border p-2">Departemen</th>
          <th class="border p-2">Template</th>
          <th class="border p-2">Skor Akhir</th>
          <th class="border p-2">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($laporan as $row): ?>
        <tr>
          <td class="border p-2"><?= htmlspecialchars($row['user_name']); ?></td>
          <td class="border p-2"><?= htmlspecialchars($row['departemen']); ?></td>
          <td class="border p-2"><?= htmlspecialchars($row['nama_template']); ?></td>
          <td class="border p-2 font-bold"><?= round($row['skor_akhir'],2); ?></td>
          <td class="border p-2">
            <a href="/kpi-app/public/report/detail/<?= $row['assignment_id'] ?>" 
               class="bg-blue-500 text-white px-3 py-1 rounded">Detail</a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <div class="mt-4">
    <a href="../public/dashboard" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
            ⬅ Kembali ke Dashboard
    </a>
  </div>
</div>
</body>
</html>
