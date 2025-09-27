<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/auth.php';

$user_id = $_SESSION['user']['id'];

// ambil daftar KPI assignment untuk user ini
$sql = "SELECT 
            a.id AS assignment_id, 
            t.nama_template, 
            t.deskripsi 
        FROM kpi_user a
        JOIN kpi_template t ON a.template_id = t.id
        WHERE a.user_id = :uid";

$stmt = $pdo->prepare($sql);
$stmt->execute([':uid' => $user_id]);
$assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KPI Saya</title>
    <link rel="stylesheet" href="/kpi-app/src/output.css">
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow">
        <h1 class="text-2xl font-bold mb-4">Daftar KPI Saya</h1>

        <?php if (count($assignments) > 0): ?>
            <ul class="space-y-6">
                <?php foreach ($assignments as $row): ?>
                    <li class="border p-4 rounded-lg">
                        <h2 class="text-xl font-semibold mb-1">
                            <?= htmlspecialchars($row['nama_template']) ?>
                        </h2>
                        <p class="text-gray-500 text-sm mb-4">
                            <?= htmlspecialchars($row['deskripsi']) ?>
                        </p>

                        <!-- tabel indikator & realisasi -->
                        <table class="w-full border text-sm mb-4">
                            <thead class="bg-gray-200">
                                <tr>
                                    <th class="border p-2">Indikator</th>
                                    <th class="border p-2">Target</th>
                                    <th class="border p-2">Satuan</th>
                                    <th class="border p-2">Bobot</th>
                                    <th class="border p-2">Realisasi</th>
                                    <th class="border p-2">Evidence</th>
                                    <!--<th class="border p-2">Skor</th>-->
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sqlItems = "SELECT 
                                                i.indikator, 
                                                i.target,
                                                i.satuan, 
                                                i.bobot, 
                                                r.realisasi,
                                                r.evidence,
                                                CASE 
                                                    WHEN i.tipe = 'normal' THEN (r.realisasi / i.target) * i.bobot
                                                    WHEN i.tipe = 'balik' THEN (i.target / NULLIF(r.realisasi,0)) * i.bobot
                                                    ELSE 0
                                                END AS skor
                                            FROM kpi_item i
                                            LEFT JOIN kpi_realisasi r 
                                                ON r.item_id = i.id 
                                               AND r.assignment_id = :aid
                                            WHERE i.template_id = (
                                                SELECT template_id 
                                                FROM kpi_user 
                                                WHERE id = :aid
                                            )";
                                $stmtItems = $pdo->prepare($sqlItems);
                                $stmtItems->execute([':aid' => $row['assignment_id']]);

                                $totalSkor = 0;
                                while ($item = $stmtItems->fetch(PDO::FETCH_ASSOC)):
                                    $totalSkor += (float)$item['skor'];
                                ?>
                                    <tr>
                                        <td class="border p-2"><?= htmlspecialchars($item['indikator']) ?></td>
                                        <td class="border p-2"><?= $item['target'] ?></td>
                                        <td class="border p-2"><?= $item['satuan'] ?></td>
                                        <td class="border p-2"><?= $item['bobot'] ?>%</td>
                                        <td class="border p-2"><?= $item['realisasi'] ?? '-' ?></td>
                                        <td class="border p-2"><?= $item['evidence'] ?? '-' ?></td>
                                        <!--<td class="border p-2"><?= round($item['skor'], 2) ?></td>-->
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                            <!--<tfoot>
                                <tr class="font-bold bg-gray-100">
                                    <td colspan="4" class="border p-2 text-right">Total Skor</td>
                                    <td class="border p-2"><?= round($totalSkor, 2) ?></td>
                                </tr>
                            </tfoot>-->
                        </table>

                        <a href="/kpi-app/public/my_kpi/detail/<?= $row['assignment_id'] ?>"
                           class="mt-2 inline-block bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                           Form Isi/Edit
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="text-gray-500">Belum ada KPI yang ditugaskan.</p>
        <?php endif; ?>

        <div class="mt-4">
            <a href="/kpi-app/public/dashboard" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                ⬅ Kembali ke Dashboard
            </a>
        </div>
    </div>
</body>
</html>