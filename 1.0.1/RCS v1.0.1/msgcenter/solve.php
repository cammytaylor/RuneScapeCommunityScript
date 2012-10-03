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
    $msgcenter->toggleSolve($_GET['id']);
    $base->redirect('viewmessage.php?id='. $_GET['id']);
}
?>