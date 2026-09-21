<?php
require_once __DIR__ . '/../auth.php'; require_admin();
$pdo=db(); $settings=$pdo->query("SELECT * FROM settings WHERE id=1")->fetch();
if(is_post()){
 $st=$pdo->prepare("UPDATE settings SET site_title=?,tagline=?,about=?,github_url=?,itch_url=?,email=? WHERE id=1");
 $st->execute([$_POST['site_title']??'',$_POST['tagline']??'',$_POST['about']??'',$_POST['github_url']??'',$_POST['itch_url']??'',$_POST['email']??'']);
 flash('success','Settings updated.'); redirect('settings.php');
}
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Settings</title><link rel="stylesheet" href="../assets/css/admin.css"></head><body><main class="wrap"><a href="index.php">← Dashboard</a><div class="form-card"><h1>Site Settings</h1><form method="post">
<div class="field"><label>Site title</label><input name="site_title" value="<?= e($settings['site_title']) ?>"></div>
<div class="field"><label>Tagline</label><input name="tagline" value="<?= e($settings['tagline']) ?>"></div>
<div class="field"><label>About</label><textarea name="about"><?= e($settings['about']) ?></textarea></div>
<div class="field"><label>GitHub URL</label><input name="github_url" value="<?= e($settings['github_url']) ?>"></div>
<div class="field"><label>itch.io URL</label><input name="itch_url" value="<?= e($settings['itch_url']) ?>"></div>
<div class="field"><label>Email</label><input name="email" value="<?= e($settings['email']) ?>"></div>
<div class="actions"><button class="btn">Save</button></div></form></div></main></body></html>
