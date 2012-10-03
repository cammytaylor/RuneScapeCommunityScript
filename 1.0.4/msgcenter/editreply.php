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

$username = $user->getUsername($_COOKIE['user'], 2);
$rank = $user->getRank($username);
$id = $_GET['id'];

if(!$user->isLoggedIn()) $base->redirect('../index.php');
if($rank < 4) $base->redirect('viewmessage.php?id='. $_GET['convo']);
$user->updateLastActive();


if(!$msgcenter->canView($_GET['convo'], $username, $rank))
{
    $content = 'You can\'t edit a reply to a non-existent message. <input type="button" class="button" value="Back" onclick="goBack()" />';
}
elseif(!isset($_POST['content']))
{
    //get current data
    $data = $database->processQuery("SELECT `content` FROM `replies` WHERE `id` = ? LIMIT 1", array($id), true);
    
    $content = '
    <form action="editreply.php?id='. $id .'&convo='. $_GET['convo'] .'" method="POST">
    <table>
    <tr><td>Message</td><td><textarea name="content" cols="45" rows="20" class="button" maxlength="2000">'. htmlentities($base->remBr(stripslashes($data[0]['content']))) .'</textarea></td></tr>
    <tr><td>Done?</td><td><input type="submit" class="button" value="Update Message"></td></tr>
    </table>
    </form>';
}
elseif(strlen($_POST['content']) > 2000)
{
    $content = 'Your reply cannot be greater than 2000 characters.';
}
else
{
    //update message
    $database->processQuery("UPDATE `replies` SET `content` = ? WHERE `id` = ? LIMIT 1", array(nl2br($_POST['content']), $id), false);
    
    $base->redirect('viewmessage.php?id='. $_GET['convo']);
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
                                    <b>Edit Message</b><br/><a href="viewmessage.php?id=<?php echo $id; ?>">Back to Message Exchange</a>
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