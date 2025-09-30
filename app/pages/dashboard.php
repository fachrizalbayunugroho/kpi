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
 	nav { 
 	background: #0f172a; 
 	padding: 10px 20px; 
 	display: flex; 
 	justify-content: space-between; 
 	align-items: center; 
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
 	nav ul li a:hover { 
 	color: #60a5fa; 
 	} 
 	.logout-btn { 
 	padding: 6px 16px; 
 	background: #ef4444; 
 	color: white; 
 	border-radius: 6px; 
 	box-shadow: 0 2px 4px rgba(0,0,0,0.2); 
 	transition: background 0.3s; 
 	} 
 	.logout-btn:hover { 
 	background: #dc2626; 
 	}
 	.nama {
 	color: white;
 	font-size: 1.5rem;
 	}</style>
</head>
<body class="p-6 bg-gray-100">
	<nav>
	<div>
    <h1 class="nama">
      <b>Halo, <?= htmlspecialchars($user['nama']) ?></b> 👋
    </h1>
	</div>
    <ul>
      <li>
        <a href="/kpi-app/public/change_password">
          Change Password
        </a>
      </li>
      <li>
        <a href="/kpi-app/public/logout" class="logout-btn">
          Logout
        </a>
      </li>
    </ul>
	</nav>
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
