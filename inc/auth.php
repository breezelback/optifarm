<?php
require __DIR__.'/../config.php';
if($_SERVER['REQUEST_METHOD']==='POST'){
  if(empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'],$_POST['csrf']??'')) die('Invalid request (CSRF).');
}
function need_role(...$roles){
  if(empty($_SESSION['user'])){ header('Location: '.BASE_URL.'/login.php'); exit; }
  if($roles && !in_array($_SESSION['user']['role'],$roles)){ http_response_code(403); die('Access denied for your role.'); }
}
function home_for($role){ return BASE_URL.'/'.($role=='admin'?'admin':($role=='bod'?'bod':'staff')).'/dashboard.php'; }
