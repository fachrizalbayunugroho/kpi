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

$request = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

$segments = explode('/', $request);

// index 0 = kpi-app, index 1 = public
$page     = $segments[2] ?? 'login';  // default ke login
$param    = $segments[3] ?? null;     // default null

// mapping routes
$routes = [
    'login'      		=> __DIR__ . '/../app/pages/login.php',
    'logout'     		=> __DIR__ . '/../app/pages/logout.php',
    'dashboard'  		=> __DIR__ . '/../app/pages/dashboard.php',
    'departments'		=> __DIR__ . '/../app/pages/admin/departments.php',
    'users'      		=> __DIR__ . '/../app/pages/admin/users.php',
    'kpi_templates'     => __DIR__ . '/../app/pages/manager/kpi_templates.php',
    'report'     		=> __DIR__ . '/../app/pages/manager/report.php',
    'my_kpi' 			=> __DIR__ . '/../app/pages/user/my_kpi.php',
    'kpi_detail' 		=> __DIR__ . '/../app/pages/user/kpi_detail.php'
];

// cek apakah $page ada di routes
if (array_key_exists($page, $routes)) {
    include $routes[$page];
} else {
    echo "<h1>404 - Page Not Found</h1>";
}