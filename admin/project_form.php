<?php
require_once __DIR__ . '/../auth.php'; require_admin();
$pdo=db(); $id=(int)($_GET['id']??0);
$item=$id ? $pdo->prepare("SELECT * FROM projects WHERE id=?")->execute([$id]) : false;
if ($id) { $st=$pdo->prepare("SELECT * FROM projects WHERE id=?"); $st->execute([$id]); $item=$st->fetch(); }
$item=$item ?: ['title'=>'','slug'=>'','category'=>'Project','description'=>'','thumbnail'=>'','project_url'=>'','github_url'=>'','model_url'=>'','model_format'=>'glb','featured'=>0,'published'=>1];
if(is_post()){
  $title=trim($_POST['title']??''); $slug=slugify($_POST['slug']??$title);
  $thumb=upload_image('thumbnail_file') ?: ($_POST['thumbnail']??'');
  $model=upload_model('model_file') ?: ($_POST['model_url']??'');
  $data=[$title,$slug,$_POST['category']??'Project',$_POST['description']??'',$thumb,$_POST['project_url']??'',$_POST['github_url']??'',$model,$_POST['model_format']??'glb',isset($_POST['featured'])?1:0,isset($_POST['published'])?1:0];
  if($id){$st=$pdo->prepare("UPDATE projects SET title=?,slug=?,category=?,description=?,thumbnail=?,project_url=?,github_url=?,model_url=?,model_format=?,featured=?,published=? WHERE id=?");$st->execute([...$data,$id]);flash('success','Project updated.');}
  else {$st=$pdo->prepare("INSERT INTO projects(title,slug,category,description,thumbnail,project_url,github_url,model_url,model_format,featured,published) VALUES(?,?,?,?,?,?,?,?,?,?,?)");$st->execute($data);flash('success','Project created.');}
  redirect('index.php');
}
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?= $id?'Edit':'Add' ?> Project</title><link rel="stylesheet" href="../assets/css/admin.css"></head><body><main class="wrap"><a href="index.php">← Dashboard</a><div class="form-card"><h1><?= $id?'Edit':'Add' ?> Project</h1>
<form method="post" enctype="multipart/form-data">
<div class="field"><label>Title</label><input name="title" value="<?= e($item['title']) ?>" required></div>
<div class="field"><label>Slug</label><input name="slug" value="<?= e($item['slug']) ?>" placeholder="auto from title"></div>
<div class="field"><label>Category</label><input name="category" value="<?= e($item['category']) ?>"></div>
<div class="field"><label>Description</label><textarea name="description"><?= e($item['description']) ?></textarea></div>
<div class="field"><label>Thumbnail URL</label><input name="thumbnail" value="<?= e($item['thumbnail']) ?>" placeholder="https://... or uploads/images/file.jpg"></div>
<div class="field"><label>Or upload thumbnail (max 5 MB)</label><input type="file" name="thumbnail_file" accept=".jpg,.jpeg,.png,.webp"></div>
<div class="field"><label>Project URL</label><input name="project_url" value="<?= e($item['project_url']) ?>"></div>
<div class="field"><label>GitHub URL</label><input name="github_url" value="<?= e($item['github_url']) ?>"></div>
<div class="field"><label>3D Model URL/path</label><input name="model_url" value="<?= e($item['model_url']) ?>" placeholder="uploads/models/car.glb or external URL"></div>
<div class="field"><label>Or upload model (max 50 MB)</label><input type="file" name="model_file" accept=".glb,.gltf,.fbx,.obj"></div>
<div class="field"><label>Model format</label><select name="model_format"><option <?= $item['model_format']=='glb'?'selected':'' ?>>glb</option><option <?= $item['model_format']=='gltf'?'selected':'' ?>>gltf</option><option <?= $item['model_format']=='fbx'?'selected':'' ?>>fbx</option><option <?= $item['model_format']=='obj'?'selected':'' ?>>obj</option></select></div>
<div class="check"><input type="checkbox" name="featured" <?= $item['featured']?'checked':'' ?>> Featured</div>
<div class="check"><input type="checkbox" name="published" <?= $item['published']?'checked':'' ?>> Published</div>
<div class="actions"><button class="btn">Save Project</button><a class="btn" href="index.php">Cancel</a></div>
</form></div></main></body></html>
