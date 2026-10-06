<?php require __DIR__.'/../inc/auth.php'; require __DIR__.'/../inc/layout.php'; need_role('admin');
if($_SERVER['REQUEST_METHOD']==='POST'){
  $p=$_POST;
  q("INSERT INTO inventory(fertilizer,type,qty,unit,min_qty,expiry,batch,cost,supplier) VALUES(?,?,?,?,?,?,?,?,?)",
    [trim($p['fertilizer']),$p['type'],$p['qty'],$p['unit'],$p['min_qty']?:50,$p['expiry'],trim($p['batch']),$p['cost']?:0,trim($p['supplier'])]);
  q("INSERT INTO stock_movements(month_date,stock_in) VALUES(DATE_FORMAT(CURDATE(),'%Y-%m-01'),?) ON DUPLICATE KEY UPDATE stock_in=stock_in+VALUES(stock_in)",[$p['qty']]);
  log_act('Stock added: '.$p['fertilizer'].' '.$p['qty'].$p['unit']); flash('Stock added.'); header('Location: inventory.php'); exit;
}
$inv=rows("SELECT * FROM inventory ORDER BY fertilizer");
$c=['Good'=>0,'Low Stock'=>0,'Expired'=>0]; $val=0; foreach($inv as $r){$c[stock_status($r)]++; $val+=$r['qty']*$r['cost'];}
top('Inventory Management'); ?>
<div class="d-flex justify-content-end mb-3"><button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#stockM"><i class="bi bi-plus-lg"></i> Add Stock</button></div>
<div class="row g-3 mb-3"><?php kpi('Total Items',count($inv),'bi-box-seam');kpi('Low Stock',$c['Low Stock'],'bi-exclamation-triangle','warning');kpi('Expired',$c['Expired'],'bi-x-octagon','danger');kpi('Total Value','₱'.number_format($val,2),'bi-cash-coin','primary');?></div>
<div class="card"><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Fertilizer</th><th>Type</th><th>Quantity</th><th>Expiry</th><th>Batch</th><th>Supplier</th><th>Status</th></tr></thead><tbody>
<?php foreach($inv as $r):?><tr><td><?=e($r['fertilizer'])?></td><td><?=e($r['type'])?></td><td><?=e($r['qty'].' '.$r['unit'])?></td><td><?=e($r['expiry'])?></td><td><?=e($r['batch'])?></td><td><?=e($r['supplier'])?></td><td><?=badge(stock_status($r))?></td></tr><?php endforeach;?></tbody></table></div></div>
<div class="modal fade" id="stockM"><div class="modal-dialog modal-lg"><form method="post" class="modal-content"><?=csrf_field()?><div class="modal-header"><h5>Add Stock</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
<div class="modal-body row g-3">
<div class="col-md-6"><label class="form-label">Fertilizer</label><input name="fertilizer" list="fl" class="form-control" required><datalist id="fl"><option>NPK 14-14-14<option>Urea 46-0-0<option>Phosphate<option>Organic Compost<option>Foliar Fertilizer</datalist></div>
<div class="col-md-6"><label class="form-label">Type</label><select name="type" class="form-select"><option>Chemical<option>Organic<option>Foliar</select></div>
<div class="col-md-4"><label class="form-label">Quantity</label><input type="number" step="0.01" min="0" name="qty" class="form-control" required></div>
<div class="col-md-4"><label class="form-label">Unit</label><select name="unit" class="form-select"><option>kg<option>L<option>sack</select></div>
<div class="col-md-4"><label class="form-label">Min. Stock Level</label><input type="number" name="min_qty" class="form-control" value="50"></div>
<div class="col-md-4"><label class="form-label">Expiry Date</label><input type="date" name="expiry" class="form-control" required></div>
<div class="col-md-4"><label class="form-label">Batch Number</label><input name="batch" class="form-control" required></div>
<div class="col-md-4"><label class="form-label">Cost per Unit (optional)</label><input type="number" step="0.01" name="cost" class="form-control"></div>
<div class="col-12"><label class="form-label">Supplier (optional)</label><input name="supplier" class="form-control"></div></div>
<div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary">Save Changes</button></div></form></div></div>
<?php bottom();
