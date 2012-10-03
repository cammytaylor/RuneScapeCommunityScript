<?php
require('../includes/config.php');
require('../structure/database.php');
require('../structure/base.php');
require('../structure/user.php');
require('../structure/poll.php');

$database = new database($db_host, $db_name, $db_user, $db_password);
$base = new base($database);
$user = new user($database);
$poll = new poll($database);
$user->updateLastActive();

$username = $user->getUsername($_COOKIE['user'], 2);
$id = $_POST['id'];

if(!$poll->canVote($id, $username) || !$poll->optionExists($id, $_POST['option']))
    $base->redirect('index.php');
else
    $database->processQuery("INSERT INTO `votes` VALUES (null, ?, ?, ?)", array($_POST['option'], $id, $username), false);

$base->redirect('results.php?id='. $id);
?>