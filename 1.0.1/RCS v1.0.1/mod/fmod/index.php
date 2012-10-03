<?php
require('../../includes/config.php');
require('../../structure/database.php');
require('../../structure/base.php');
require('../../structure/user.php');

$database = new database($db_host, $db_name, $db_user, $db_password);
$base = new base($database);
$user = new user($database);
$user->updateLastActive();

$username = $user->getUsername($_COOKIE['user'], 2);
$rank = $user->getRank($username);

if($rank < 3) $base->redirect('../../index.php');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns:IE>
<head>
<meta http-equiv="Expires" content="0">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache">
<meta name="MSSmartTagsPreventParsing" content="TRUE">
<title><?php echo $data['wb_title']; ?></title>
<link href="../../css/basic-3.css" rel="stylesheet" type="text/css" media="all">
<link href="../../css/admin.css" rel="stylesheet" type="text/css" media="all">
<link rel="shortcut icon" href="../../img/favicon.ico" />
<?php include('../../includes/google_analytics.html'); ?>
</head>
	<div id="body">

			<div style="text-align: center; background: none;">
                        <div class="titleframe e">
                            <b>Moderation</b><br> <a href="../../index.php" class=c>Main Menu</a>
                        </div>
                        </div>

		<img class="widescroll-top" src="../../img/scroll/backdrop_765_top.gif" alt="" width="765" height="50" />
		<div class="widescroll">
			<div class="widescroll-bgimg">

				<div class="widescroll-content">
                                        <div class="optthree" id="options">
                                            <div class="option_title" id="options_title">
                                                <b>Options</b>
                                            </div>
                                            <div class="option" id="options-1">
                                                <b>View User</b><br/>
                                                View a user's information.<br/>
                                                <a href="userinformation.php" style="color:black;">GO</a>
                                            </div>
                                            
                                            <div class="option" id="options-2">
                                                <b>Mute User</b><br/>
                                                Mute a user.<br/>
                                                <a href="mute.php" style="color:black;">GO</a>
                                            </div>
                                            
                                            <div class="option" id="options-3">
                                                <b>Alt Check</b><br/>
                                                Check if a user has alts.<br/>
                                                <a href="altcheck.php" style="color:black;">GO</a>
                                            </div>
                                            
                                            <div class="option" id="options-4">
                                                <b>Recent Posts</b><br/>
                                                View recent posts.<br/>
                                                <a href="recentposts.php" style="color:black;">GO</a>
                                            </div>
                                        </div>
                                </div>
			</div>
		</div>
		<img class="widescroll-bottom" src="../../img/scroll/backdrop_765_bottom.gif" alt="" width="765" height="50" />	

		<div class="tandc"><?php echo $data['wb_foot']; ?></div>
                <script src="../../js/jquery.js"></script>
                <script src="../../js/modindex.js"></script>
</body>
</html>
