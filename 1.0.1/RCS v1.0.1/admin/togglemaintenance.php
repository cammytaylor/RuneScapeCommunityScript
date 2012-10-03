<?php
require('../includes/config.php');
require('../structure/database.php');
require('../structure/forum.php');
require('../structure/forum.thread.php');
require('../structure/base.php');
require('../structure/user.php');

$database = new database($db_host, $db_name, $db_user, $db_password);
$thread = new thread($database);
$base = new base($database);
$user = new user($database);
$user->updateLastActive();

//useful variables
$rank = $user->getRank($user->getUsername($_COOKIE['user'], 2));

if($rank < 4) $base->redirect('../index.php');

//toggle the maintenance status of the site
$m = $database->processQuery("SELECT `maintenance` FROM `config`", array(), true);
$database->processQuery("UPDATE `config` SET `maintenance` = ?", array(($m[0]['maintenance'] == 0) ? 1 : 0), false);

$redirect = 'http://www.Asgarniax.org/admin/index.php';
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
<link href="../css/basic-3.css" rel="stylesheet" type="text/css" media="all">
<link rel="shortcut icon" href="img/favicon.ico" />
<title><?php echo $data['wb_title']; ?></title>
</head>
<body>

<div id="body">
    <div style="text-align: center; background: none;">
    <div class="titleframe e">
    <b>Administration</b>
    </div>
    </div>
<br>
<br>
<div class="frame wide_e">
<div style="text-align: justify">
<center>
<br/>

You have successfully set the maintenance mode to <?php echo ($m[0]['maintenance'] == 1) ? 'off' : 'on'; ?>
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