<?php require __DIR__.'/../inc/auth.php'; require __DIR__.'/../inc/layout.php'; need_role('staff'); $id=$_SESSION['user']['id'];
if($_SERVER['REQUEST_METHOD']==='POST'){ q("INSERT INTO field_reports(user_id,field_id,note) VALUES(?,?,?)",[$id,$_POST['field_id'],trim($_POST['note'])]); log_act('Field report submitted'); flash('Field report submitted.'); header('Location: reports.php'); exit; }
$fields=rows("SELECT * FROM fields"); $list=rows("SELECT r.*,f.name fname FROM field_reports r JOIN fields f ON f.id=r.field_id WHERE r.user_id=? ORDER BY r.id DESC",[$id]);
top('Field Reports'); ?>
<div class="row g-3"><div class="col-lg-5"><div class="card"><form method="post" class="card-body"><?=csrf_field()?><h6>New Field Report</h6>
<label class="form-label">Field</label><select name="field_id" class="form-select mb-3"><?php foreach($fields as $f) echo "<option value='{$f['id']}'>".e($f['name'])."</option>";?></select>
<label class="form-label">Observations / Updates</label><textarea name="note" rows="5" class="form-control mb-3" required placeholder="Crop condition, pests, shortages, delays..."></textarea><button class="btn btn-primary">Submit Report</button></form></div></div>
<div class="col-lg-7"><div class="card"><div class="card-body"><h6>My Reports</h6>
<?php foreach($list as $r) echo '<div class="border-bottom py-2"><b>'.e($r['fname']).'</b> <span class="small text-muted">'.e($r['created_at']).'</span><div>'.nl2br(e($r['note'])).'</div></div>'; if(!$list) echo '<div class="text-muted">No reports yet.</div>';?></div></div></div></div>
<?php bottom();
