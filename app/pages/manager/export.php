<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/auth.php';

if (!isset($_GET['user_id'])) {
    die("User tidak ditemukan.");
}
$user_id = (int) $_GET['user_id'];

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

// ambil detail KPI
$sql = "SELECT i.indikator, i.target, i.bobot,
               r.realisasi, r.keterangan, r.evidence
        FROM kpi_assignments a
        JOIN kpi_items i ON i.template_id = a.template_id
        LEFT JOIN kpi_realisasi r ON r.assignment_id = a.id AND r.item_id = i.id
        WHERE a.user_id = :uid";
$stmt = $pdo->prepare($sql);
$stmt->execute([':uid' => $user_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// set header untuk excel (format CSV biar universal)
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=laporan_kpi_{$user['nama']}.xls");
header("Pragma: no-cache");
header("Expires: 0");

// judul
echo "Laporan Detail KPI\n";
echo "Nama: {$user['nama']}\n";
echo "Departemen: {$user['dept']}\n\n";

// header tabel
echo "Indikator\tTarget\tBobot\tRealisasi\tKeterangan\tEvidence\tSkor\n";

$totalBobot = 0;
$totalSkor = 0;

foreach ($items as $row) {
    $totalBobot += $row['bobot'];
    $skor = 0;
    if ($row['target'] > 0 && $row['realisasi'] !== null) {
        $skor = ($row['realisasi'] / $row['target']) * $row['bobot'];
    }
    $totalSkor += $skor;

    echo "{$row['indikator']}\t{$row['target']}\t{$row['bobot']}\t" .
         ($row['realisasi'] ?? '-') . "\t" .
         ($row['keterangan'] ?? '-') . "\t" .
         ($row['evidence'] ?? '-') . "\t" .
         round($skor, 2) . "\n";
}

// total
echo "\nTotal Bobot\t{$totalBobot}%\n";
echo "Total Skor\t" . round($totalSkor, 2) . "\n";
$persen = $totalBobot > 0 ? round(($totalSkor / $totalBobot) * 100, 2) : 0;
echo "Progress\t{$persen}%\n";
exit;
