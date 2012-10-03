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
<style>
#contain_content{
width:650px;
}
    
#posts {
width:80%;
text-align:left;
}

#threads {
width:80%;
text-align:left;
}
</style>
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
                                            $posts = $database->processQuery("SELECT `thread`,`username`,`id` FROM `posts` ORDER BY `id` DESC LIMIT 10", array(), true);
                                            $threads = $database->processQuery("SELECT `id`,`title`,`username`,`parent` FROM `threads` ORDER BY `id` DESC LIMIT 10", array(), true);
                                            
                                            ?> 
                                    
                                            <div id="contain_content">
                                                <div id="posts">
                                                    <h2>Recent Posts</h2>
                                                    <br/><br/>
                                                    <?php
                                                        foreach($posts as $post)
                                                        {
                                                            $thread = $database->processQuery("SELECT `title`,`parent` FROM `threads` WHERE `id` = ?", array($post['thread']), true);

                                                            ?>

                                                                <a href="userinformation.php?username=<?php echo $post['username']; ?>"><?php echo $post['username']; ?></a>&nbsp;
                                                                <a href="../../forums/viewthread.php?forum=<?php echo $thread[0]['parent']; ?>&id=<?php echo $post['thread']; ?>&goto=<?php echo $post['id']; ?>">posted</a>&nbsp;
                                                                in the thread <a href="../../forums/viewthread.php?forum=<?php echo $thread[0]['parent']; ?>&id=<?php echo $post['thread']; ?>&goto=start"><?php echo $thread[0]['title']; ?></a>
                                                                <br/><br/>
                                                            <?php
                                                        }
                                                    ?>
                                                </div>

                                                <div id="threads">
                                                    <h2>Recent Threads</h2>
                                                    <br/><br/>
                                                    <?php
                                                        foreach($threads as $thread)
                                                        {
                                                            ?>

                                                                <a href="userinformation.php?username=<?php echo $thread['username']; ?>"><?php echo $thread['username']; ?></a> created the thread&nbsp;
                                                                <a href="../../forums/viewthread.php?forum=<?php echo $thread['parent']; ?>&id=<?php echo $thread['id']; ?>&goto=start"><?php echo $thread['title']; ?></a>
                                                                <br/><br/>
                                                            <?php
                                                        }
                                                    ?>
                                                </div>
                                            </div>
				</div>
			</div>
		</div>
		<img class="widescroll-bottom" src="../../img/scroll/backdrop_765_bottom.gif" alt="" width="765" height="50" />	

		<div class="tandc"><?php echo $data['wb_foot']; ?></div>
</body>
</html>
