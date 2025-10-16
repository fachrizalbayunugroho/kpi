<?php
$page_title = "Manajemen Departemen";

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/auth.php';
include_once __DIR__ . '/../include/header.php';

// Hanya admin yang boleh akses
checkRole(['admin']);

// Tambah departemen
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
    $name = trim($_POST['name']);
    if ($name !== '') {
        $stmt = $pdo->prepare("INSERT INTO departments (name) VALUES (?)");
        $stmt->execute([$name]);
        header("Location: departments");
        exit;
    }
}

// Hapus departemen
if ($action === 'delete' && $param) {
    $stmt = $pdo->prepare("DELETE FROM departments WHERE id = :id");
    $stmt->execute([':id' => $param]);

   echo "<script>alert('Departemen berhasil dihapus'); window.location.href='/kpi-app/public/departments';</script>";
   exit;
}

// Ambil daftar departemen
$stmt = $pdo->query("SELECT * FROM departments ORDER BY id DESC");
$departments = $stmt->fetchAll();
?>
<h1 class="text-2xl font-bold text-center mt-4 mb-6">Manajemen Departemen</h1>
<div class="max-w-5xl mx-auto bg-white p-6 rounded-2xl shadow">
    <!-- Form tambah departemen -->
    <form method="POST" class="mb-6 flex gap-2">
        <input type="text" name="name" placeholder="Nama Departemen"
            class="flex-1 p-2 border rounded" required>
        <button type="submit"
            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Tambah
        </button>
    </form>

    <!-- Tabel daftar departemen -->
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-200 text-left">
                    <!--<th class="p-2 border">ID</th>-->
                    <th class="p-2 border">Nama Departemen</th>
                    <th class="p-2 border">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($departments as $dept): ?>
                <tr class="hover:bg-gray-50">
                    <!--<td class="p-2 border"><?= $dept['id'] ?></td>-->
                    <td class="p-2 border"><?= $dept['name'] ?></td>
                    <td class="p-2 border">
                        <a href="/kpi-app/public/departments/delete/<?= $dept['id'] ?>"
                           onclick="return confirm('Yakin hapus departemen ini?')"
                           class="px-2 py-1 text-white bg-red-500 rounded hover:bg-red-600">
                            Hapus
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($departments)): ?>
                <tr>
                    <td colspan="3" class="p-2 text-center text-gray-500">Belum ada departemen</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>

    <!-- Tombol kembali -->
    <div class="mt-4">
        <a href="../public/dashboard" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
            ⬅ Kembali ke Dashboard
        </a>
    </div>
</div>
</body>
</html>
