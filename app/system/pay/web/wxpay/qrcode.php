<?php
error_reporting(E_ERROR);
require_once '../../../include/class/phpqrcode/phpqrcode.php';
$url = urldecode($_GET["data"]);
//QRcode::png($url);