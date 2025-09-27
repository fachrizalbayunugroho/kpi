<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/auth.php';

// GET data template
if ($action === 'detail' && $param) {
$stmt = $pdo->prepare("SELECT t.*, d.name AS departemen 
                        FROM kpi_template t
                        LEFT JOIN departments d ON t.departemen_id = d.id
                        WHERE t.id = :id");
$stmt->execute([':id' => $param]);
$template = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (!$template) {
    die("Template tidak ditemukan!");
}

// CREATE item KPI
if (isset($_POST['add_item'])) {
    $indikator = $_POST['indikator'];
    $bobot = (float)$_POST['bobot'];
    $target = $_POST['target'];
    $satuan = $_POST['satuan'];
    $tipe = $_POST['tipe'];

    // Hitung total bobot yang sudah ada
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(bobot),0) as total 
                           FROM kpi_item 
                           WHERE template_id = :template_id");
    $stmt->execute([':template_id' => $param]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalBobot = (int)$row['total'];

    // Validasi
    if ($totalBobot + $bobot > 100) {
        echo "<script>alert('Total bobot melebihi 100!'); 
              window.history.back();</script>";
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO kpi_item 
        (template_id, indikator, target, satuan, bobot, tipe) 
        VALUES (:template_id, :indikator, :target, :satuan, :bobot, :tipe)");

    $stmt->execute([
        ':template_id'	=> $param,
        ':indikator'	=> $indikator,
        ':target'		=> $target,
        ':satuan'		=> $satuan,
        ':bobot'		=> $bobot,
        ':tipe'			=> $tipe
    ]);


	echo "<script>alert('Item berhasil ditambahkan'); window.location.href='/kpi-app/public/kpi_templates/detail/$param';</script>";
    exit;

if ($bobot <= 0 || $target <= 0 || trim($indikator) === '') {
    echo "<script>alert('Input tidak valid!'); window.history.back();</script>";
    exit;
}
}

// cek apakah ada request delete
$subAction = $segments[3] ?? null;   // "delete"
$subParam  = $segments[4] ?? null;   // id item

// Hitung total bobot semua item dalam template
$stmt = $pdo->prepare("SELECT COALESCE(SUM(bobot),0) as total 
                       FROM kpi_item 
                       WHERE template_id = :template_id");
$stmt->execute([':template_id' => $param]);
$totalBobot = (float)$stmt->fetch(PDO::FETCH_ASSOC)['total'];


if ($subAction === 'delete' && $subParam) {
    $stmt = $pdo->prepare("DELETE FROM kpi_item WHERE id = :id");
    $stmt->execute([':id' => $subParam]);

    echo "<script>alert('Item berhasil dihapus'); window.location.href='/kpi-app/public/kpi_templates/detail/$param';</script>";
    exit;
}

// GET items dalam template
$stmt = $pdo->prepare("SELECT * FROM kpi_item WHERE template_id = :template_id ORDER BY id DESC");
$stmt->execute([':template_id' => $param]);
$items = $stmt;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Item Template KPI</title>
  <link rel="stylesheet" href="/kpi-app/src/output.css">
</head>
<body class="bg-gray-100 p-6">
  <div class="max-w-5xl mx-auto bg-white p-6 rounded-2xl shadow">

    <h1 class="text-2xl font-bold mb-4">Item Template KPI</h1>

    <!-- Detail Template -->
    <div class="mb-6 p-4 border rounded bg-gray-50">
      <p><strong>Nama Template:</strong> <?= htmlspecialchars($template['nama_template']); ?></p>
      <p><strong>Departemen:</strong> <?= $template['departemen']; ?></p>
      <!--<p><strong>Periode:</strong> <?= $template['periode']; ?></p>-->
      <p><strong>Deskripsi:</strong> <?= htmlspecialchars($template['deskripsi']); ?></p>
    </div>

    <!-- Form Tambah Item -->
    <form method="POST" class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
      <input type="text" name="indikator" placeholder="Indikator KPI" required
             class="p-2 border rounded w-full">
      <input type="number" name="bobot" step="0.01" placeholder="Bobot (%)" required
             class="p-2 border rounded w-full">
      <input type="number" name="target" step="0.01" placeholder="Target" required
             class="p-2 border rounded w-full">
      <input type="text" name="satuan" placeholder="%, unit, hari, Rp" required 
    		 class="border p-2 w-full">
      <select name="tipe" class="p-2 border rounded w-full" required>
    	<option value="normal">Normal (lebih besar lebih baik)</option>
    	<option value="inverse">Inverse (lebih kecil lebih baik)</option>
  	  </select>
      <button type="submit" name="add_item"
             class="bg-green-600 text-white p-2 rounded col-span-1 md:col-span-3">
        Tambah Item
      </button>
    </form>

    <!-- Tabel Items -->
    <table class="w-full border text-sm">
      <thead class="bg-gray-200">
        <tr>
          <th class="border px-2 py-1">Indikator</th>
      	  <th class="border px-2 py-1">Target</th>
      	  <th class="border px-2 py-1">Satuan</th>
      	  <th class="border px-2 py-1">Bobot</th>
      	  <th class="border px-2 py-1">Tipe</th>
          <th class="border p-2">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php while($i = $items->fetch(PDO::FETCH_ASSOC)): ?>
        <tr>
          <td class="border px-2 py-1"><?= htmlspecialchars($i['indikator']) ?></td>
          <td class="border px-2 py-1"><?= htmlspecialchars($i['target']) ?></td>
          <td class="border px-2 py-1"><?= htmlspecialchars($i['satuan']) ?></td>
          <td class="border px-2 py-1"><?= htmlspecialchars($i['bobot']) ?>%</td>
          <td class="border px-2 py-1"><?= htmlspecialchars($i['tipe']) ?></td>
          <td class="border p-2">          	
          <a href="/kpi-app/public/kpi_templates/detail/<?= $param ?>/delete/<?= $i['id'] ?>"
               onclick="return confirm('Hapus item ini?')"
               class="bg-red-500 text-white px-2 py-1 rounded">Hapus</a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>

    <p class="mt-4 font-semibold">
  Total Bobot: <?= $totalBobot; ?>%</p>

    <div class="mt-4">
      <a href="/kpi-app/public/kpi_templates" class="bg-gray-600 text-white px-4 py-2 rounded">Kembali</a>
    </div>

  </div>
</body>
</html>
