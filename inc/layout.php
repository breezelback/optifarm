<?php
function top($title){
  $u=$_SESSION['user']; $b=BASE_URL;
  $menus=[
   'admin'=>[['Dashboard','admin/dashboard.php','bi-speedometer2'],['Users','admin/users.php','bi-people'],['Inventory','admin/inventory.php','bi-box-seam'],['Tasks','admin/tasks.php','bi-calendar-check'],['Reports','admin/reports.php','bi-file-earmark-text'],['Notifications','notifications.php','bi-bell'],['Settings','settings.php','bi-gear']],
   'bod'=>[['Executive Dashboard','bod/dashboard.php','bi-graph-up-arrow'],['Inventory Status','bod/inventory.php','bi-box-seam'],['Tasks','bod/tasks.php','bi-calendar-check'],['Analytics Insights','bod/analytics.php','bi-lightbulb'],['Notifications','notifications.php','bi-bell'],['Settings','settings.php','bi-gear']],
   'staff'=>[['Dashboard','staff/dashboard.php','bi-speedometer2'],['My Tasks','staff/tasks.php','bi-calendar-check'],['Field Reports','staff/reports.php','bi-clipboard-data'],['Notifications','notifications.php','bi-bell'],['Settings','settings.php','bi-gear']]];
  $f=flash();
  echo '<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>'.e($title).' | OptiFarm</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="'.$b.'/assets/style.css" rel="stylesheet"></head><body>
  <nav class="sidebar" id="sidebar"><div class="brand"><i class="bi bi-flower2"></i> OptiFarm</div><ul class="nav flex-column">';
  foreach($menus[$u['role']] as $m){ $on=(substr($_SERVER['SCRIPT_NAME'],-strlen($m[1]))===$m[1])?' active':'';
    echo '<li><a class="nav-link'.$on.'" href="'.$b.'/'.$m[1].'"><i class="bi '.$m[2].'"></i> '.$m[0].'</a></li>'; }
  echo '</ul><a class="nav-link logout" href="'.$b.'/logout.php"><i class="bi bi-box-arrow-left"></i> Log Out</a></nav>
  <main class="content"><div class="topbar"><button class="btn btn-light d-lg-none" onclick="document.getElementById(\'sidebar\').classList.toggle(\'show\')"><i class="bi bi-list"></i></button>
  <h4 class="m-0">'.e($title).'</h4><div class="ms-auto d-flex align-items-center gap-3"><a href="'.$b.'/notifications.php" class="text-dark fs-5"><i class="bi bi-bell"></i></a>
  <span class="small text-muted text-end"><b>'.e($u['name']).'</b><br>'.strtoupper(e($u['role'])).'</span></div></div>';
  if($f) echo '<div class="alert alert-'.$f[1].' alert-dismissible fade show">'.e($f[0]).'<button class="btn-close" data-bs-dismiss="alert"></button></div>';
}
function bottom(){
  echo '</main><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>'.($GLOBALS['page_js']??'').'</body></html>';
}
function kpi($label,$value,$icon,$color='success'){
  echo '<div class="col-6 col-xl-3"><div class="card kpi h-100"><div class="card-body d-flex justify-content-between align-items-center">
  <div><div class="text-muted small">'.e($label).'</div><div class="fs-3 fw-bold">'.e($value).'</div></div>
  <div class="icon bg-'.$color.'-subtle text-'.$color.'"><i class="bi '.$icon.'"></i></div></div></div></div>';
}
function chart_js($id,$type,$labels,$sets,$opts='{}'){
  $GLOBALS['page_js']=($GLOBALS['page_js']??'').'<script>new Chart(document.getElementById('.json_encode($id).'),{type:'.json_encode($type).',data:{labels:'.json_encode($labels).',datasets:'.json_encode($sets).'},options:Object.assign({responsive:true,maintainAspectRatio:false},'.$opts.')});</script>';
}
