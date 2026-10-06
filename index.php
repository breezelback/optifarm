<?php require __DIR__.'/inc/auth.php';
header('Location: '.(empty($_SESSION['user'])?BASE_URL.'/login.php':home_for($_SESSION['user']['role']))); exit;
