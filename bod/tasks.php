<?php require __DIR__.'/../inc/auth.php'; require __DIR__.'/../inc/layout.php'; need_role('bod','admin');
$t=rows("SELECT t.*,f.name fname,u.name uname FROM tasks t JOIN fields f ON f.id=t.field_id LEFT JOIN users u ON u.id=t.assigned_to ORDER BY t.app_date DESC");
$c=array_count_values(array_column($t,'status'));
top('Tasks Overview'); ?>
<div class="row g-3 mb-3"><?php kpi('Total Tasks',count($t),'bi-list-task');kpi('Completed',$c['Completed']??0,'bi-check2-circle');kpi('In Progress',$c['In Progress']??0,'bi-arrow-repeat','info');kpi('Pending',$c['Pending']??0,'bi-clock','warning');?></div>
<div class="card"><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Field</th><th>Fertilizer</th><th>Date</th><th>Assigned</th><th>Priority</th><th>Status</th></tr></thead><tbody>
<?php foreach($t as $r) echo '<tr><td>'.e($r['fname']).'</td><td>'.e($r['fertilizer']).'</td><td>'.e($r['app_date']).'</td><td>'.e($r['uname']).'</td><td>'.badge($r['priority']).'</td><td>'.badge($r['status']).'</td></tr>';?></tbody></table></div></div>
<?php bottom();
