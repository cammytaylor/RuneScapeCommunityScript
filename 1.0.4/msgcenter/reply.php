<?php
require('../includes/config.php');
require('../structure/database.php');
require('../structure/base.php');
require('../structure/user.php');
require('../structure/msgcenter.php');

$database = new database($db_host, $db_name, $db_user, $db_password);
$base = new base($database);
$msgcenter = new msgcenter($database);
$user = new user($database);

if(!$user->isLoggedIn()) $base->redirect('../index.php');
$user->updateLastActive();

$username = $user->getUsername($_COOKIE['user'], 2);
$rank = $user->getRank($username);
$id = $_GET['id'];

if(!$msgcenter->canReply($id, $username, $rank))
{
    $content = 'You can\'t reply to this conversation. Some possible reasons is that you\'re attempting to post a reply when no response has been made, the conversation has been marked as solved, or this is a mass message (can\'t reply to mass messages). <input type="button" class="button" value="Back" onclick="goBack()" />';
}
elseif(!isset($_POST['reply']))
{
    $content = '
    <form action="reply.php?id='. $id .'" method="POST">
    <textarea name="reply" cols="45" rows="20" class="button" maxlength="2000"></textarea><br/>
    <input type="submit" class="button" value="Reply">
    </form>';
}
elseif(strlen($_POST['reply']) > 2000)
{
    $content = 'Your reply cannot be greater than 2000 characters.';
}
elseif(strlen($_POST['reply']) == 0)
{
    $content = 'Your reply cannot be empty.';
}
else
{
    //retrieve some details
    $data = $database->processQuery("SELECT `creator` FROM `messages` WHERE `id` = ?", array($id), true);
    $opened = ($data[0]['creator'] == $username) ? 0 : 1;
    
    //insert reply
    $database->processQuery("INSERT INTO `replies` VALUES (null, ?, ?, ?, ?, NOW())", array($username, $id, nl2br($_POST['reply']), $_SERVER['REMOTE_ADDR']), false);
    
    //update lastreply field and opened field
    $database->processQuery("UPDATE `messages` SET `lastreply` = ?, `opened` = ? WHERE `id` = ? LIMIT 1", array($username, $opened, $id), false);
    
    $base->redirect('viewmessage.php?id='. $id);
}
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
<link href="../css/msgcenter.css" rel="stylesheet" type="text/css" media="all">
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
                            <div class="header">
                                <div class="msg_titleframe">
                                    <b>Reply to PM</b><br/><a href="index.php">Back to Message Centre</a>
                                </div>
                            </div>
                        </div>

		<img class="widescroll-top" src="../img/scroll/backdrop_765_top.gif" alt="" width="765" height="50" />
		<div class="widescroll">
			<div class="widescroll-bgimg">

				<div class="widescroll-content" style="text-align:center;">
                                    <div id="black_fields">
                                        <?php
                                            echo $content;
                                        ?>
                                    </div>
				</div>
			</div>
		</div>
		<img class="widescroll-bottom" src="../img/scroll/backdrop_765_bottom.gif" alt="" width="765" height="50" />	

		<div class="tandc"><?php echo $data['wb_foot']; ?></div>
</body>
</html>