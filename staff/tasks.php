<?php require __DIR__.'/../inc/auth.php'; require __DIR__.'/../inc/layout.php'; need_role('staff'); $id=$_SESSION['user']['id'];
if($_SERVER['REQUEST_METHOD']==='POST'){
  $t=rows("SELECT * FROM tasks WHERE id=? AND assigned_to=?",[$_POST['id'],$id])[0]??null;
  if($t && in_array($_POST['status'],['Pending','In Progress','Completed'])){
    q("UPDATE tasks SET status=? WHERE id=?",[$_POST['status'],$t['id']]);
    if($_POST['status']=='Completed' && $t['status']!='Completed'){ // deduct stock & record usage
      q("UPDATE inventory SET qty=GREATEST(0,qty-?) WHERE fertilizer=? AND expiry>=CURDATE() ORDER BY expiry LIMIT 1",[$t['qty'],$t['fertilizer']]);
      q("INSERT INTO stock_movements(month_date,stock_out) VALUES(DATE_FORMAT(CURDATE(),'%Y-%m-01'),?) ON DUPLICATE KEY UPDATE stock_out=stock_out+VALUES(stock_out)",[$t['qty']]);
    }
    log_act('Task #'.$t['id'].' set to '.$_POST['status']); flash('Task updated.');
  } header('Location: tasks.php'); exit;
}
$list=rows("SELECT t.*,f.name fname FROM tasks t JOIN fields f ON f.id=t.field_id WHERE assigned_to=? ORDER BY t.status='Completed',t.app_date",[$id]);
top('My Tasks'); ?>
<div class="card"><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Field</th><th>Fertilizer</th><th>When</th><th>Method</th><th>Note</th><th>Priority</th><th>Update Status</th></tr></thead><tbody>
<?php foreach($list as $t):?><tr><td><?=e($t['fname'])?></td><td><?=e($t['fertilizer'].' ('.$t['qty'].')')?></td><td><?=e($t['app_date'].' '.substr($t['app_time'],0,5))?></td><td><?=e($t['app_type'])?></td><td class="small"><?=e($t['note'])?></td><td><?=badge($t['priority'])?></td>
<td><form method="post" class="d-flex gap-1"><?=csrf_field()?><input type="hidden" name="id" value="<?=$t['id']?>"><select name="status" class="form-select form-select-sm"><?php foreach(['Pending','In Progress','Completed'] as $s) echo '<option '.($t['status']==$s?'selected':'').'>'.$s.'</option>';?></select><button class="btn btn-sm btn-primary">Save</button></form></td></tr><?php endforeach;?></tbody></table></div></div>
<?php bottom();
