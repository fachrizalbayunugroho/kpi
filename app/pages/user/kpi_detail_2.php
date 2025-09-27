<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/auth.php';

$user_id = $_SESSION['user']['id'];
$assignment_id = $param ?? null; 

if (!$assignment_id) {
    die("Assignment tidak valid.");
}

// cek apakah assignment memang milik user ini
$stmt = $pdo->prepare("SELECT a.id, t.id AS template_id, t.nama_template, t.deskripsi 
                       FROM kpi_user a
                       JOIN kpi_template t ON a.template_id = t.id
                       WHERE a.id = :aid AND a.user_id = :uid");
$stmt->execute([':aid' => $assignment_id, ':uid' => $user_id]);
$assignment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$assignment) {
    die("Data tidak ditemukan atau Anda tidak berhak mengaksesnya.");
}

// jika ada submit form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['realisasi'] as $itemId => $val) {
        $val = $val === '' ? null : (float)$val;

        // handle file upload (optional per item)
        $filename = null;
        if (isset($_FILES['evidence']['name'][$itemId]) && $_FILES['evidence']['name'][$itemId] !== '') {
            $uploadDir = __DIR__ . '/../../uploads/evidence/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $origName = basename($_FILES['evidence']['name'][$itemId]);
            $ext = pathinfo($origName, PATHINFO_EXTENSION);
            $newName = 'evidence_' . $user_id . '_' . $itemId . '_' . time() . '.' . $ext;
            $targetPath = $uploadDir . $newName;

            if (move_uploaded_file($_FILES['evidence']['tmp_name'][$itemId], $targetPath)) {
                $filename = $newName;
            }
        }

        // cek apakah sudah ada isian realisasi untuk item ini
        $check = $pdo->prepare("SELECT id FROM kpi_realisasi WHERE assignment_id = :aid AND item_id = :iid");
        $check->execute([':aid' => $assignment_id, ':iid' => $itemId]);
        $exist = $check->fetch(PDO::FETCH_ASSOC);

        if ($exist) {
            // update
            $upd = $pdo->prepare("UPDATE kpi_realisasi 
                                  SET realisasi = :val, evidence = COALESCE(:evi, evidence) 
                                  WHERE id = :id");
            $upd->execute([':val' => $val, ':evi' => $filename, ':id' => $exist['id']]);
        } else {
            // insert baru
            $ins = $pdo->prepare("INSERT INTO kpi_realisasi (assignment_id, item_id, realisasi) 
                                  VALUES (:aid, :iid, :val)");
            $ins->execute([':aid' => $assignment_id, ':iid' => $itemId, ':val' => $val]);
        }
    }

    echo "<script>alert('Data berhasil disimpan'); window.location.href='/kpi-app/public/my_kpi';</script>";
    exit;
}

// ambil daftar indikator untuk ditampilkan di form
$sqlItems = "SELECT 
                i.id AS item_id, 
                i.indikator, 
                i.target, 
                i.bobot, 
                i.satuan,
                r.realisasi
            FROM kpi_item i
            LEFT JOIN kpi_realisasi r 
                   ON r.item_id = i.id 
                  AND r.assignment_id = :aid
            WHERE i.template_id = :tid";
$stmtItems = $pdo->prepare($sqlItems);
$stmtItems->execute([':aid' => $assignment_id, ':tid' => $assignment['template_id']]);
$items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Edit KPI Saya</title>
    <link rel="stylesheet" href="/kpi-app/src/output.css">
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow">
        <h1 class="text-2xl font-bold mb-4">
            Form KPI: <?= htmlspecialchars($assignment['nama_template']) ?>
        </h1>

        <form method="POST" enctype="multipart/form-data" class="space-y-4">
            <table class="w-full border text-sm">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="border p-2">Indikator</th>
                        <th class="border p-2">Target</th>
                        <th class="border p-2">Satuan</th>
                        <th class="border p-2">Bobot</th>
                        <th class="border p-2">Realisasi</th>
                        <th class="border p-2">Evidence</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td class="border p-2"><?= htmlspecialchars($item['indikator']) ?></td>
                            <td class="border p-2"><?= $item['target'] ?></td>
                            <td class="border p-2"><?= htmlspecialchars($item['satuan']) ?></td>
                            <td class="border p-2"><?= $item['bobot'] ?></td>
                            <td class="border p-2">
                                <input type="number" step="0.01" name="realisasi[<?= $item['item_id'] ?>]"
                                       value="<?= $item['realisasi'] ?>"
                                       class="w-full p-2 border rounded">
                            </td>
                            <td class="border p-2">
                                <?php if (!empty($item['evidence'])): ?>
                                    <a href="/kpi-app/uploads/evidence/<?= htmlspecialchars($item['evidence']) ?>" target="_blank"
                                       class="text-blue-500 underline">Lihat</a><br>
                                <?php endif; ?>
                                <input type="file" name="evidence[<?= $item['item_id'] ?>]" class="text-sm">
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

    <button type="submit" 
        class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg">Simpan Realisasi
    </button>
    </form>
    <div class="mt-4">
    <a href="/kpi-app/public/my_kpi" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">Kembali</a>
	</div>
    </div>
</body>
</html>
