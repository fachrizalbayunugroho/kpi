<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/auth.php';

$assignmentId = $param;

// ambil info assignment termasuk template_id dan user_id
$sql = "SELECT ku.id AS assignment_id, ku.user_id, u.nama AS user_nama,
               t.id AS template_id, t.nama_template AS nama_template, d.name AS departemen
        FROM kpi_user ku
        JOIN users u ON ku.user_id = u.id
        JOIN kpi_template t ON ku.template_id = t.id
        LEFT JOIN departments d ON t.departemen_id = d.id
        WHERE ku.id = :aid";

$stmt = $pdo->prepare($sql);
$stmt->execute([':aid' => $assignmentId]);
$assignment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$assignment) {
    die("Assignment tidak ditemukan!");
}

$template_id = $assignment['template_id'];
$user_id     = $assignment['user_id'];

// ambil data KPI detail (assignment + item + realisasi)
if ($action === 'detail' && $param) {
$sql = "SELECT i.indikator, i.target, i.bobot, i.satuan, i.tipe,
               r.realisasi, r.evidence,
               CASE 
                 WHEN r.realisasi IS NULL OR r.realisasi = 0 THEN 0
                 WHEN i.tipe = 'inverse' 
                      THEN (i.target / r.realisasi) * i.bobot
                 ELSE (r.realisasi / i.target) * i.bobot
               END AS skor_akhir
        FROM kpi_item i
        LEFT JOIN kpi_realisasi r 
               ON r.item_id = i.id AND r.assignment_id = :aid
        WHERE i.template_id = :tid";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':aid' => $assignmentId,
    ':tid' => $template_id
]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

?>
<!DOCTYPE html>
<html>
<head>
  <title>Laporan Detail KPI</title>
  <link rel="stylesheet" href="/kpi-app/src/output.css">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body class="bg-gray-100 p-6">
<div class="max-w-6xl mx-auto bg-white p-6 rounded-lg shadow">
  <h1 class="text-2xl font-bold mb-2">Detail KPI: <?= htmlspecialchars($assignment['user_nama']) ?></h1>
  <p class="text-l font-bold mb-2">Departemen: <?= htmlspecialchars($assignment['departemen']) ?></p>
  <p class="text-l font-bold mb-4">Nama template KPI: <?= htmlspecialchars($assignment['nama_template']) ?></p>
  <div class="overflow-x-auto">
    <table class="min-w-full border text-sm">
      <thead class="bg-gray-100">
        <tr>
          <th class="border px-2 py-1">Indikator</th>
          <th class="border px-2 py-1">Target</th>
          <th class="border px-2 py-1">Satuan</th>
          <th class="border px-2 py-1">Bobot</th>
          <th class="border px-2 py-1">Realisasi</th>
          <th class="border px-2 py-1">Evidence</th>
          <th class="border px-2 py-1">Skor</th>
        </tr>
      </thead>
      <tbody>
        <?php 
        $totalBobot = 0;
        $totalSkor = 0;

        foreach ($items as $row): 
            $totalBobot += $row['bobot'];
        ?>
        <tr>
          <td class="border px-2 py-1"><?= htmlspecialchars($row['indikator']) ?></td>
          <td class="border px-2 py-1 text-center"><?= htmlspecialchars($row['target']) ?></td>
          <td class="border px-2 py-1 text-center"><?= htmlspecialchars($row['satuan']) ?></td>
          <td class="border px-2 py-1 text-center"><?= htmlspecialchars($row['bobot']) ?>%</td>
          <td class="border px-2 py-1 text-center"><?= htmlspecialchars($row['realisasi'] ?? '-') ?></td>
          <td class="border px-2 py-1 text-center">
            <?php if ($row['evidence']): ?>
              <a href="<?= htmlspecialchars($row['evidence']) ?>" target="_blank" 
                 class="text-blue-600 underline">Lihat</a>
            <?php else: ?>
              -
            <?php endif; ?>
          </td>
          <td class="border px-2 py-1 text-center"><?= round($row['skor_akhir'], 2) ?></td>
        </tr>
        <?php $totalSkor += $row['skor_akhir']; ?>
        <?php endforeach; ?>
      </tbody>
      <tfoot class="bg-gray-50 font-bold">
        <tr>
          <td class="border px-2 py-1 text-center" colspan="3">Total</td>
          <td class="border px-2 py-1 text-center"><?= $totalBobot ?>%</td>
          <td class="border px-2 py-1 text-center" colspan="2">Skor Akhir</td>
          <td class="border px-2 py-1 text-center"><?= round($totalSkor, 2) ?></td>
        </tr>
      </tfoot>
    </table>
  </div>

  <div class="mt-4">
    <?php 
      $persen = $totalBobot > 0 ? round(($totalSkor / $totalBobot) * 100, 2) : 0;
    ?>
    <p class="font-semibold">Progress: <?= $persen ?>%</p>
    <div class="w-1/2 bg-gray-200 rounded-full h-5 mt-1">
      <div class="bg-green-500 h-5 rounded-full" style="width: <?= $persen ?>%;"></div>
    </div>
  </div>

  <div class="mt-6">
    <a href="/kpi-app/public/report" class="bg-gray-500 text-white px-4 py-2 rounded">Kembali</a>
    <a href="/kpi-app/public/report/export/<?= $user_id ?>" 
     class="bg-green-600 text-white px-4 py-2 rounded">Export Excel</a>
  </div>
</div>
</body>
</html>
