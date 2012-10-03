<?php
require('../includes/config.php');
require('../structure/database.php');
require('../structure/base.php');

$database = new database($db_host, $db_name, $db_user, $db_password);
$base = new base;

if(isset($_POST['qfc']))
{
    $thread = $database->processQuery("SELECT `id`,`parent` FROM `threads` WHERE `qfc` = ? LIMIT 1", array($_POST['qfc']), true);

    if($database->getRowCount() >= 1) $base->redirect('viewthread.php?forum='. $thread[0]['parent'] .'&id='. $thread[0]['id']);
}

$base->redirect('index.php');
?>