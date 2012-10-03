<?php
/*
 * this file is for the threadmod.js's AJAX requests
 */

require('../../includes/config.php');
require('../../structure/database.php');
require('../../structure/base.php');
require('../../structure/forum.php');
require('../../structure/user.php');

$database = new database($db_host, $db_name, $db_user, $db_password);
$base = new base;
$user = new user($database);
$user->updateLastActive();
$username = $user->getUsername($_COOKIE['user'], 2);
$rank = $user->getRank($username);

if($rank > 2 && isset($_POST['id']) && isset($_POST['title']))
{
    //id of the thread we're editing
    $id = $_POST['id'];
    
    //currently only function we have, more soon
    if(isset($_POST['title']))
    {
        $database->processQuery("UPDATE `threads` SET `title` = ? WHERE `id` = ?", array($_POST['title'], $id), false);
        $base->appendToFile('../logs.txt', $username .' changed a thread\'s name to: '. $_POST['title']);
    }
}
?>
