<?php require __DIR__.'/../inc/auth.php'; require __DIR__.'/../inc/layout.php'; need_role('admin');
if($_SERVER['REQUEST_METHOD']==='POST'){
  $p=$_POST;
  if(($p['admin_code']??'')!==ADMIN_CODE) flash('Invalid Admin Code Access.','danger');
  else try {
    if($p['do']==='add'){
      q("INSERT INTO users(name,email,password,role) VALUES(?,?,?,?)",[trim($p['name']),trim($p['email']),password_hash($p['password'],PASSWORD_DEFAULT),$p['role']]);
      log_act('User added: '.$p['email']); flash('User added.');
    } else {
      $st=((int)$p['id']===$_SESSION['user']['id'])?'Active':$p['status'];
      q("UPDATE users SET name=?,email=?,role=?,status=? WHERE id=?",[trim($p['name']),trim($p['email']),$p['role'],$st,$p['id']]);
      if(!empty($p['password'])) q("UPDATE users SET password=? WHERE id=?",[password_hash($p['password'],PASSWORD_DEFAULT),$p['id']]);
      log_act('User updated: '.$p['email']); flash('User updated.');
    }
  } catch(PDOException $x){ flash('Email already exists.','danger'); }
  header('Location: users.php'); exit;
}
$s=trim($_GET['s']??''); $list=rows("SELECT * FROM users WHERE name LIKE ? OR email LIKE ? ORDER BY id",["%$s%","%$s%"]);
$roles=['admin'=>'Administrator','staff'=>'Agriculturist Staff','bod'=>'Board of Directors'];
function userform($mode,$roles){ ?>
<?=csrf_field()?><input type="hidden" name="do" value="<?=$mode?>"><input type="hidden" name="id" id="<?=$mode?>_id">
<div class="mb-2"><label class="form-label">Full Name</label><input name="name" id="<?=$mode?>_name" class="form-control" required></div>
<div class="mb-2"><label class="form-label">Email Address</label><input type="email" name="email" id="<?=$mode?>_email" class="form-control" required></div>
<div class="mb-2"><label class="form-label">Assign Role</label><select name="role" id="<?=$mode?>_role" class="form-select"><?php foreach($roles as $k=>$v) echo "<option value='$k'>$v</option>";?></select></div>
<?php if($mode=='edit'):?><div class="mb-2"><label class="form-label">Status</label><select name="status" id="edit_status" class="form-select"><option>Active</option><option>Inactive</option></select></div><?php endif;?>
<div class="mb-2"><label class="form-label"><?=$mode=='add'?'Password':'Change Password (leave blank to keep)'?></label><input type="password" name="password" class="form-control" <?=$mode=='add'?'required minlength="8"':''?>></div>
<div class="mb-2"><label class="form-label">Admin Code Access</label><input type="password" name="admin_code" class="form-control" required></div>
<?php }
top('User Management'); ?>
<div class="d-flex gap-2 mb-3"><form class="flex-grow-1"><input name="s" value="<?=e($s)?>" class="form-control" placeholder="Search name or email..."></form>
<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addM"><i class="bi bi-plus-lg"></i> Add User</button></div>
<div class="card"><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Actions</th></tr></thead><tbody>
<?php foreach($list as $u):?><tr><td><?=e($u['name'])?></td><td><?=e($u['email'])?></td><td><?=e($roles[$u['role']])?></td><td><?=badge($u['status'])?></td>
<td><a href="#" class="edit" data-bs-toggle="modal" data-bs-target="#editM" data-u='<?=e(json_encode($u))?>'>Edit</a></td></tr><?php endforeach;?></tbody></table></div></div>
<div class="modal fade" id="addM"><div class="modal-dialog"><form method="post" class="modal-content"><div class="modal-header"><h5>Add User</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
<div class="modal-body"><?php userform('add',$roles);?></div><div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary">Add User</button></div></form></div></div>
<div class="modal fade" id="editM"><div class="modal-dialog"><form method="post" class="modal-content"><div class="modal-header"><h5>Edit User</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
<div class="modal-body"><?php userform('edit',$roles);?></div><div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary">Save Changes</button></div></form></div></div>
<?php $page_js='<script>document.querySelectorAll(".edit").forEach(a=>a.onclick=()=>{const u=JSON.parse(a.dataset.u);["id","name","email","role","status"].forEach(k=>document.getElementById("edit_"+k).value=u[k])})</script>'; bottom();
