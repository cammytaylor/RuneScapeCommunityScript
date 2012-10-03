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
                                                        <form action="mute.php" method="POST">
                                                            <tr><td>Username:</td><td><input type="text" name="username" <?php echo (isset($_GET['target'])) ? 'value="'.$_GET['target'].'"' : ''; ?> maxlength="12"></td></tr>
                                                            <tr><td><b><font color="red">MARK TO UNMUTE USER</font></b></td><td><input type="checkbox" name="unmute"></td></tr>
                                                            <tr>
                                                                <td>Mute Length</td>
                                                                <td>
                                                                    <select name="mute">
                                                                        <option value="4">4</option>
                                                                        <option value="8">8</option>
                                                                        <option value="16">16</option>
                                                                        <option value="32">32</option>
                                                                        <option value="48">48</option>
                                                                    </select>
                                                                    hours
                                                                </td>
                                                            </tr>
                                                            <tr><td>Mute</td><td><input type="submit" value="Mute"></td></tr>
                                                        </form>
                                                    </table>
                                    
                                                <?php
                                            }
                                            elseif(!$user->doesExist($_POST['username']))
                                            {
                                                echo 'Sorry, no user exists with that username. <input type="button" class="button" value="Back" onclick="goBack()" />';
                                            }
                                            elseif(!in_array($_POST['mute'], array(4,8,16,32,48)))
                                            {
                                                echo 'You can\'t mute for an "undefined" length. <input type="button" class="button" value="Back" onclick="goBack()" />';
                                            }
                                            else
                                            {
                                                $muted = ($user->checkMute($_POST['username'])) ? true : false;
                                                
                                                if(isset($_POST['unmute']))
                                                {
                                                    if(!$muted)
                                                    {
                                                        echo 'You can\'t unmute someone who isn\'t muted! <input type="button" class="button" value="Back" onclick="goBack()" />';
                                                    }
                                                    else
                                                    {
                                                        $user->unmute($_POST['username']);
                                                        
                                                        echo '<b>'. $_POST['username'].'</b> has been unmuted. <a href="index.php">Back to MCP</a> | <a href="mute.php">Unmute/Mute Another User</a>';
                                                    }
                                                }
                                                else
                                                {
                                                    if($user->getRank($_POST['username']) > 1)
                                                    {
                                                        echo 'Only members can be muted, not fellow staff! <input type="button" class="button" value="Back" onclick="goBack()" />';
                                                    }
                                                    elseif($muted)
                                                    {
                                                        echo 'This user is already muted! <input type="button" class="button" value="Back" onclick="goBack()" />';
                                                    }
                                                    else
                                                    {
                                                        //add the mute
                                                        $user->mute($_POST['username'], $_POST['mute']);

                                                        //success! user muted
                                                        ?>
                                                            <b><?php echo $_POST['username']; ?></b> was muted for <b><?php echo $_POST['mute']; ?></b> hours. <a href="index.php">Back to MCP</a> | <a href="mute.php">Unmute/Mute Another User</a></b>
                                                        <?php
                                                    }
                                                }
                                            }
                                        ?>
				</div>
			</div>
		</div>
		<img class="widescroll-bottom" src="../../img/scroll/backdrop_765_bottom.gif" alt="" width="765" height="50" />	

		<div class="tandc"><?php echo $data['wb_foot']; ?></div>
</body>
</html>
