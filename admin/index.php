<?php
require_once __DIR__ . '/../auth.php';
require_admin();
$pdo = db();
$projects = $pdo->query("SELECT * FROM projects ORDER BY created_at DESC")->fetchAll();
$assets = $pdo->query("SELECT * FROM assets ORDER BY created_at DESC")->fetchAll();
$flash = get_flash();
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Admin Dashboard</title><link rel="stylesheet" href="../assets/css/admin.css"></head>
<body>
<header class="admin-nav"><strong>Portfolio Admin</strong><nav><a href="../index.php" target="_blank">View Site</a><a href="settings.php">Settings</a><a href="logout.php">Logout</a></nav></header>
<main class="wrap">
<?php if ($flash): ?><div class="flash <?= e($flash['type']) ?>"><?= e($flash['message']) ?></div><?php endif; ?>
<div class="top"><div><p class="eyebrow">CONTROL PANEL</p><h1>Dashboard</h1></div><div><a class="btn" href="project_form.php">+ Project</a> <a class="btn" href="asset_form.php">+ Asset</a></div></div>

<section><h2>Projects</h2><div class="table-wrap"><table><tr><th>Title</th><th>Category</th><th>Published</th><th>Actions</th></tr>
<?php foreach ($projects as $p): ?><tr><td><?= e($p['title']) ?></td><td><?= e($p['category']) ?></td><td><?= $p['published'] ? 'Yes':'No' ?></td><td><a href="project_form.php?id=<?= (int)$p['id'] ?>">Edit</a> · <a href="project_delete.php?id=<?= (int)$p['id'] ?>" onclick="return confirm('Delete this project?')">Delete</a></td></tr><?php endforeach; ?>
</table></div></section>

<section><h2>Assets</h2><div class="table-wrap"><table><tr><th>Title</th><th>Category</th><th>Published</th><th>Actions</th></tr>
<?php foreach ($assets as $a): ?><tr><td><?= e($a['title']) ?></td><td><?= e($a['category']) ?></td><td><?= $a['published'] ? 'Yes':'No' ?></td><td><a href="asset_form.php?id=<?= (int)$a['id'] ?>">Edit</a> · <a href="asset_delete.php?id=<?= (int)$a['id'] ?>" onclick="return confirm('Delete this asset?')">Delete</a></td></tr><?php endforeach; ?>
</table></div></section>
</main></body></html>
