<?php
//session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
$page_title = "Dashboard";
include_once __DIR__ . '/include/header.php';

$user = $_SESSION['user'];
$role = $user['role'];

?>
	<div class="mt-4 text-xl">
		<p>Role kamu: <span class="font-semibold"><?= $role ?></span></p>
	</div>
    <!-- MENU ADMIN -->
    <?php if ($role === 'admin'): ?>
        <div class="mt-6 space-x-2">
            <a href="/kpi-app/public/departments"
               class="px-3 py-2 md:px-4 md:py-3 bg-blue-600 text-white rounded hover:bg-blue-700">
                Kelola Departemen
            </a>
            <a href="/kpi-app/public/users/page/1"
               class="px-3 py-2 md:px-4 md:py-3 bg-green-600 text-white rounded hover:bg-green-700">
                Kelola User
            </a>
        </div>
    <?php endif; ?>

    <!-- MENU MANAGER -->
    <?php if ($role === 'manager'): ?>
        <div class="mt-6 space-x-2">
            <a href="/kpi-app/public/kpi_templates"
               class="px-3 py-2 md:px-5 md:py-3 bg-purple-600 text-white rounded hover:bg-purple-700">
                Template KPI
            </a>
            <a href="/kpi-app/public/report"
               class="px-3 py-2 md:px-5 md:py-3 bg-yellow-600 text-white rounded hover:bg-yellow-700">
                Report
            </a>
        </div>
    <?php endif; ?>

    <!-- MENU USER -->
    <?php if ($role === 'user'): ?>
        <div class="mt-6">
            <a href="/kpi-app/public/my_kpi"
               class="px-3 py-2 md:px-5 md:py-3 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                Isi Realisasi KPI
            </a>
        </div>
    <?php endif; ?>

</body>
</html>
