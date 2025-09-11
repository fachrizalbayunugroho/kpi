<?php
//session_start();
require_once __DIR__ . '/../config/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'nama' => $user['nama'],
            'role' => $user['role'],
            'departemen_id' => $user['departemen_id']
        ];
        header("Location: dashboard");
        exit;
    } else {
        $error = "Email atau password salah.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Aplikasi KPI</title>
    <link rel="stylesheet" href="/kpi-app/src/output.css">
</head>
<body class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="w-full max-w-sm p-6 bg-white rounded-xl shadow">
        <h1 class="mb-4 text-xl font-bold text-center">Login Aplikasi KPI</h1>
        <?php if ($error): ?>
            <p class="mb-2 text-red-500"><?= $error ?></p>
        <?php endif; ?>
        <form method="POST">
            <input type="email" name="email" placeholder="Email"
                class="w-full p-2 mb-3 border rounded" required>
            <input type="password" name="password" placeholder="Password"
                class="w-full p-2 mb-3 border rounded" required>
            <button type="submit"
                class="w-full p-2 text-white bg-blue-600 rounded hover:bg-blue-700">
                Login
            </button>
        </form>
    </div>
</body>
</html>