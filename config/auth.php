<?php
declare(strict_types=1);
if(session_status()===PHP_SESSION_NONE){session_start();}
date_default_timezone_set('Asia/Jakarta');
function isLoggedIn():bool{return isset($_SESSION['user_id'],$_SESSION['role']);}
function userId():?int{return isset($_SESSION['user_id'])?(int)$_SESSION['user_id']:null;}
function userRole():?string{return $_SESSION['role']??null;}
function loginUser(int $id,string $role):void{session_regenerate_id(true);$_SESSION['user_id']=$id;$_SESSION['role']=$role;}
function logoutUser():void{$_SESSION=[];if(ini_get('session.use_cookies')){$p=session_get_cookie_params();setcookie(session_name(),' ',time()-42000,$p['path'],$p['domain'],$p['secure'],$p['httponly']);}session_destroy();}
function requireLogin():void{if(!isLoggedIn()){header('Location: ../login.php');exit;}}
function requireRole(string $role):void{requireLogin();if(userRole()!==$role){http_response_code(403);exit('Akses ditolak.');}}
function csrfToken():string{if(empty($_SESSION['csrf']))$_SESSION['csrf']=bin2hex(random_bytes(32));return $_SESSION['csrf'];}
function checkCsrf(?string $token):bool{return $token&&isset($_SESSION['csrf'])&&hash_equals($_SESSION['csrf'],$token);}
