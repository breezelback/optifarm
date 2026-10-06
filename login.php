<?php require __DIR__.'/inc/auth.php';
$err='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $u=rows("SELECT * FROM users WHERE email=? AND status='Active'",[trim($_POST['email']??'')])[0]??null;
  if($u && password_verify($_POST['password']??'',$u['password'])){
    session_regenerate_id(true); unset($u['password']); $_SESSION['user']=$u; log_act($u['name'].' logged in');
    header('Location: '.home_for($u['role'])); exit;
  } $err='Invalid email or password.';
}
?><!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Login | OptiFarm</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="<?=BASE_URL?>/assets/style.css" rel="stylesheet"></head><body>
<div class="login-wrap"><div class="card p-4" style="width:380px"><div class="text-center mb-3">
<div class="fs-1 text-success"><i class="bi bi-flower2"></i></div><h3 class="fw-bold">OptiFarm</h3>
<div class="text-muted small">Analytics-Driven Agricultural Resource Optimization<br>AUG Agricultural Farming Company</div></div>
<?php if($err):?><div class="alert alert-danger py-2"><?=e($err)?></div><?php endif;?>
<form method="post"><?=csrf_field()?>
<label class="form-label">Email Address</label><input type="email" name="email" class="form-control mb-3" required autofocus>
<label class="form-label">Password</label>
<div class="input-group mb-3"><input type="password" id="pw" name="password" class="form-control" required>
<button type="button" class="btn btn-outline-secondary" onclick="pw.type=pw.type=='password'?'text':'password'"><i class="bi bi-eye"></i></button></div>
<button class="btn btn-primary w-100">Log In</button></form>
<div class="text-center mt-3 small"><a href="#" onclick="alert('Please contact your System Administrator to reset your password.');return false;">Forgot Password?</a></div>
</div></div></body></html>
