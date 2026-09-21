<?php
require_once __DIR__ . '/../auth.php'; require_admin();
$pdo=db(); $id=(int)($_GET['id']??0); $item=null;
if($id){$st=$pdo->prepare("SELECT * FROM assets WHERE id=?");$st->execute([$id]);$item=$st->fetch();}
$item=$item ?: ['title'=>'','category'=>'Asset','description'=>'','thumbnail'=>'','file_url'=>'','external_url'=>'','published'=>1];
if(is_post()){
  $thumb=upload_image('thumbnail_file') ?: ($_POST['thumbnail']??'');
  $data=[trim($_POST['title']??''),$_POST['category']??'Asset',$_POST['description']??'',$thumb,$_POST['file_url']??'',$_POST['external_url']??'',isset($_POST['published'])?1:0];
  if($id){$st=$pdo->prepare("UPDATE assets SET title=?,category=?,description=?,thumbnail=?,file_url=?,external_url=?,published=? WHERE id=?");$st->execute([...$data,$id]);flash('success','Asset updated.');}
  else {$st=$pdo->prepare("INSERT INTO assets(title,category,description,thumbnail,file_url,external_url,published) VALUES(?,?,?,?,?,?,?)");$st->execute($data);flash('success','Asset created.');}
  redirect('index.php');
}
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?= $id?'Edit':'Add' ?> Asset</title><link rel="stylesheet" href="../assets/css/admin.css"></head><body><main class="wrap"><a href="index.php">← Dashboard</a><div class="form-card"><h1><?= $id?'Edit':'Add' ?> Asset</h1>
<form method="post" enctype="multipart/form-data">
<div class="field"><label>Title</label><input name="title" value="<?= e($item['title']) ?>" required></div>
<div class="field"><label>Category</label><input name="category" value="<?= e($item['category']) ?>"></div>
<div class="field"><label>Description</label><textarea name="description"><?= e($item['description']) ?></textarea></div>
<div class="field"><label>Thumbnail URL</label><input name="thumbnail" value="<?= e($item['thumbnail']) ?>"></div>
<div class="field"><label>Or upload thumbnail (max 5 MB)</label><input type="file" name="thumbnail_file" accept=".jpg,.jpeg,.png,.webp"></div>
<div class="field"><label>File URL/path</label><input name="file_url" value="<?= e($item['file_url']) ?>" placeholder="uploads/... or external URL"></div>
<div class="field"><label>External details URL</label><input name="external_url" value="<?= e($item['external_url']) ?>"></div>
<div class="check"><input type="checkbox" name="published" <?= $item['published']?'checked':'' ?>> Published</div>
<div class="actions"><button class="btn">Save Asset</button><a class="btn" href="index.php">Cancel</a></div>
</form></div></main></body></html>
