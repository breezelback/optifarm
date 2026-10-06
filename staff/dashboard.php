<?php require __DIR__.'/../inc/auth.php'; require __DIR__.'/../inc/layout.php'; need_role('staff'); $id=$_SESSION['user']['id'];
$c=array_column(rows("SELECT status,COUNT(*) c FROM tasks WHERE assigned_to=? GROUP BY status",[$id]),'c','status');
$next=rows("SELECT t.*,f.name fname FROM tasks t JOIN fields f ON f.id=t.field_id WHERE assigned_to=? AND t.status<>'Completed' ORDER BY t.app_date,t.app_time LIMIT 5",[$id]);
top('My Dashboard'); ?>
<div class="row g-3 mb-3"><?php kpi('Pending',$c['Pending']??0,'bi-clock','warning');kpi('In Progress',$c['In Progress']??0,'bi-arrow-repeat','info');kpi('Completed',$c['Completed']??0,'bi-check2-circle');kpi('Low Stock Items',count(array_filter(rows("SELECT * FROM inventory"),fn($r)=>stock_status($r)!='Good')),'bi-exclamation-triangle','danger');?></div>
<div class="card"><div class="card-body"><h6>Upcoming Tasks</h6><table class="table align-middle mb-0"><thead><tr><th>Field</th><th>Fertilizer</th><th>When</th><th>Priority</th><th>Status</th></tr></thead><tbody>
<?php foreach($next as $t) echo '<tr><td>'.e($t['fname']).'</td><td>'.e($t['fertilizer']).' ('.e($t['qty']).')</td><td>'.e($t['app_date'].' '.substr($t['app_time'],0,5)).'</td><td>'.badge($t['priority']).'</td><td>'.badge($t['status']).'</td></tr>';
if(!$next) echo '<tr><td colspan="5" class="text-muted">No upcoming tasks.</td></tr>';?></tbody></table></div></div>
<?php bottom();
