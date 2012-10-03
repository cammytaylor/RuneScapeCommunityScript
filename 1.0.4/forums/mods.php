<?php
require('../includes/config.php');
require('../structure/database.php');
require('../structure/user.php');
require('../structure/forum.php');

$database = new database($db_host, $db_name, $db_user, $db_password);
$user = new user($database);
$forum = new forum($database);

$user->updateLastActive();

$username = $user->getUsername($_COOKIE['user'], 2);
$rank = $user->getRank($username);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns:IE>
<head>
<meta http-equiv="Expires" content="0">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache">
<meta name="MSSmartTagsPreventParsing" content="TRUE">
<title><?php echo $data['wb_title']; ?></title>
<link href="../css/basic-3.css" rel="stylesheet" type="text/css" media="all" />
<link href="../css/forum-3.css" rel="stylesheet" type="text/css" media="all" />
<link href="../css/forummsg-1.css" rel="stylesheet" type="text/css" media="all" />
<link rel="shortcut icon" href="../img/favicon.ico" />
<!--[if IE 8]>
<link rel="stylesheet" type="text/css" href="../css/forummsg-ie-1.css" />
<![endif]-->
<?php include('../includes/google_analytics.html'); ?>
</head>
<body>
	<div id="body">
		<?php $forum->getNavBar($username, $rank); ?>
                <br /><br />
                
                <div style="text-align: center; background: none;">
                <div class="titleframe e">
                <b>Forum Moderators</b><br>
                <a href="../index.php" class=c>Main Menu</a> - <a href="index.php">Back to Forums</a>
                </div>
                </div>
                <br/>
                <br/>
                <div class="frame wide_e">
                <div style="text-align: justify">
                    <table cellspacing="4">
                    <?php
                        //retrieve moderators
                        $query = $database->processQuery("SELECT `username` FROM `users` WHERE `acc_status` = 3 ORDER BY `username` ASC", array(), true);
                        
                        if($database->getRowCount() == 0)
                        {
                            echo '<tr><td>There are currently no forum moderators.</td></tr>';
                        }
                        else
                        {
                            foreach($query as $mod)
                            {
                                echo '<tr><td><span style="padding-left:20px"><img src="../img/title2/icon_crown_green.gif">'. $mod['username'] .'</span></td></tr>';
                            }
                        }
                    ?>
                    </table>
                </div>
                </div>
                
				<div class="tandc"><?php echo $data['wb_foot']; ?></div>
		</div>

	</div>
</body>