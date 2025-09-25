<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/auth.php';

//if (!isset($_GET['user_id'])) {
    //die("User tidak ditemukan.");
//}
$user_id = $param;

// ambil info user
$sqlUser = "SELECT u.id, u.nama, d.name as dept
            FROM users u
            JOIN departments d ON u.departemen_id = d.id
            WHERE u.id = :uid";
$stmtUser = $pdo->prepare($sqlUser);
$stmtUser->execute([':uid' => $user_id]);
$user = $stmtUser->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("Data user tidak ditemukan.");
}

// ambil data KPI detail (assignment + item + realisasi)
if ($action === 'detail' && $param) {
$sql = "SELECT i.id as item_id, i.indikator, i.target, i.bobot, i.satuan,
               r.realisasi, r.keterangan, r.evidence
        FROM kpi_assignment a
        JOIN kpi_item i ON i.template_id = a.template_id
        LEFT JOIN kpi_user r ON r.assignment_id = a.id AND r.item_id = i.id
        WHERE a.user_id = :uid";
$stmt = $pdo->prepare($sql);
$stmt->execute([':uid' => $user_id]);
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
  <h1 class="text-2xl font-bold mb-2">Detail KPI: <?= htmlspecialchars($user['nama']) ?></h1>
  <p class="text-gray-600 mb-4">Departemen: <?= htmlspecialchars($user['dept']) ?></p>

  <div class="overflow-x-auto">
    <table class="min-w-full border text-sm">
      <thead class="bg-gray-100">
        <tr>
          <th class="border px-2 py-1">Indikator</th>
          <th class="border px-2 py-1">Target</th>
          <th class="border px-2 py-1">Satuan</th>
          <th class="border px-2 py-1">Bobot</th>
          <th class="border px-2 py-1">Realisasi</th>
          <th class="border px-2 py-1">Keterangan</th>
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
            $skor = 0;
            if ($row['target'] > 0 && $row['realisasi'] !== null) {
                $skor = ($row['realisasi'] / $row['target']) * $row['bobot'];
            }
            $totalSkor += $skor;
        ?>
        <tr>
          <td class="border px-2 py-1"><?= htmlspecialchars($row['indikator']) ?></td>
          <td class="border px-2 py-1 text-center"><?= htmlspecialchars($row['target']) ?></td>
          <td class="border px-2 py-1 text-center"><?= htmlspecialchars($row['satuan']) ?></td>
          <td class="border px-2 py-1 text-center"><?= htmlspecialchars($row['bobot']) ?>%</td>
          <td class="border px-2 py-1 text-center"><?= htmlspecialchars($row['realisasi'] ?? '-') ?></td>
          <td class="border px-2 py-1"><?= htmlspecialchars($row['keterangan'] ?? '-') ?></td>
          <td class="border px-2 py-1 text-center">
            <?php if ($row['evidence']): ?>
              <a href="<?= htmlspecialchars($row['evidence']) ?>" target="_blank" 
                 class="text-blue-600 underline">Lihat</a>
            <?php else: ?>
              -
            <?php endif; ?>
          </td>
          <td class="border px-2 py-1 text-center"><?= round($skor, 2) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
      <tfoot class="bg-gray-50 font-bold">
        <tr>
          <td class="border px-2 py-1 text-right" colspan="3">Total</td>
          <td class="border px-2 py-1 text-center"><?= $totalBobot ?>%</td>
          <td class="border px-2 py-1 text-center" colspan="3">Skor Akhir</td>
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
