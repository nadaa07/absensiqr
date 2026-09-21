<?php
declare(strict_types=1);
function e(?string $v):string{return htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8');}
function baseUrl():string{$script=str_replace('\\','/',dirname($_SERVER['SCRIPT_NAME']??'')); while(in_array(basename($script),['admin','dosen','mahasiswa','api'],true))$script=str_replace('\\','/',dirname($script)); return rtrim($script,'/');}
function redirect(string $url):never{header('Location: '.$url);exit;}
function flash(string $type,string $message):void{$_SESSION['flash']=['type'=>$type,'message'=>$message];}
function getFlash():?array{$f=$_SESSION['flash']??null;unset($_SESSION['flash']);return $f;}
function distanceMeters(float $lat1,float $lon1,float $lat2,float $lon2):float{$r=6371000;$p1=deg2rad($lat1);$p2=deg2rad($lat2);$dp=deg2rad($lat2-$lat1);$dl=deg2rad($lon2-$lon1);$a=sin($dp/2)**2+cos($p1)*cos($p2)*sin($dl/2)**2;return 2*$r*asin(min(1,sqrt($a)));}
function jsonResponse(array $data,int $code=200):never{http_response_code($code);header('Content-Type: application/json; charset=utf-8');echo json_encode($data,JSON_UNESCAPED_UNICODE);exit;}
