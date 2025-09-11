<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/auth.php';

$user_id = $_SESSION['user']['id'];

// ambil daftar KPI assignment untuk user ini
$sql = "SELECT 
            a.id AS assignment_id, 
            t.nama, 
            t.periode, 
            t.deskripsi 
        FROM kpi_assignments a
        JOIN kpi_templates t ON a.template_id = t.id
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
            <ul class="space-y-4">
                <?php foreach ($assignments as $row): ?>
                    <li class="border p-4 rounded-lg">
                        <h2 class="text-xl font-semibold"><?= htmlspecialchars($row['nama']) ?></h2>
                        <p class="text-gray-600">Periode: <?= htmlspecialchars($row['periode']) ?></p>
                        <p class="text-gray-500 text-sm"><?= htmlspecialchars($row['deskripsi']) ?></p>
                        
                        <a href="kpi_detail&assignment_id=<?= $row['assignment_id'] ?>" 
                           class="mt-2 inline-block bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                           Lihat Detail
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="text-gray-500">Belum ada KPI yang ditugaskan.</p>
        <?php endif; ?>
        <div class="mt-4">
        <a href="../dashboard" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
            ⬅ Kembali ke Dashboard
        </a>
    </div>
    </div>
</body>
</html>
