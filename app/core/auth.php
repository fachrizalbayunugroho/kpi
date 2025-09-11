<?php
function checkRole($allowedRoles = []) {
    if (!isset($_SESSION['user'])) {
        header("Location: login.php");
        exit;
    }

    $role = $_SESSION['user']['role'];
    if (!in_array($role, $allowedRoles)) {
        echo "⚠️ Akses ditolak. Anda tidak punya izin.";
        exit;
    }
}
