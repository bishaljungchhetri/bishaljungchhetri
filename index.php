<?php 

define('BASEPATH',__dir__);

require_once BASEPATH."/vendor/autoload.php";

$app = new \System\Core\SystemInit;

$app->start();


