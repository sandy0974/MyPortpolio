<?php
require_once __DIR__ . '/../auth.php'; require_admin();
$id=(int)($_GET['id']??0);
db()->prepare("DELETE FROM projects WHERE id=?")->execute([$id]);
flash('success','Project deleted.');
redirect('index.php');
