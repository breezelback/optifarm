<?php require __DIR__.'/../inc/auth.php'; require __DIR__.'/../inc/layout.php'; need_role('admin');
$users=val("SELECT COUNT(*) FROM users WHERE status='Active'");
$stock=val("SELECT COALESCE(SUM(qty),0) FROM inventory");
$pending=val("SELECT COUNT(*) FROM tasks WHERE status='Pending'");
$inv=rows("SELECT * FROM inventory"); $low=array_filter($inv,fn($r)=>stock_status($r)!='Good');
$mv=rows("SELECT DATE_FORMAT(month_date,'%b') m,stock_in,stock_out FROM stock_movements ORDER BY month_date");
$dist=rows("SELECT type,SUM(qty) q FROM inventory GROUP BY type");
$recent=rows("SELECT t.*,f.name fname FROM tasks t JOIN fields f ON f.id=t.field_id ORDER BY t.id DESC LIMIT 5");
top('Admin Dashboard'); ?>
<div class="row g-3 mb-3"><?php kpi('Active Users',$users,'bi-people');kpi('Total Fertilizer Stock',number_format($stock).' kg','bi-box-seam','primary');kpi('Pending Tasks',$pending,'bi-clock','warning');kpi('Low Stock Alerts',count($low),'bi-exclamation-triangle','danger');?></div>
<div class="row g-3 mb-3">
 <div class="col-lg-8"><div class="card"><div class="card-body"><h6>Inventory Movement</h6><div class="chart-box"><canvas id="c1"></canvas></div></div></div></div>
 <div class="col-lg-4"><div class="card"><div class="card-body"><h6>Fertilizer Distribution</h6><div class="chart-box"><canvas id="c2"></canvas></div></div></div></div></div>
<div class="row g-3">
 <div class="col-lg-7"><div class="card"><div class="card-body"><h6>Recent Applications</h6><table class="table align-middle mb-0"><thead><tr><th>Field</th><th>Fertilizer</th><th>Date</th><th>Status</th></tr></thead><tbody>
 <?php foreach($recent as $r):?><tr><td><?=e($r['fname'])?></td><td><?=e($r['fertilizer'])?></td><td><?=e($r['app_date'])?></td><td><?=badge($r['status'])?></td></tr><?php endforeach;?></tbody></table></div></div></div>
 <div class="col-lg-5"><div class="card"><div class="card-body"><h6>Low Stock Alerts</h6>
 <?php foreach($low as $r): $p=min(100,round($r['qty']/max($r['min_qty'],1)*100));?>
 <div class="mb-3"><div class="d-flex justify-content-between small"><span><i class="bi bi-exclamation-triangle text-warning"></i> <?=e($r['fertilizer'])?></span><span><?=e($r['qty'].' / min '.$r['min_qty'].' '.$r['unit'])?></span></div>
 <div class="progress" style="height:8px"><div class="progress-bar bg-<?=stock_status($r)=='Expired'?'danger':'warning'?>" style="width:<?=$p?>%"></div></div></div>
 <?php endforeach; if(!$low) echo '<div class="text-muted">All stock levels are healthy.</div>';?></div></div></div></div>
<?php
chart_js('c1','bar',array_column($mv,'m'),[['label'=>'Stock In','data'=>array_map('floatval',array_column($mv,'stock_in')),'backgroundColor'=>'#1f7a4d'],['label'=>'Stock Out (Usage)','data'=>array_map('floatval',array_column($mv,'stock_out')),'backgroundColor'=>'#f2b94b']]);
chart_js('c2','doughnut',array_column($dist,'type'),[['data'=>array_map('floatval',array_column($dist,'q')),'backgroundColor'=>['#1f7a4d','#f2b94b','#3b82f6','#9ca3af']]]);
bottom();
