<?php

$user = $_SESSION['user'];

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? htmlspecialchars($page_title) : "KPI System"; ?></title>
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
      <b>Halo, <?= htmlspecialchars($user['nama']) ?></b>
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
