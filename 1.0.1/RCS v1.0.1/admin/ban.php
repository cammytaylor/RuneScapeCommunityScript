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
                                            if(!isset($_POST['username']))
                                            {
                                                ?>
                                    
                                                    <div id="blackfields">
                                                        <form action="ban.php" method="POST">
                                                            <table>
                                                                <tr><td>Username</td><td><input type="text" class="button" name="username" maxlength="12"></td></tr>
                                                                <tr><td>Delete all their posts & threads</td><td><input type="checkbox" name="d_posts" value="1"></td></tr>
                                                                <tr><td>Delete all messages they created</td><td><input type="checkbox" name="d_messages" value="1"></td></tr>
                                                                <tr><td>Delete all accounts linked to their IP</td><td><input type="checkbox" name="d_accounts" value="1"></td></tr>
                                                                <tr><td>Ban IP</td><td><input type="checkbox" name="banip" value="1"></td></tr>
                                                                <tr><td>Done?</td><td><input type="submit" value="Banish!"></td></tr>
                                                            </table>
                                                        </form>
                                                    </div>
                                    
                                                <?php
                                            }
                                            else
                                            {
                                                $selected_user = $_POST['username'];
                                                $ip = $user->getUserIp($selected_user);
                                                
                                                if(!$user->doesExist($selected_user))
                                                {
                                                    echo 'No user exists with this username. <input type="button" value="Back" onclick="goBack()" />';
                                                }
                                                elseif($user->getRank($selected_user) > 1)
                                                {
                                                    echo 'You can\'t ban a fellow staff member. <input type="button" value="Back" onclick="goBack()" />';
                                                }
                                                else
                                                {
                                                    //carry out all the operations
                                                    ?> <ul> <?php
                                                    
                                                    $user->ban($selected_user);
                                                    $base->appendToFile('../forums/logs.txt', array($username. ' banned the user'. $selected_user));
                                                    
                                                    echo '<li><b>'. $selected_user .'</b> has been banned.</li>';
                                                    
                                                    //delete all posts and thread
                                                    if(isset($_POST['d_posts']))
                                                    {
                                                        $database->processQuery("DELETE FROM `posts` WHERE `username` = ?", array($selected_user), false);
                                                        echo '<li>Posts deleted.</li>';
                                                        
                                                        //delete their threads and all posts in that thread
                                                        $threads = $database->processQuery("SELECT `id` FROM `threads` WHERE `username` = ?", array($selected_user), true);
                                                        
                                                        //delete all posts in the threads the user mades
                                                        foreach($threads as $thread)
                                                        {
                                                            $database->processQuery("DELETE FROM `posts` WHERE `thread` = ?", array($thread['id']), false);
                                                        }
                                                        
                                                        //delete the thread now
                                                        $database->processQuery("DELETE FROM `threads` WHERE `username` = ?", array($selected_user), false);
                                                        
                                                        echo '<li>Threads deleted.</li>';
                                                    }
                                                    
                                                    //delete all messages created by them
                                                    if(isset($_POST['d_messages']))
                                                    {
                                                        $database->processQuery("DELETE FROM `messages` WHERE `creator` = ?", array($selected_user), false);
                                                        echo '<li>Messaged deleted.</li>';
                                                    }
                                                    
                                                    //delete all accounts linked to their IP
                                                    if(isset($_POST['d_accounts']))
                                                    {
                                                        $database->processQuery("DELETE FROM `users` WHERE `ip` = ?", array($ip), false);
                                                        echo '<li>All accounts deleted.</li>';
                                                    }
                                                    
                                                    if(isset($_POST['banip'])) 
                                                    {
                                                        $database->processQuery("INSERT INTO `banned_ips` VALUES (null, ?)", array($ip), false);
                                                        echo '<li>IP has been banned.</li>';
                                                    }
                                                    
                                                    ?> </ul> <?php
                                                }
                                            }
                                        ?>
				</div>
			</div>
		</div>
		<img class="widescroll-bottom" src="../img/scroll/backdrop_765_bottom.gif" alt="" width="765" height="50" />	

		<div class="tandc"><?php echo $data['wb_foot']; ?></div>
</body>
</html>
