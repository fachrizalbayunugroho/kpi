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

$requestUri = $_SERVER['REQUEST_URI'];
$basePath   = '/kpi-app/public'; // sesuaikan dengan folder project kamu
$path       = str_replace($basePath, '', $requestUri);
$segments   = array_values(array_filter(explode('/', $path)));

$page   = $segments[0] ?? 'login';  
$action = $segments[1] ?? null;     
$param  = $segments[2] ?? null;     

switch ($page) {
    case 'kpi_templates':
        if ($action === 'assign' && $param) {
            require __DIR__ . '/../app/pages/manager/assign_kpi.php';
        } elseif ($action === 'detail' && $param) {
          	require __DIR__ . '/../app/pages/manager/template_item.php';
        } else {
            require __DIR__ . '/../app/pages/manager/kpi_templates.php'; 
        }
        break;

    case 'report':
        if ($action === 'detail' && $param) {
            require __DIR__ . '/../app/pages/manager/report_detail.php';
        } elseif ($action === 'export' && $param) {
          	require __DIR__ . '/../app/pages/manager/export.php';
        } else {
            require __DIR__ . '/../app/pages/manager/report.php';
        }
        break;

    case 'users':
        require __DIR__ . '/../app/pages/admin/users.php';
        break;

    case 'departments':
        require __DIR__ . '/../app/pages/admin/departments.php';
        break;

    case 'login':
        require __DIR__ . '/../app/pages/login.php';
        break;

    case 'logout':
        require __DIR__ . '/../app/pages/logout.php';
        break;

    case 'change_password':
        require __DIR__ . '/../app/pages/change_password.php';
        break;

    case 'dashboard':
        require __DIR__ . '/../app/pages/dashboard.php';
        break;

    case 'my_kpi':
        if ($action === 'detail' && $param) {
            require __DIR__ . '/../app/pages/user/kpi_detail.php';
        } elseif ($action === 'save') {
            require __DIR__ . '/../app/pages/user/save.php';
        } else {
            require __DIR__ . '/../app/pages/user/my_kpi.php';
        }
        break;
}