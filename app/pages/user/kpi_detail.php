<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/auth.php';

// ambil assignment_id dari URL
if (!isset($_GET['assignment_id'])) {
    die("Assignment tidak ditemukan.");
}
$assignment_id = (int) $_GET['assignment_id'];

// ambil detail assignment & template
$sql = "SELECT a.id AS assignment_id, a.user_id, t.id AS template_id, t.nama, t.periode, t.deskripsi
        FROM kpi_assignments a
        JOIN kpi_templates t ON a.template_id = t.id
        WHERE a.id = :aid";
$stmt = $pdo->prepare($sql);
$stmt->execute([':aid' => $assignment_id]);
$assignment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$assignment) {
    die("Data assignment tidak ditemukan.");
}

// ambil item KPI dari template
$sqlItems = "SELECT * FROM kpi_items WHERE template_id = :tid";
$stmtItems = $pdo->prepare($sqlItems);
$stmtItems->execute([':tid' => $assignment['template_id']]);
$items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail KPI</title>
    <link rel="stylesheet" href="/kpi-app/src/output.css">
</head>
<body class="bg-gray-100 p-6">
<div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow">
    <h1 class="text-2xl font-bold mb-2"><?= htmlspecialchars($assignment['nama']) ?></h1>
    <p class="text-gray-600">Periode: <?= htmlspecialchars($assignment['periode']) ?></p>
    <p class="text-gray-500 mb-4"><?= htmlspecialchars($assignment['deskripsi']) ?></p>
    <p class="text-l mb-6">(CAN ONLY BE FILLED ONCE)</p>
    <form action="save" method="post" enctype="multipart/form-data" class="space-y-4">
        <input type="hidden" name="assignment_id" value="<?= $assignment['assignment_id'] ?>">

        <div class="overflow-x-auto">
        <table class="w-full border border-gray-300 text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-2 py-1">Indikator</th>
                    <th class="border px-2 py-1">Target</th>
                    <th class="border px-2 py-1">Bobot</th>
                    <th class="border px-2 py-1">Realisasi</th>
                    <th class="border px-2 py-1">Keterangan</th>
                    <th class="border px-2 py-1">Evidence</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                <tr>
                    <td class="border px-2 py-1"><?= htmlspecialchars($item['indikator']) ?></td>
                    <td class="border px-2 py-1 text-center"><?= htmlspecialchars($item['target']) ?></td>
                    <td class="border px-2 py-1 text-center"><?= htmlspecialchars($item['bobot']) ?>%</td>
                    <td class="border px-2 py-1">
                            <input type="number" step="0.01" name="realisasi[<?= $item['id'] ?>]" 
                                   class="border rounded p-1 w-full">
                        </td>
                        <td class="border px-2 py-1">
                            <textarea name="keterangan[<?= $item['id'] ?>]" 
                                      class="border rounded p-1 w-full" rows="2"></textarea>
                        </td>
                        <td class="border px-2 py-1">
                            <input type="file" name="evidence[<?= $item['id'] ?>]" class="text-sm">
                        </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

        <button type="submit" 
                class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg">
            Simpan Realisasi
        </button>
    </form>
    <div class="mt-4">
    <a href="../user/my_kpi" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">Kembali</a>
	</div>
</div>
<script>
document.getElementById('kpiForm').addEventListener('submit', function(e) {
    let valid = true;
    let messages = [];

    // cek semua input realisasi
    document.querySelectorAll('input[name^="realisasi"]').forEach(el => {
        if (el.value.trim() === "") {
            valid = false;
            messages.push("Semua nilai realisasi harus diisi.");
        }
    });

    // cek semua textarea keterangan
    document.querySelectorAll('textarea[name^="keterangan"]').forEach(el => {
        if (el.value.trim() === "") {
            valid = false;
            messages.push("Semua keterangan harus diisi.");
        }
    });

    if (!valid) {
        e.preventDefault();
        alert(messages.join("\n"));
    }
});
</script>

</body>
</html>