<?php
require('../includes/config.php');
require('../structure/database.php');
require('../structure/base.php');
require('../structure/user.php');
require('../structure/msgcenter.php');

$database = new database($db_host, $db_name, $db_user, $db_password);
$base = new base($database);
$user = new user($database);
$msgcenter = new msgcenter($database);
$user->updateLastActive();


$username = $user->getUsername($_COOKIE['user'], 2);
$rank = $user->getRank($username);

if($rank < 4 || !$msgcenter->canView($_GET['id'], $username, $rank))
{
    $base->redirect('viewmessage.php?id='. $_GET['id']);
}
else
{
    $database->processQuery("DELETE FROM `replies` WHERE `conversation` = ?", array($_GET['id']), false);
    $database->processQuery("DELETE FROM `messages` WHERE `id` = ?", array($_GET['id']), false);
    $base->redirect('index.php');
}
?>