<?php
require('../includes/config.php');
require('../structure/database.php');
require('../structure/base.php');
require('../structure/user.php');
require('../structure/forum.php');

$database = new database($db_host, $db_name, $db_user, $db_password);
$base = new base($database);
$user = new user($database);
$forum = new forum($database);
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
<link href="../css/admin.css" rel="stylesheet" type="text/css" media="all">
<link rel="shortcut icon" href="../img/favicon.ico" />
<?php include('../includes/google_analytics.html'); ?>
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
                                            if(!isset($_GET['check_version'])){
                                                echo 'You\'re running version '. $version .'; <a href="?check_version=1">Check if there\'s a new version.</a>';
                                            }else{
                                                $ch = curl_init('http://rcscript.comlu.com/getLatest.php');
                                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                                
                                                if(($latest = curl_exec($ch)) === false){
                                                  echo 'An error occured: '. curl_error($ch);
                                                }else{
                                                    //000webhost includes a default html code which can mess up the comparison,
                                                    //so we want JUST the value of the current version returned
                                                    $latest = substr($latest, 0, (strpos($latest, '-')));
                                                    
                                                    if((string)$version == (string)$latest){
                                                        echo 'You\'re running the latest version.';
                                                    }else{
                                                        echo 'You\'re outdated! <a href="http://rcscript.comlu.com/">Download the new version, '. $latest .'</a>';
                                                    }
                                                }
                                                curl_close($ch);
                                            }
                                        ?>
                                        <hr>
                                        <br/>
                                        <br/>
                                        <div class="optthree" id="users">
                                            <div class="option_title" id="users_title">
                                                <b>Users</b>
                                            </div>
                                            <div class="option" id="users-1">
                                                <b>Ban/Unban a User</b><br/>
                                                Issue a ban against a user.<br/>
                                                <a href="ban.php" style="color:black;">GO</a>
                                            </div>
                                            
                                            <div class="option" id="users-2">
                                                <b>Lookup User</b><br/>
                                                View Info/Promote/Demote/Etc<br/>
                                                <a href="userinformation.php" style="color:black;">GO</a>
                                            </div>
                                            
                                            <div class="option" id="users-3">
                                                <b>Username Change</b><br/>
                                                Change a user's username.<br/>
                                                <a href="change_username.php" style="color:black;">GO</a>
                                            </div>
                                            
                                            <div class="option" id="users-3">
                                                <b>User List</b><br/>
                                                View the user list.<br/>
                                                <a href="users.php" style="color:black;">GO</a>
                                            </div>
                                        </div>
                                        
                                        <div class="optthree" id="site">
                                            <div class="option_title" id="site_title">
                                                <b>Site</b>
                                            </div>
                                            
                                            <div class="option" id="site-1">
                                                <b>Add Poll</b><br/>
                                                Add a new poll!<br/>
                                                <a href="add_poll.php" style="color:black;">GO</a>
                                            </div>
                                            
                                            <div class="option" id="site-2">
                                                <b>Maintenance</b><br/>
                                                Toggle the site maintenance mode.<br/>
                                                <a href="togglemaintenance.php" style="color:black;">GO</a>
                                            </div>
                                            
                                            <div class="option" id="site-3">
                                                <b>Daily Pictures</b><br/>
                                                Change the daily picture.<br/>
                                                <a href="changepicture.php" style="color:black;">GO</a>
                                            </div>
                                            
                                            <div class="option" id="site-4">
                                                <b>Add News</b><br/>
                                                Add news post<br/>
                                                <a href="add_news.php" style="color:black;">GO</a>
                                            </div>
                                            
                                            <div class="option" id="site-5">
                                                <b>Add Story</b><br/>
                                                Add a story<br/>
                                                <a href="addstory.php" style="color:black;">GO</a>
                                            </div>
                                            
                                            <div class="option" id="site-6">
                                                <b>Add Poll</b><br/>
                                                Add a poll<br/>
                                                <a href="add_poll.php" style="color:black;">GO</a>
                                            </div>
                                            
                                            <div class="option" id="site-6">
                                                <b>Site Settings</b><br/>
                                                Edit your site's settings<br/>
                                                <a href="settings.php" style="color:black;">GO</a>
                                            </div>
                                        </div>
                                        
                                        <div class="optthree" id="forum">
                                            <div class="option_title" id="forum_title">
                                                <b>Forum</b>
                                            </div>
                                            
                                            <div class="option" id="forum-1">
                                                <b>Delete Posts</b><br/>
                                                Delete posts/threads by a user<br/>
                                                <a href="deleteposts.php" style="color:black;">GO</a>
                                            </div>
                                            
                                            <div class="option" id="forum-2">
                                                <b>Add Forum</b><br/>
                                                Add a new forum<br/>
                                                <a href="addforum.php" style="color:black;">GO</a>
                                            </div>
                                            
                                            <div class="option" id="forum-3">
                                                <b>Add Category</b><br/>
                                                Add a category<br/>
                                                <a href="addcat.php" style="color:black;">GO</a>
                                            </div>
											
                                            <div class="option" id="forum-4">
                                                <b>Edit Category</b><br/>
                                                Edit/delete a category<br/>
                                                <a href="editcat.php" style="color:black;">GO</a>
                                            </div>
                                            
                                            <div class="option" id="forum-4">
                                                <b>Edit a Forum</b><br/>
                                                Edit/delete a forum<br/>
                                                <a href="editforum.php" style="color:black;">GO</a>
                                            </div>
                                        </div>
                                    
                                        <div class="optthree" id="stats">
                                            <div class="option_title" id="stats_title">
                                                <b>Stats</b>
                                            </div>
                                            
                                            <div class="option" id="stats-1">
                                                <b>Threads</b><br/>
                                                <?php echo $forum->threadCount(); ?><br/>
                                            </div>
                                            
                                            <div class="option" id="stats-2">
                                                <b>Posts</b><br/>
                                                <?php echo $forum->postCount(); ?><br/>
                                            </div>
                                            
                                            <div class="option" id="stats-3">
                                                <b>Users</b><br/>
                                                <?php echo $base->userCount(); ?><br/>
                                            </div>
                                        </div>
				</div>
			</div>
		</div>
		<img class="widescroll-bottom" src="../img/scroll/backdrop_765_bottom.gif" alt="" width="765" height="50" />	

		<div class="tandc"><?php echo $data['wb_foot']; ?></div>
</body>
</html>
