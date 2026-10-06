<?php require __DIR__.'/../inc/auth.php'; require __DIR__.'/../inc/layout.php'; need_role('admin');
if($_SERVER['REQUEST_METHOD']==='POST'){
  $p=$_POST;
  if(($p['do']??'')==='delete'){ q("DELETE FROM tasks WHERE id=?",[$p['id']]); flash('Task deleted.'); }
  else { q("INSERT INTO tasks(field_id,fertilizer,app_date,app_time,assigned_to,note,app_type,qty,repeat_every,priority) VALUES(?,?,?,?,?,?,?,?,?,?)",
    [$p['field_id'],$p['fertilizer'],$p['app_date'],$p['app_time'],$p['assigned_to'],trim($p['note']),$p['app_type'],$p['qty'],$p['repeat_every'],$p['priority']]);
    log_act('Task scheduled: '.$p['fertilizer']); flash('Task scheduled.'); }
  header('Location: tasks.php'); exit;
}
$tasks=rows("SELECT t.*,f.name fname,u.name uname FROM tasks t JOIN fields f ON f.id=t.field_id LEFT JOIN users u ON u.id=t.assigned_to ORDER BY t.app_date DESC");
$fields=rows("SELECT * FROM fields"); $staff=rows("SELECT id,name FROM users WHERE role='staff' AND status='Active'"); $fert=rows("SELECT DISTINCT fertilizer FROM inventory");
top('Fertilizer Application Tasks'); ?>
<div class="d-flex justify-content-end mb-3"><button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#taskM"><i class="bi bi-plus-lg"></i> Add Task</button></div>
<div class="card"><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Field</th><th>Fertilizer</th><th>Date / Time</th><th>Assigned To</th><th>Qty</th><th>Priority</th><th>Status</th><th></th></tr></thead><tbody>
<?php foreach($tasks as $t):?><tr><td><?=e($t['fname'])?></td><td><?=e($t['fertilizer'])?><div class="small text-muted"><?=e($t['app_type'])?></div></td><td><?=e($t['app_date'].' '.substr($t['app_time'],0,5))?></td><td><?=e($t['uname'])?></td><td><?=e($t['qty'])?></td><td><?=badge($t['priority'])?></td><td><?=badge($t['status'])?></td>
<td><form method="post" onsubmit="return confirm('Delete this task?')"><?=csrf_field()?><input type="hidden" name="do" value="delete"><input type="hidden" name="id" value="<?=$t['id']?>"><button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form></td></tr><?php endforeach;?></tbody></table></div></div>
<div class="modal fade" id="taskM"><div class="modal-dialog modal-lg"><form method="post" class="modal-content"><?=csrf_field()?><div class="modal-header"><h5>Add Task</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body row g-3">
<div class="col-md-6"><label class="form-label">Field</label><select name="field_id" class="form-select"><?php foreach($fields as $f) echo "<option value='{$f['id']}'>".e($f['name'])."</option>";?></select></div>
<div class="col-md-6"><label class="form-label">Fertilizer</label><select name="fertilizer" class="form-select"><?php foreach($fert as $f) echo '<option>'.e($f['fertilizer']).'</option>';?></select></div>
<div class="col-md-6"><label class="form-label">Application Date</label><input type="date" name="app_date" class="form-control" required></div>
<div class="col-md-6"><label class="form-label">Time</label><input type="time" name="app_time" class="form-control" required></div>
<div class="col-md-6"><label class="form-label">Assigned To</label><select name="assigned_to" class="form-select"><?php foreach($staff as $s) echo "<option value='{$s['id']}'>".e($s['name'])."</option>";?></select></div>
<div class="col-md-6"><label class="form-label">Application Type</label><select name="app_type" class="form-select"><option>Broadcast<option>Side-dress<option>Basal<option>Foliar spray<option>Drip</select></div>
<div class="col-md-4"><label class="form-label">Quantity</label><input type="number" step="0.01" name="qty" class="form-control" required></div>
<div class="col-md-4"><label class="form-label">Repeat</label><select name="repeat_every" class="form-select"><option>None<option>Weekly<option>Monthly</select></div>
<div class="col-md-4"><label class="form-label">Priority</label><select name="priority" class="form-select"><option>Low<option selected>Medium<option>High</select></div>
<div class="col-12"><label class="form-label">Note</label><textarea name="note" class="form-control" rows="2"></textarea></div></div>
<div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary">Save Changes</button></div></form></div></div>
<?php bottom();
