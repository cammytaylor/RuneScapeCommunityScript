<?php
require('../../includes/config.php');
require('../../structure/database.php');
require('../../structure/forum.php');
require('../../structure/forum.post.php');
require('../../structure/base.php');
require('../../structure/user.php');

$database = new database($db_host, $db_name, $db_user, $db_password);
$post = new post($database);
$base = new base($database);
$user = new user($database);
$user->updateLastActive();
$username = $user->getUsername($_COOKIE['user'], 2);
$rank = $user->getRank($username);

//take action then log it
if($rank > 2) $post->deletePost($_GET['pid'], $rank); $base->appendToFile('../logs.txt', array($username.' deleted the post '. $_GET['pid']));

$base->redirect('../viewthread.php?forum='. $_GET['forum'] .'&id='. $_GET['id']);
?>