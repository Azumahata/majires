<?php
// error_reporting(E_ALL);
define('TITLE' ,'マジレス（｀・ω・´）きぼんぬ');
define('DB_HOST',   'localhost');
define('DB_NAME',   'majires');
define('DB_USER',   'root');
define('DB_PASSWD', '');

define('DB_CONNECT_STRING' ,'mysql:host='.DB_HOST.';dbname='.DB_NAME);

$sessionFlg = session_start();

function getPDO() {
  return new PDO(DB_CONNECT_STRING, DB_USER, DB_PASSWD);
}

function redirect($location, $params) {
  header('Location: ' . $location . '?' . http_build_query($params), true, 301);
  exit;
}
function getRoomLocation($id) {
  return './room.php?' . http_build_query(array('room_id' => $id));
}

function errorRedirect() {
  header('Location: ' . './index.php?error=true', true, 301);
  exit;
}

