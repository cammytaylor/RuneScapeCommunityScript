 <?php
require('../includes/config.php');
require('../structure/database.php');
require('../structure/base.php');
require('../structure/user.php');

$database = new database($db_host, $db_name, $db_user, $db_password);
$base = new base($database);
$user = new user($database);
$user->updateLastActive();

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
                            <b>Administration</b><br> <a href="../index.php" class=c>Main Menu</a> - <a href="index.php">Home</a>
                        </div>
                        </div>

		<img class="widescroll-top" src="../img/scroll/backdrop_765_top.gif" alt="" width="765" height="50" />
		<div class="widescroll">
			<div class="widescroll-bgimg">
				<div class="widescroll-content">
                                        <?php
                                            $s_user = $_GET['username'];
                                        
                                            if(!isset($_GET['username']))
                                            {
                                                ?>
                                    
                                                    <table>
                                                        <form action="userinformation.php" method="GET">
                                                            <tr><td>Username:</td><td><input type="text" name="username" maxlength="12"> <input type="submit" value="Lookup"></td></tr>
                                                        </form>
                                                    </table>
                                    
                                                <?php
                                            }
                                            elseif(!$user->doesExist($s_user))
                                            {
                                                echo 'Sorry, no user exists with that username. <input type="button" class="button" value="Back" onclick="goBack()" />';
                                            }
                                            elseif($_GET['change_rank']){
                                                $s_rank = $user->getRank($s_user);
                                                if($s_rank == 0){
                                                    echo 'You can\'t promote/demote a banned user.';
                                                }elseif(!isset($_GET['promote']) && !isset($_GET['demote'])){
                                                    if($s_rank == 1){
                                                        echo '<a href="userinformation.php?username='. $s_user .'&change_rank=1&promote=1">Promote</a>';
                                                    }elseif($s_rank == 4){
                                                        echo '<a href="userinformation.php?username='. $s_user .'&change_rank=1&demote=1">Demote</a>';
                                                    }else{
                                                        echo '<a href="userinformation.php?username='. $s_user .'&change_rank=1&promote=1">Promote</a> | <a href="userinformation.php?username='. $s_user .'&change_rank=1&demote=1">Demote</a>';
                                                    }
                                                }elseif(isset($_GET['promote']) && isset($_GET['demote'])){
                                                    echo 'You can\'t promote and demote a user at the same time.';
                                                }else{
                                                    $back = '<a href="?username='. $s_user .'">Back</a>';
                                                    if(isset($_GET['promote']) && $s_rank >= 1 && $s_rank < 4){ 
                                                        $database->processQuery("UPDATE `users` SET `acc_status` = `acc_status` + 1 WHERE `username` = ? LIMIT 1", array($s_user), false);
                                                        
                                                        switch(($s_rank+1)){
                                                            case 2:
                                                                echo 'User promoted to player moderator. '. $back;
                                                                break;
                                                            case 3:
                                                                echo 'User promoted to forum moderator. '. $back;
                                                                break;
                                                            case 4:
                                                                echo 'User promoted to administrator. '. $back;
                                                                break;
                                                        }
                                                    }elseif(isset($_GET['demote']) && $s_rank > 1 && $s_rank <= 4){
                                                        $database->processQuery("UPDATE `users` SET `acc_status` = `acc_status` - 1 WHERE `username` = ? LIMIT 1", array($s_user), false);
                                                        
                                                        switch(($s_rank-1)){
                                                            case 1:
                                                                echo 'User demoted to member. '. $back;
                                                                break;
                                                            case 2:
                                                                echo 'User demoted to player moderator. '. $back;
                                                                break;
                                                            case 3:
                                                                echo 'User demoted to forum moderator. '. $back;
                                                                break;
                                                        }
                                                    }else{
                                                        echo 'You can\'t do this. '. $back;
                                                    }
                                                }
                                            }
                                            else
                                            {
                                                
                                                //let's extract the user's information
                                                $info = $database->processQuery("SELECT `id`,`reg_date`,`acc_status`,`ip`,`lastlogin`,`lastpost`,`lastip` FROM `users` WHERE `username` = ?", array($s_user), true);
                                                
                                                //retrieve message information
                                                $MandR = $user->getMandR($s_user);
                                                $MandR = explode(':', $MandR);
                                                ?>
                                                    <center><input type="button" class="button" value="Back" onclick="goBack()" /></center>
                                    
                                                    <table>
                                                        <tr><td>Username</td><td><?php echo $user->dName($s_user, $info[0]['acc_status']); ?></td><td><a href="?username=<?php echo $s_user; ?>&change_rank=1">Promote/Demote</a></td></tr>
                                                        <tr><td>ID</td><td><?php echo $info[0]['id']; ?></td></tr>
                                                        <tr><td>IP</td><td><?php echo $info[0]['ip']; ?></td></tr>
                                                        <tr><td>Latest IP</td><td><?php echo $info[0]['lastip']; ?></td></tr>
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
                                                        <tr><td>Messages Created</td><td><?php echo $MandR[0]; ?></td></tr>
                                                        <tr><td>Message Replies</td><td><?php echo $MandR[1]; ?></td></tr>
                                                        <tr><td>Last Login</td><td><?php echo $base->seconds_to_time(time()-$info[0]['lastlogin']); ?></td></tr>
                                                        <tr><td>Last Post</td><td><?php echo $base->seconds_to_time(time()-$info[0]['lastpost']); ?></td></tr>
                                                    </table>
                                    
                                                <?php
                                            }
                                        ?>
				</div>
			</div>
		</div>
		<img class="widescroll-bottom" src="../img/scroll/backdrop_765_bottom.gif" alt="" width="765" height="50" />	

		<div class="tandc"><?php echo $data['wb_foot']; ?></div>
</body>
</html>
