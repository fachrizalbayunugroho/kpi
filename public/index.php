<?php
    if(!isset($_SESSION)) 
    { 
        session_start(); 
    }
    else
    {
        session_destroy();
        session_start(); 
    }
require __DIR__ . '/../app/config/database.php';
require __DIR__ . '/../app/core/auth.php';

$page = $_GET['page'] ?? 'login';
$parts = explode('/', $page);

// jika tidak ada role → anggap halaman umum (login, logout, dashboard)
if (count($parts) === 1) {
    $file = $parts[0];
    $path = __DIR__ . '/../app/pages/' . $file . '.php';
} else {
    // ada role (admin/manager/user)
    $role = $parts[0];
    $file = $parts[1] ?? 'index';
    $path = __DIR__ . '/../app/pages/' . $role . '/' . $file . '.php';
}

// load file jika ada
if (file_exists($path)) {
    require $path;
}