<?php
// OptiFarm - configuration (edit DB credentials if needed)
session_start();
define('DB_HOST','localhost'); define('DB_NAME','optifarm_new'); define('DB_USER','root'); define('DB_PASS','');
define('ADMIN_CODE','AUG2026'); // "Admin Code Access" for adding/editing users

// Dynamic BASE_URL so the app works in any subfolder (e.g. /optifarm)
$root = str_replace('\\','/',__DIR__);
$doc  = rtrim(str_replace('\\','/',$_SERVER['DOCUMENT_ROOT']),'/');
define('BASE_URL', rtrim(str_replace($doc,'',$root),'/'));

try {
  $pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4',DB_USER,DB_PASS,
    [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
} catch (PDOException $e) { die('Database connection failed. Import database.sql and check config.php'); }

function q($s,$p=[]){ global $pdo; $st=$pdo->prepare($s); $st->execute($p); return $st; }
function rows($s,$p=[]){ return q($s,$p)->fetchAll(); }
function val($s,$p=[]){ return q($s,$p)->fetchColumn(); }
function e($v){ return htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8'); }
function flash($m=null,$t='success'){ if($m){$_SESSION['flash']=[$m,$t];return;} $f=$_SESSION['flash']??null; unset($_SESSION['flash']); return $f; }
function stock_status($r){ if($r['expiry']<date('Y-m-d'))return 'Expired'; if($r['qty']<=$r['min_qty'])return 'Low Stock'; return 'Good'; }
function badge($s){
  $c=['Good'=>'success','Completed'=>'success','Active'=>'success','Low Stock'=>'warning','In Progress'=>'info','Pending'=>'warning',
      'Expired'=>'danger','Inactive'=>'secondary','High'=>'danger','Medium'=>'warning','Low'=>'secondary'];
  return '<span class="badge text-bg-'.($c[$s]??'secondary').'">'.e($s).'</span>';
}
function csrf_field(){ if(empty($_SESSION['csrf']))$_SESSION['csrf']=bin2hex(random_bytes(16)); return '<input type="hidden" name="csrf" value="'.$_SESSION['csrf'].'">'; }
function alerts_for($u){
  $a=[];
  foreach(rows("SELECT * FROM inventory") as $r){ $s=stock_status($r);
    if($s=='Low Stock')$a[]=['Alerts','Low stock: '.$r['fertilizer'].' ('.$r['qty'].' '.$r['unit'].' left)','bi-exclamation-triangle text-warning'];
    if($s=='Expired')$a[]=['Alerts','Expired: '.$r['fertilizer'].' batch '.$r['batch'],'bi-x-octagon text-danger']; }
  $w=$u['role']=='staff'?" AND assigned_to=".(int)$u['id']:"";
  foreach(rows("SELECT t.*,f.name fname FROM tasks t JOIN fields f ON f.id=t.field_id WHERE t.status='Pending'$w ORDER BY app_date LIMIT 8") as $t)
    $a[]=['Alerts','Pending task: '.$t['fertilizer'].' at '.$t['fname'].' on '.$t['app_date'],'bi-clock text-primary'];
  foreach(rows("SELECT * FROM activity_log ORDER BY id DESC LIMIT 5") as $l) $a[]=['System',$l['action'].' - '.$l['created_at'],'bi-gear text-secondary'];
  return $a;
}
function log_act($msg){ q("INSERT INTO activity_log(user_id,action) VALUES(?,?)",[$_SESSION['user']['id']??null,$msg]); }
