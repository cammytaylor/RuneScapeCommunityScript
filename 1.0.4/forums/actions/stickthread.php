<?php
require('../../includes/config.php');
require('../../structure/database.php');
require('../../structure/forum.php');
require('../../structure/forum.thread.php');
require('../../structure/base.php');
require('../../structure/user.php');

$database = new database($db_host, $db_name, $db_user, $db_password);
$thread = new thread($database);
$base = new base($database);
$user = new user($database);
$user->updateLastActive();

//useful variables
$username = $user->getUsername($_COOKIE['user'], 2);
$rank = $user->getRank($username);
$id = $_GET['id'];

//take action then log it
if($thread->checkExistence($id) && $thread->canView($id, $username, $rank) && $rank > 2)
{
    $thread_info = $database->processQuery("SELECT `sticky` FROM `threads` WHERE `id` = ?", array($id), true);
    $database->processQuery("UPDATE `threads` SET `sticky` = ? WHERE `id` = ?", array(($thread_info[0]['sticky'] == 1) ? 0 : 1, $id), false);
    
    $base->appendToFile('../logs.txt', array($username.' stuck/unstuck the thread '. $id));
}
else
{
    $base->redirect('../viewthread.php?forum='. $_GET['forum'] .'&id='. $id .'&goto=start');
}

$redirect = 'http://'.$path.'forums/viewthread.php?forum='. $_GET['forum'] .'&id='. $id.'&goto=start';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns:IE>

<!-- LeeStrong Runescape Website Source --!>
<!-- Added by HTTrack --><meta http-equiv="content-type" content="text/html;charset=ISO-8859-1"><!-- /Added by HTTrack -->
<head>
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache">
<meta name="MSSmartTagsPreventParsing" content="TRUE">
<meta HTTP-EQUIV="REFRESH" content="3; url=<?php echo $redirect; ?>">
<link href="../../css/basic-3.css" rel="stylesheet" type="text/css" media="all">
<link rel="shortcut icon" href="img/favicon.ico" />
<title><?php echo $data['wb_title']; ?></title>
</head>
<body>

<div id="body">
    <div style="text-align: center; background: none;">
    <div class="titleframe e">
    <b>Moderator Actions</b>
    </div>
    </div>
<br>
<br>
<div class="frame wide_e">
<div style="text-align: justify">
<center>
<br/>

You have successfully stuck/unstuck the thread!
<br/>
<br/>
<a href="<?php echo $redirect; ?>">Click here if you haven't been redirected after 3 seconds...</a>

<br/>
<br/>
</center>
</div>
</div>
<br>

<div class="tandc"><?php echo $data['wb_foot']; ?></div>

</div>

</body>
<meta http-equiv="content-type" content="text/html;charset=ISO-8859-1"<!-- /Added by HTTrack -->
</html>