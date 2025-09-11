<?php
//session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];
$role = $user['role'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="/kpi-app/src/output.css">
<style>
  header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: #222;
    padding: 10px 20px;
    color: white;
  }

  .name {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .name h1 {
    font-size: 1.5rem;
    margin: 0;
  }

  nav ul {
    display: flex;
    list-style: none;
    gap: 20px;
    margin: 0;
    padding: 0;
  }

  nav ul li a {
    color: white;
    text-decoration: none;
    font-size: 1rem;
    transition: color 0.3s;
  }

</style>
</head>

<header class="flex items-center justify-between bg-white shadow px-6 py-4 mb-6">
  <!-- Nama User -->
  <div>
    <h1 class="text-xl md:text-2xl font-bold text-gray-800">
      Halo, <?= htmlspecialchars($user['nama']) ?> 👋
    </h1>
  </div>

  <!-- Navigation Menu -->
  <nav>
    <ul class="flex items-center gap-4">
      <li>
        <a href="/kpi-app/public/logout"
           class="px-4 py-2 bg-red-500 text-white rounded-lg shadow hover:bg-red-600 transition">
          Logout
        </a>
      </li>
    </ul>
  </nav>
</header>

<body class="p-6 bg-gray-100">
	<div class="mt-4 text-xl">
		<p>Role kamu: <span class="font-semibold"><?= $role ?></span></p>
	</div>
    <!-- MENU ADMIN -->
    <?php if ($role === 'admin'): ?>
        <div class="mt-6 space-x-2">
            <a href="/kpi-app/public/admin/departments"
               class="px-3 py-2 md:px-4 md:py-3 bg-blue-600 text-white rounded hover:bg-blue-700">
                Kelola Departemen
            </a>
            <a href="/kpi-app/public/admin/users"
               class="px-3 py-2 md:px-4 md:py-3 bg-green-600 text-white rounded hover:bg-green-700">
                Kelola User
            </a>
        </div>
    <?php endif; ?>

    <!-- MENU MANAGER -->
    <?php if ($role === 'manager'): ?>
        <div class="mt-6 space-x-2">
            <a href="/kpi-app/public/manager/kpi_templates"
               class="px-3 py-2 md:px-5 md:py-3 bg-purple-600 text-white rounded hover:bg-purple-700">
                Template KPI
            </a>
            <a href="/kpi-app/public/manager/report"
               class="px-3 py-2 md:px-5 md:py-3 bg-yellow-600 text-white rounded hover:bg-yellow-700">
                Report
            </a>
        </div>
    <?php endif; ?>

    <!-- MENU USER -->
    <?php if ($role === 'user'): ?>
        <div class="mt-6">
            <a href="/kpi-app/public/user/my_kpi"
               class="px-3 py-2 md:px-5 md:py-3 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                Isi Realisasi KPI
            </a>
        </div>
    <?php endif; ?>

</body>
</html>
