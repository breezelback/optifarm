<?php require __DIR__.'/inc/auth.php'; require __DIR__.'/inc/layout.php'; need_role();
$tab=$_GET['tab']??'All'; $all=alerts_for($_SESSION['user']);
$list=array_filter($all,fn($a)=>$tab=='All'||$a[0]==$tab);
top('Notifications'); ?>
<ul class="nav nav-pills mb-3"><?php foreach(['All','Alerts','System'] as $t) echo '<li class="nav-item"><a class="nav-link '.($tab==$t?'active bg-success':'text-success').'" href="?tab='.$t.'">'.$t.'</a></li>';?></ul>
<div class="card"><ul class="list-group list-group-flush">
<?php foreach($list as $a):?><li class="list-group-item d-flex gap-3 align-items-center py-3"><i class="bi <?=$a[2]?> fs-4"></i><div><?=e($a[1])?><div class="small text-muted"><?=e($a[0])?></div></div></li><?php endforeach;
if(!$list) echo '<li class="list-group-item text-muted">No notifications.</li>';?></ul></div>
<?php bottom();
