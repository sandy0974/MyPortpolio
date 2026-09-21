<?php
require_once __DIR__ . '/auth.php';

require_user();

$current = current_user_profile();
if (!$current) {
    logout_current_user();
    redirect('login.php');
}

$success = '';
$error = '';

if (is_post()) {
    $fullName = trim((string) ($_POST['full_name'] ?? ''));
    $username = trim((string) ($_POST['username'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $confirmPassword = (string) ($_POST['confirm_password'] ?? '');

    if ($fullName === '' || $username === '' || $email === '') {
        $error = 'Nama lengkap, username, dan email wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } elseif ($password !== '' && $password !== $confirmPassword) {
        $error = 'Konfirmasi password tidak cocok.';
    } else {
        $check = db()->prepare('SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ? LIMIT 1');
        $check->execute([$username, $email, (int) $current['id']]);
        if ($check->fetch()) {
            $error = 'Username atau email sudah dipakai pengguna lain.';
        } else {
            $params = [$fullName, $username, $email];
            $sql = 'UPDATE users SET full_name = ?, username = ?, email = ?';

            if ($password !== '') {
                $sql .= ', password_hash = ?';
                $params[] = password_hash($password, PASSWORD_DEFAULT);
            }

            $sql .= ' WHERE id = ?';
            $params[] = (int) $current['id'];

            $stmt = db()->prepare($sql);
            $stmt->execute($params);

            $_SESSION['user_username'] = $username;
            $_SESSION['user_name'] = $fullName;
            $_SESSION['user_email'] = $email;

            $current = current_user_profile();
            $success = 'Profil berhasil diperbarui.';
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Profil Saya</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <main class="profile-shell">
    <div class="profile-card">
      <p class="eyebrow">ACCOUNT</p>
      <h1>Profil Saya</h1>

      <?php if ($error): ?>
        <div class="flash error"><?= e($error) ?></div>
      <?php endif; ?>
      <?php if ($success): ?>
        <div class="flash success"><?= e($success) ?></div>
      <?php endif; ?>

      <div class="profile-meta">
        <div>
          <strong>Username</strong>
          <span><?= e($current['username'] ?? '') ?></span>
        </div>
        <div>
          <strong>Email</strong>
          <span><?= e($current['email'] ?? '') ?></span>
        </div>
      </div>

      <form method="post" action="profile.php">
        <div class="field">
          <label for="full_name">Nama Lengkap</label>
          <input id="full_name" name="full_name" type="text" value="<?= e($current['full_name'] ?? '') ?>" required>
        </div>

        <div class="field">
          <label for="username">Username</label>
          <input id="username" name="username" type="text" value="<?= e($current['username'] ?? '') ?>" required>
        </div>

        <div class="field">
          <label for="email">Email</label>
          <input id="email" name="email" type="email" value="<?= e($current['email'] ?? '') ?>" required>
        </div>

        <div class="field">
          <label for="password">Password Baru</label>
          <input id="password" name="password" type="password" placeholder="Kosongkan bila tidak ingin mengubah">
        </div>

        <div class="field">
          <label for="confirm_password">Konfirmasi Password Baru</label>
          <input id="confirm_password" name="confirm_password" type="password" placeholder="Ulangi password baru">
        </div>

        <div class="profile-actions">
          <button class="btn btn-primary" type="submit">Simpan Perubahan</button>
          <a class="btn btn-ghost" href="index.php">Beranda</a>
          <a class="btn btn-ghost" href="logout.php">Logout</a>
        </div>
      </form>
    </div>
  </main>
</body>
</html>
