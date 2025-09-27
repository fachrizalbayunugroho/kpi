<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/auth.php';

// pastikan ada data dari form
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "<script>alert('Akses tidak valid.'); history.back();</script>";
    exit;
}

$assignment_id = (int) $_POST['assignment_id'];
$realisasi = $_POST['realisasi'] ?? [];
$uploadDir = __DIR__ . '/../user/upload/';
$user_id = (int) $_POST['user_id'];
$template_id = (int) $_POST['template_id'];

// validasi input kosong
foreach ($realisasi as $item_id => $nilai) {
    if (trim($nilai) === '') {
        echo "<script>alert('Semua nilai realisasi harus diisi.'); history.back();</script>";
        exit;
    }
}

foreach ($realisasi as $item_id => $nilai) {
    $evidencePath = null;

    // cek jika ada file upload untuk item ini
    if (isset($_FILES['evidence']['name'][$item_id]) && $_FILES['evidence']['error'][$item_id] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES['evidence']['tmp_name'][$item_id];
        $ext = pathinfo($_FILES['evidence']['name'][$item_id], PATHINFO_EXTENSION);
        $fileName = uniqid("evidence_{$item_id}_") . '.' . $ext;
        $destination = $uploadDir . $fileName;

        if (move_uploaded_file($tmpName, $destination)) {
            $evidencePath = '../user/upload/' . $fileName;
        }
    }

    // cek apakah sudah ada data sebelumnya
    $check = $pdo->prepare("SELECT id, evidence FROM kpi_realisasi 
                            WHERE assignment_id = :aid AND item_id = :iid");
    $check->execute([':aid' => $assignment_id, ':iid' => $item_id]);
    $exist = $check->fetch(PDO::FETCH_ASSOC);

    if ($exist) {
        // kalau tidak upload baru → tetap pakai evidence lama
        if ($evidencePath === null) {
            $evidencePath = $exist['evidence'];
        }

        $upd = $pdo->prepare("UPDATE kpi_realisasi 
                              SET realisasi = :realisasi, evidence = :evidence 
                              WHERE id = :id");
        $upd->execute([
            ':realisasi' => $nilai,
            ':evidence' => $evidencePath,
            ':id' => $exist['id']
        ]);
    } else {
        $ins = $pdo->prepare("INSERT INTO kpi_realisasi 
                              (assignment_id, user_id, item_id, template_id, realisasi, evidence) 
                              VALUES (:aid, :uid, :iid, :tid, :realisasi, :evidence)");
        $ins->execute([
            ':aid' => $assignment_id,
            ':uid' => $user_id,
            ':iid' => $item_id,
            ':tid' => $template_id,
            ':realisasi' => $nilai,
            ':evidence' => $evidencePath
        ]);
    }
}

// kembali ke halaman detail
echo "<script>alert('Data berhasil disimpan!'); window.location.href='kpi_detail&assignment_id={$assignment_id}';</script>";
exit;
