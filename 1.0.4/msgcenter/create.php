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

if(!$msgcenter->canCreate($username, $rank) || $user->checkMute($username))
{
    $content = 'You can\'t create a new conversation. <input type="button" class="button" value="Back" onclick="goBack()" />';
}
elseif(!isset($_POST['title']) || !isset($_POST['reply']))
{
    $content = '
    <form action="create.php" method="POST">
    <table>
    <tr><td>Title</td><td align="left"><input type="text" class="button" name="title" maxlength="50"></td></tr>';
    
    if($rank > 3) $content .= '<tr><td>Mass Message</td><td align="left"><input type="checkbox" name="mass_message" id="mass"></td></tr><tr><td>Receiver</td><td align="left"><input type="text" class="button" name="receiver" id="receiver" maxlength="12"></td></tr>';
    
    $content .= '
    <tr><td>Message</td><td><textarea name="reply" class="button" cols="45" rows="20" maxlength="2000"></textarea></td></tr>
    <tr><td></td><td align="left"><input type="submit" class="button" value="Create"></td></tr>
    </table>
    </form>';
}
elseif(strlen($_POST['reply']) > 2000 || strlen($_POST['title']) > 50)
{
    $content = 'Your reply cannot be greater than 2000 characters; your title cannot be greater than 50 characters. <input type="button" class="button" value="Back" onclick="goBack()" />';
}
elseif(strlen($_POST['reply']) == 0 || strlen($_POST['title']) == 0)
{
    $content = 'Either your message contents or title is empty. <input type="button" class="button" value="Back" onclick="goBack()" />';
}
else
{
    if($rank < 4)
        $receiver = '!';
    else
        $receiver = (isset($_POST['mass_message'])) ? '*' : $_POST['receiver']; 
    
    //verify the selected user exists
    $database->processQuery("SELECT * FROM `users` WHERE `username` = ?", array($receiver), false);

    if($database->getRowCount() == 0 && $receiver != '!' && $receiver != '*')
    {
        $content = 'The chosen user does not exist. <input type="button" class="button" value="Back" onclick="goBack()" />';
    }
    else
    {
        //create conversation
        $database->processQuery("INSERT INTO `messages` VALUES (null, ?, ?, ?, ?, ?, NOW(), '0', '0', ?, ?)", array($username, $receiver, $_POST['title'], nl2br($_POST['reply']), $_SERVER['REMOTE_ADDR'], $username, time()), false);
        $base->redirect('viewmessage.php?id='. $id);
    }
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
                                    <b>Create Conversation</b><br/><a href="index.php">Back to Message Centre</a>
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
<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript" src="../js/msgcenter_mass_click.js"></script>
</html>