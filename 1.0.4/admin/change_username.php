<?php
require('../includes/config.php');
require('../structure/database.php');
require('../structure/base.php');
require('../structure/user.php');
require('../structure/user.register.php');

$database = new database($db_host, $db_name, $db_user, $db_password);
$base = new base($database);
$user = new user($database);
$user->updateLastActive();
$register = new user_register($database);

$username = $user->getUsername($_COOKIE['user'], 2);
$rank = $user->getRank($username);

if($rank < 4) $base->redirect('../index.php');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns:IE>
<head>
<meta http-equiv="Expires" content="0">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache">
<meta name="MSSmartTagsPreventParsing" content="TRUE">
<title><?php echo $data['wb_title']; ?></title>
<link href="../css/basic-3.css" rel="stylesheet" type="text/css" media="all">
<link rel="shortcut icon" href="../img/favicon.ico" />
<?php include('../includes/google_analytics.html'); ?>
<script type="text/javascript">
function goBack()
{
	window.history.back();
}	
</script>
</head>
	<div id="body">

			<div style="text-align: center; background: none;">
                        <div class="titleframe e">
                            <b>Administration</b><br> <a href="../index.php" class=c>Main Menu</a>
                        </div>
                        </div>

		<img class="widescroll-top" src="../img/scroll/backdrop_765_top.gif" alt="" width="765" height="50" />
		<div class="widescroll">
			<div class="widescroll-bgimg">

				<div class="widescroll-content">
                                        <?php
                                            if(!isset($_POST['old']) || !isset($_POST['new']))
                                            {
                                                ?>
                                    
                                                    <div id="blackfields">
                                                        <form action="change_username.php" method="POST">
                                                            <table>
                                                                <tr><td><b><font size="1">Abuse of this system will result in a demotion.</font></b></td></tr>
                                                                <tr><td>Username</td><td><input type="text" class="button" name="old" maxlength="12"></td></tr>
                                                                <tr><td>New Username</td><td><input type="text" class="button" name="new" maxlength="12"></td></tr>
                                                                <tr><td>Done?</td><td><input type="submit" value="Change Username"></td></tr>
                                                            </table>
                                                        </form>
                                                    </div>
                                    
                                                <?php
                                            }
                                            elseif(!$user->doesExist($_POST['old']))
                                            {
                                                echo 'You can\'t change the name of a non-existent user. <input type="button" value="Back" onclick="goBack()" />';
                                            }
                                            elseif($user->getRank($_POST['old']) > 1 && $user->getUserId($_COOKIE['user']) != 1)
                                            {
                                                echo 'You can\'t change the name of a staff member. <input type="button" value="Back" onclick="goBack()" />';
                                            }
                                            elseif(!$register->validateUsername($_POST['new']))
                                            {
                                                echo 'The newly created username cannot be used. <input type="button" value="Back" onclick="goBack()" />';
                                            }
                                            else
                                            {
                                                //replace all their content with new username
                                                $database->processQuery("UPDATE `users` SET `username` = ? WHERE `username` = ? LIMIT 1", array($_POST['new'], $_POST['old']), false);
                                                $database->processQuery("UPDATE `posts` SET `username` = ? WHERE `username` = ?", array($_POST['new'], $_POST['old']), false);
                                                $database->processQuery("UPDATE `threads` SET `username` = ? WHERE `username` = ?", array($_POST['new'], $_POST['old']), false);
                                                $database->processQuery("UPDATE `messages` SET `creator` = ? WHERE `creator` = ?", array($_POST['new'], $_POST['old']), false);
                                                $database->processQuery("UPDATE `messages` SET `receiver` = ? WHERE `receiver` = ?", array($_POST['new'], $_POST['old']), false);
                                                $database->processQuery("UPDATE `replies` SET `username` = ? WHERE `username` = ?", array($_POST['new'], $_POST['old']), false);
                                                
                                                $base->appendToFile('../forums/logs.txt', array($username .' changed '. $_POST['old'].'\'s username to '. $_POST['new']));
                                                echo 'The user\'s username has successfully been changed. (<b>'. $_POST['old'] .' -> '. $_POST['new'] .')';
                                            }
                                        ?>
				</div>
			</div>
		</div>
		<img class="widescroll-bottom" src="../img/scroll/backdrop_765_bottom.gif" alt="" width="765" height="50" />	

		<div class="tandc"><?php echo $data['wb_foot']; ?></div>
</body>
</html>
