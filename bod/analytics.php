<?php require __DIR__.'/../inc/auth.php'; require __DIR__.'/../inc/layout.php'; need_role('bod','admin');
// Simple predictive analytics: linear regression (least squares) on monthly production + average fertilizer usage
$p=rows("SELECT month_date d,SUM(tons) t FROM production GROUP BY month_date ORDER BY month_date"); $n=count($p);
$sx=$sy=$sxy=$sxx=0; foreach($p as $i=>$r){$sx+=$i;$sy+=$r['t'];$sxy+=$i*$r['t'];$sxx+=$i*$i;}
$den=max(1e-9,$n*$sxx-$sx*$sx); $m=($n*$sxy-$sx*$sy)/$den; $b=($sy-$m*$sx)/max(1,$n);
$labels=[];$actual=[];$fc=[];
foreach($p as $i=>$r){$labels[]=date('M Y',strtotime($r['d']));$actual[]=(float)$r['t'];$fc[]=null;}
$last=end($p)['d']; for($k=1;$k<=3;$k++){$labels[]=date('M Y',strtotime("$last +$k month"));$actual[]=null;$fc[]=round($m*($n-1+$k)+$b,1);}
$fc[$n-1]=$actual[$n-1];
$avg=val("SELECT AVG(stock_out) FROM (SELECT stock_out FROM stock_movements ORDER BY month_date DESC LIMIT 3) x");
$stock=val("SELECT SUM(qty) FROM inventory"); $months=$avg>0?round($stock/$avg,1):0;
top('Analytics Insights'); ?>
<div class="row g-3 mb-3"><?php kpi('Trend (MT / month)',($m>=0?'+':'').round($m,2),'bi-graph-up','success');kpi('Next Month Forecast',end($fc).' MT','bi-bullseye','primary');kpi('Avg Monthly Usage',round($avg).' kg','bi-droplet','warning');kpi('Supply Lasts (est.)',$months.' mo','bi-hourglass-split',$months<2?'danger':'info');?></div>
<div class="card mb-3"><div class="card-body"><h6>Production Forecast (3 months)</h6><div class="chart-box"><canvas id="c1"></canvas></div></div></div>
<div class="card"><div class="card-body"><h6>Insights</h6><ul class="mb-0">
<li>Production is <b><?=$m>=0?'trending upward':'trending downward'?></b> by about <?=abs(round($m,2))?> MT per month.</li>
<li>At the current usage rate (last 3 months), total fertilizer stock covers about <b><?=$months?> months</b>. <?=$months<2?'<span class="text-danger">Plan replenishment soon.</span>':''?></li>
<li>Forecast uses simple linear regression on historical monthly production; accuracy improves as more data is recorded.</li></ul></div></div>
<?php
chart_js('c1','line',$labels,[['label'=>'Actual (MT)','data'=>$actual,'borderColor'=>'#1f7a4d','tension'=>.3],['label'=>'Forecast (MT)','data'=>$fc,'borderColor'=>'#f2b94b','borderDash'=>[6,4],'tension'=>.3]]);
bottom();
