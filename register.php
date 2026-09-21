<?php
require_once __DIR__ . '/auth.php';

if (is_admin_logged_in()) {
    redirect('admin/index.php');
}

if (is_logged_in()) {
    redirect('index.php');
}

$error = '';
$success = '';

if (is_post()) {
    $fullName = trim((string) ($_POST['full_name'] ?? ''));
    $username = trim((string) ($_POST['username'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $confirmPassword = (string) ($_POST['confirm_password'] ?? '');

    if ($fullName === '' || $username === '' || $email === '' || $password === '') {
        $error = 'Semua field wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Konfirmasi password tidak cocok.';
    } else {
        $check = db()->prepare('SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1');
        $check->execute([$username, $email]);
        if ($check->fetch()) {
            $error = 'Username atau email sudah terdaftar.';
        } else {
            $stmt = db()->prepare('INSERT INTO users (full_name, username, email, password_hash) VALUES (?, ?, ?, ?)');
            $stmt->execute([$fullName, $username, $email, password_hash($password, PASSWORD_DEFAULT)]);

            $success = 'Akun berhasil dibuat. Silakan login.';
            $_POST = [];
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Register</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <main class="auth-shell">
    <div class="form-card">
      <p class="eyebrow">CREATE ACCOUNT</p>
      <h1>Register</h1>

      <?php if ($error): ?>
        <div class="flash error"><?= e($error) ?></div>
      <?php endif; ?>
      <?php if ($success): ?>
        <div class="flash success"><?= e($success) ?></div>
      <?php endif; ?>

      <form method="post" action="register.php">
        <div class="field">
          <label for="full_name">Nama Lengkap</label>
          <input id="full_name" name="full_name" type="text" value="<?= e($_POST['full_name'] ?? '') ?>" required>
        </div>

        <div class="field">
          <label for="username">Username</label>
          <input id="username" name="username" type="text" value="<?= e($_POST['username'] ?? '') ?>" required>
        </div>

        <div class="field">
          <label for="email">Email</label>
          <input id="email" name="email" type="email" value="<?= e($_POST['email'] ?? '') ?>" required>
        </div>

        <div class="field">
          <label for="password">Password</label>
          <input id="password" name="password" type="password" required>
        </div>

        <div class="field">
          <label for="confirm_password">Konfirmasi Password</label>
          <input id="confirm_password" name="confirm_password" type="password" required>
        </div>

        <div class="form-actions">
          <button class="btn btn-primary" type="submit">Daftar</button>
          <a class="btn btn-ghost" href="login.php">Sudah punya akun?</a>
        </div>
      </form>
    </div>
  </main>
</body>
</html>
