<?php
require_once __DIR__ . '/auth.php';

if (is_admin_logged_in()) {
    redirect('admin/index.php');
}

if (is_logged_in()) {
    redirect('index.php');
}

$error = '';

if (is_post()) {
    $identifier = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    $admin = null;
    if ($identifier !== '') {
        $stmt = db()->prepare('SELECT * FROM admins WHERE username = ? LIMIT 1');
        $stmt->execute([$identifier]);
        $admin = $stmt->fetch();
    }

    if ($admin && password_verify($password, $admin['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = (int) $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        redirect('admin/index.php');
    }

    $user = null;
    if ($identifier !== '') {
        $stmt = db()->prepare('SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1');
        $stmt->execute([$identifier, $identifier]);
        $user = $stmt->fetch();
    }

    if ($user && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['user_username'] = $user['username'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['user_email'] = $user['email'];
        redirect('index.php');
    }

    $error = 'Username atau password salah.';
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <main class="auth-shell">
    <div class="form-card">
      <p class="eyebrow">PORTFOLIO</p>
      <h1>Login</h1>

      <?php if ($error): ?>
        <div class="flash error"><?= e($error) ?></div>
      <?php endif; ?>

      <form method="post" action="login.php">
        <div class="field">
          <label for="username">Username atau Email</label>
          <input id="username" name="username" type="text" required>
        </div>

        <div class="field">
          <label for="password">Password</label>
          <input id="password" name="password" type="password" required>
        </div>

        <div class="form-actions">
          <button class="btn btn-primary" type="submit">Masuk</button>
          <a class="btn btn-ghost" href="register.php">Daftar</a>
        </div>
      </form>
    </div>
  </main>
</body>
</html>
