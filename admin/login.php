<?php
require_once __DIR__ . '/../auth.php';
if (!empty($_SESSION['admin_id'])) redirect('index.php');
$error = '';
if (is_post()) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $stmt = db()->prepare("SELECT * FROM admins WHERE username=? LIMIT 1");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();
    if ($admin && password_verify($password, $admin['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        redirect('index.php');
    }
    $error = 'Username atau password salah.';
}
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Admin Login</title><link rel="stylesheet" href="../assets/css/admin.css"></head>
<body><main class="wrap"><div class="form-card" style="max-width:430px;margin:90px auto"><p class="eyebrow">PORTFOLIO ADMIN</p><h1>Login</h1>
<?php if ($error): ?><div class="flash error"><?= e($error) ?></div><?php endif; ?>
<form method="post"><div class="field"><label>Username</label><input name="username" required></div><div class="field"><label>Password</label><input type="password" name="password" required></div><button class="btn">Login</button></form></div></main></body></html>
