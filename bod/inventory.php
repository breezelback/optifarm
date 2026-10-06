<?php require __DIR__.'/../inc/auth.php'; require __DIR__.'/../inc/layout.php'; need_role('bod','admin');
$cat=$_GET['cat']??''; $inv=rows("SELECT * FROM inventory WHERE (?='' OR type=?) ORDER BY fertilizer",[$cat,$cat]);
if(isset($_GET['export'])){ header('Content-Type: text/csv'); header('Content-Disposition: attachment; filename="inventory.csv"'); $o=fopen('php://output','w');
  fputcsv($o,['Fertilizer','Type','Qty','Unit','Expiry','Batch','Status']); foreach($inv as $r) fputcsv($o,[$r['fertilizer'],$r['type'],$r['qty'],$r['unit'],$r['expiry'],$r['batch'],stock_status($r)]); exit; }
$st=['Good'=>0,'Low Stock'=>0,'Expired'=>0]; $val=0; foreach($inv as $r){$st[stock_status($r)]++; $val+=$r['qty']*$r['cost'];}
$bytype=rows("SELECT type,COUNT(*) c FROM inventory GROUP BY type");
top('Inventory Status'); ?>
<div class="row g-3 mb-3"><?php kpi('Total Inventory Value','₱'.number_format($val),'bi-cash-coin','primary');kpi('Total Items',count($inv),'bi-box-seam');kpi('Low Stock',$st['Low Stock'],'bi-exclamation-triangle','warning');kpi('Expired',$st['Expired'],'bi-x-octagon','danger');?></div>
<div class="row g-3 mb-3"><div class="col-md-6"><div class="card"><div class="card-body"><h6>Inventory by Category</h6><div class="chart-box"><canvas id="c1"></canvas></div></div></div></div>
<div class="col-md-6"><div class="card"><div class="card-body"><h6>Stock Status</h6><div class="chart-box"><canvas id="c2"></canvas></div></div></div></div></div>
<div class="d-flex gap-2 mb-3"><?php foreach([''=>'All','Chemical'=>'Chemical','Organic'=>'Organic','Foliar'=>'Foliar'] as $k=>$v) echo '<a class="btn btn-sm '.($cat==$k?'btn-success':'btn-outline-success').'" href="?cat='.$k.'">'.$v.'</a>';?>
<a class="btn btn-sm btn-primary ms-auto" href="?cat=<?=e($cat)?>&export=1"><i class="bi bi-download"></i> Export</a></div>
<div class="card"><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Fertilizer</th><th>Type</th><th>Qty</th><th>Expiry</th><th>Status</th></tr></thead><tbody>
<?php foreach($inv as $r) echo '<tr><td>'.e($r['fertilizer']).'</td><td>'.e($r['type']).'</td><td>'.e($r['qty'].' '.$r['unit']).'</td><td>'.e($r['expiry']).'</td><td>'.badge(stock_status($r)).'</td></tr>';?></tbody></table></div></div>
<?php
chart_js('c1','doughnut',array_column($bytype,'type'),[['data'=>array_map('intval',array_column($bytype,'c')),'backgroundColor'=>['#1f7a4d','#f2b94b','#3b82f6']]]);
chart_js('c2','doughnut',array_keys($st),[['data'=>array_values($st),'backgroundColor'=>['#1f7a4d','#f2b94b','#dc3545']]]);
bottom();
