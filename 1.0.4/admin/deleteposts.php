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
                                            if(!isset($_POST['username']) || (!isset($_POST['threads']) && !isset($_POST['posts'])))
                                            {
                                                ?>
                                    
                                                    <div id="blackfields">
                                                        <form action="deleteposts.php" method="POST">
                                                            <table>
                                                                <tr><td>Username</td><td><input type="text" class="button" name="username" maxlength="12"></td></tr>
                                                                <tr><td>Delete posts</td><td><input type="checkbox" name="posts" value="1"></td></tr>
                                                                <tr><td>Delete threads</td><td><input type="checkbox" name="threads" value="1"></td></tr>
                                                                <tr><td>Done?</td><td><input type="submit" value="Delete!"></td></tr>
                                                            </table>
                                                        </form>
                                                    </div>
                                    
                                                <?php
                                            }
                                            else
                                            {
                                                $selected_user = $_POST['username'];
                                                
                                                if($user->getRank($selected_user) > 1)
                                                {
                                                    echo 'You can\'t delete posts by a fellow staff member. <input type="button" value="Back" onclick="goBack()" />';
                                                }
                                                else
                                                {
                                                    if(isset($_POST['posts'])) $database->processQuery("DELETE FROM `posts` WHERE `username` = ?", array($selected_user), false);
                                                    $affected = $database->getRowCount();
                                                    
                                                    if(isset($_POST['threads']))
                                                    {
                                                        $threads = $database->processQuery("SELECT `id` FROM `threads` WHERE `username` = ?", array($selected_user), true);
                                                        
                                                        //delete all posts in the threads the user mades
                                                        foreach($threads as $thread)
                                                        {
                                                            $database->processQuery("DELETE FROM `posts` WHERE `thread` = ?", array($thread['id']), false);
                                                            $affected += $database->getRowCount();
                                                        }
                                                        
                                                        //delete the thread now
                                                        $database->processQuery("DELETE FROM `threads` WHERE `username` = ?", array($selected_user), false);
                                                        $affected += $database->getRowCount();
                                                    }
                                                    
                                                    $affected += $database->getRowCount();
                                                    
                                                    if($affected == 0)
                                                    {
                                                        echo 'No posts or threads were deleted. Are you sure they exist?';
                                                    }
                                                    else
                                                    {
                                                        $base->appendToFile('../forums/logs.txt', array($username .' deleted posts and/or threads by'. $selected_user));
                                                        echo $affected .' posts/threads deleted.';
                                                    }
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
