<?php require __DIR__.'/../inc/auth.php'; require __DIR__.'/../inc/layout.php'; need_role('bod','admin');
$prod=rows("SELECT DATE_FORMAT(month_date,'%b') m,SUM(tons) t FROM production GROUP BY month_date ORDER BY month_date");
$tot=array_sum(array_column($prod,'t')); $n=count($prod);
$growth=$n>1?round(($prod[$n-1]['t']-$prod[$n-2]['t'])/$prod[$n-2]['t']*100,1):0;
$done=val("SELECT COUNT(*) FROM tasks WHERE status='Completed'"); $all=max(1,val("SELECT COUNT(*) FROM tasks"));
$value=val("SELECT COALESCE(SUM(qty*cost),0) FROM inventory"); $staff=val("SELECT COUNT(*) FROM users WHERE role='staff' AND status='Active'");
$fs=rows("SELECT status,COUNT(*) c FROM fields GROUP BY status");
$top=rows("SELECT f.name,f.crop,SUM(p.tons) t FROM production p JOIN fields f ON f.id=p.field_id GROUP BY f.id ORDER BY t DESC LIMIT 3");
$inv=rows("SELECT * FROM inventory"); $okc=count(array_filter($inv,fn($r)=>stock_status($r)=='Good'));
top('Executive Dashboard'); ?>
<div class="row g-3 mb-3"><?php kpi('Total Production',number_format($tot).' MT','bi-basket');kpi('Monthly Growth',($growth>=0?'+':'').$growth.'%','bi-graph-up-arrow','primary');
kpi('Task Completion',round($done/$all*100).'%','bi-check2-circle');kpi('Active Staff',$staff,'bi-people','info');
kpi('Inventory Value','₱'.number_format($value),'bi-cash-coin','warning');kpi('Stock Health',round($okc/max(1,count($inv))*100).'%','bi-shield-check');?></div>
<div class="row g-3 mb-3"><div class="col-lg-8"><div class="card"><div class="card-body"><h6>Production Overview (MT per month)</h6><div class="chart-box"><canvas id="c1"></canvas></div></div></div></div>
<div class="col-lg-4"><div class="card"><div class="card-body"><h6>Farm Status</h6><div class="chart-box"><canvas id="c2"></canvas></div></div></div></div></div>
<div class="row g-3"><div class="col-lg-6"><div class="card"><div class="card-body"><h6>Top Performing Fields</h6><ol class="mb-0">
<?php foreach($top as $t) echo '<li class="py-1"><b>'.e($t['name']).'</b> <span class="text-muted">('.e($t['crop']).')</span> — '.e($t['t']).' MT</li>';?></ol></div></div></div>
<div class="col-lg-6"><div class="card"><div class="card-body"><h6>Recent Alerts</h6>
<?php foreach(array_slice(array_filter(alerts_for($_SESSION['user']),fn($a)=>$a[0]=='Alerts'),0,5) as $a) echo '<div class="py-1"><i class="bi '.$a[2].'"></i> '.e($a[1]).'</div>';?></div></div></div></div>
<?php
chart_js('c1','line',array_column($prod,'m'),[['label'=>'Production (MT)','data'=>array_map('floatval',array_column($prod,'t')),'borderColor'=>'#1f7a4d','backgroundColor'=>'rgba(31,122,77,.15)','fill'=>true,'tension'=>.3]]);
chart_js('c2','doughnut',array_column($fs,'status'),[['data'=>array_map('intval',array_column($fs,'c')),'backgroundColor'=>['#1f7a4d','#f2b94b','#9ca3af']]]);
bottom();
