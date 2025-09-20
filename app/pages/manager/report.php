<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/auth.php';

$managerDept = $_SESSION['user']['departemen_id'];

// ambil laporan per user di departemen manager
$sql = "SELECT u.id as user_id, u.nama as user_nama, d.name as dept_nama,
               SUM(i.bobot) as total_bobot,
               SUM( (r.realisasi / NULLIF(i.target,0)) * i.bobot ) as skor
        FROM users u
        JOIN departments d ON u.departemen_id = d.id
        JOIN kpi_assignments a ON a.user_id = u.id
        JOIN kpi_items i ON i.template_id = a.template_id
        LEFT JOIN kpi_realisasi r ON r.assignment_id = a.id AND r.item_id = i.id
        WHERE d.id = :dept
        GROUP BY u.id";
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
  <h1 class="text-2xl font-bold mb-4">Laporan KPI Departemen <?= $laporan[0]['dept_nama'] ?? '' ?></h1>

  <div class="overflow-x-auto">
    <table class="min-w-full border text-sm">
      <thead class="bg-gray-100">
        <tr>
          <th class="border px-2 py-1">Nama User</th>
          <th class="border px-2 py-1">Total Bobot</th>
          <th class="border px-2 py-1">Skor Akhir</th>
          <th class="border px-2 py-1">Progress</th>
          <th class="border px-2 py-1">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($laporan as $row): 
          $total = $row['total_bobot'] ?: 0;
          $skor = $row['skor'] ?: 0;
          $persen = $total > 0 ? round(($skor / $total) * 100, 2) : 0;
        ?>
        <tr>
          <td class="border px-2 py-1"><?= htmlspecialchars($row['user_nama']) ?></td>
          <td class="border px-2 py-1 text-center"><?= $total ?>%</td>
          <td class="border px-2 py-1 text-center"><?= round($skor,2) ?></td>
          <td class="border px-2 py-1 w-48">
            <div class="w-full bg-gray-200 rounded-full h-4">
              <div class="bg-blue-500 h-4 rounded-full" style="width: <?= $persen ?>%;"></div>
            </div>
            <p class="text-xs text-gray-600 mt-1"><?= $persen ?>%</p>
          </td>
          <td class="border px-2 py-1 text-center">
          	<a href="/kpi-app/public/report/detail/<?= $row['user_id'] ?>"
               class="bg-blue-500 text-white px-2 py-1 rounded">Detail</a>
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
