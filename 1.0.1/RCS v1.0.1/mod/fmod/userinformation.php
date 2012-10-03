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

if(isset($_GET['username'])) $_POST['username'] = $_GET['username'];
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
<link rel="shortcut icon" href="../../img/favicon.ico" />
<?php include('../../includes/google_analytics.html'); ?>
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
                            <b>Moderation</b><br> <a href="../../index.php" class=c>Main Menu</a>  - <a href="index.php">Back</a>
                        </div>
                        </div>

		<img class="widescroll-top" src="../../img/scroll/backdrop_765_top.gif" alt="" width="765" height="50" />
		<div class="widescroll">
			<div class="widescroll-bgimg">

				<div class="widescroll-content">
                                        <?php
                                            if(!isset($_POST['username']))
                                            {
                                                ?>
                                    
                                                    <table>
                                                        <form action="userinformation.php" method="POST">
                                                            <tr><td>Username:</td><td><input type="text" name="username" maxlength="12"> <input type="submit" value="Lookup"></td></tr>
                                                        </form>
                                                    </table>
                                    
                                                <?php
                                            }
                                            elseif(!$user->doesExist($_POST['username']))
                                            {
                                                echo 'Sorry, no user exists with that username. <input type="button" class="button" value="Back" onclick="goBack()" />';
                                            }
                                            else
                                            {
                                                $s_user = $_POST['username'];
                                                
                                                //let's extract the user's information
                                                $info = $database->processQuery("SELECT `id`,`reg_date`,`acc_status`,`ip`,`lastlogin`,`lastpost` FROM `users` WHERE `username` = ?", array($s_user), true);
                                                
                                                //retrieve message information
                                                $MandR = $user->getMandR($s_user);
                                                $MandR = explode(':', $MandR);
                                                ?>
                                                    <center><input type="button" class="button" value="Back" onclick="goBack()" /></center>
                                    
                                                    <table>
                                                        <tr><td>Username</td><td><?php echo $s_user; ?></td></tr>
                                                        <tr><td>ID</td><td><?php echo $info[0]['id']; ?></td></tr>
                                                        <tr><td>Reg. Date</td><td><?php echo $info[0]['reg_date']; ?></td></tr>
                                                        <tr><td>Posts</td><td><?php echo $user->postcount($s_user); ?></td></tr>
                                                        <tr>
                                                            <td>Muted</td>
                                                            <td>
                                                                <?php 
                                                                    $check = $user->checkMute($s_user);
                                                                    echo ($check) ? 'Yes ('. $check .' hours left)' : 'No'; 
                                                               ?>
                                                            </td>
                                                        </tr>
                                                        <tr><td>Last Login</td><td><?php echo $base->seconds_to_time(time()-$info[0]['lastlogin']); ?></td></tr>
                                                        <tr><td>Last Post</td><td><?php echo $base->seconds_to_time(time()-$info[0]['lastpost']); ?></td></tr>
                                                    </table>
                                    
                                                <?php
                                            }
                                        ?>
				</div>
			</div>
		</div>
		<img class="widescroll-bottom" src="../../img/scroll/backdrop_765_bottom.gif" alt="" width="765" height="50" />	

		<div class="tandc"><?php echo $data['wb_foot']; ?></div>
</body>
</html>
