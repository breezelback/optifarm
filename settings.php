<?php require __DIR__.'/inc/auth.php'; require __DIR__.'/inc/layout.php'; need_role();
$id=$_SESSION['user']['id'];
if($_SERVER['REQUEST_METHOD']==='POST'){
  $pw=$_POST['password']??'';
  if($pw!=='' && ($pw!==$_POST['confirm'] || strlen($pw)<8)) flash('Passwords must match and be at least 8 characters.','danger');
  else try {
    q("UPDATE users SET name=?,email=? WHERE id=?",[trim($_POST['name']),trim($_POST['email']),$id]);
    if($pw!=='') q("UPDATE users SET password=? WHERE id=?",[password_hash($pw,PASSWORD_DEFAULT),$id]);
    $u=rows("SELECT * FROM users WHERE id=?",[$id])[0]; unset($u['password']); $_SESSION['user']=$u; flash('Settings saved.');
  } catch(PDOException $x){ flash('Email already in use.','danger'); }
  header('Location: settings.php'); exit;
}
$u=$_SESSION['user']; top('Settings'); ?>
<div class="card" style="max-width:640px"><form method="post" class="card-body row g-3"><?=csrf_field()?>
<div class="col-12"><label class="form-label">Full Name</label><input name="name" class="form-control" value="<?=e($u['name'])?>" required></div>
<div class="col-12"><label class="form-label">Email Address</label><input type="email" name="email" class="form-control" value="<?=e($u['email'])?>" required></div>
<div class="col-12"><label class="form-label">Role</label><input class="form-control" value="<?=e(strtoupper($u['role']))?>" disabled></div>
<div class="col-md-6"><label class="form-label">New Password</label><input type="password" name="password" class="form-control"></div>
<div class="col-md-6"><label class="form-label">Confirm Password</label><input type="password" name="confirm" class="form-control"></div>
<div class="col-12"><button class="btn btn-primary">Save Changes</button></div></form></div>
<?php bottom();
