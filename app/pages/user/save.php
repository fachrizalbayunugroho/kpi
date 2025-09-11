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
$keterangan= $_POST['keterangan'] ?? [];
$uploadDir = __DIR__ . '/../user/upload/';

foreach ($realisasi as $item_id => $nilai) {
    if (trim($nilai) === '') {
        echo "<script>alert('Semua nilai realisasi harus diisi.'); history.back();</script>";
        exit;
    }
    if (!isset($keterangan[$item_id]) || trim($keterangan[$item_id]) === '') {
        echo "<script>alert('Semua keterangan harus diisi.'); history.back();</script>";
        exit;
    }
}

foreach ($realisasi as $item_id => $nilai) {
    $evidencePath = null;
    $ket = $keterangan[$item_id] ?? null;

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

    // simpan ke database
    $sql = "INSERT INTO kpi_realisasi (assignment_id, item_id, realisasi, keterangan, evidence) 
            VALUES (:aid, :iid, :realisasi, :ket, :evidence)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':aid' => $assignment_id,
        ':iid' => $item_id,
        ':realisasi' => $nilai,
        ':ket' => $ket,
        ':evidence' => $evidencePath
    ]);
}

// kembali ke halaman detail
echo "<script>alert('Data berhasil diinput!'); window.location.href='kpi_detail&assignment_id={$assignment_id}';</script>";
exit;
