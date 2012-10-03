<?php
require('../includes/config.php');
require('../structure/database.php');
require('../structure/forum.php');
require('../structure/forum.thread.php');
require('../structure/base.php');
require('../structure/user.php');

$database = new database($db_host, $db_name, $db_user, $db_password);
$base = new base($database);
$user = new user($database);
$forum = new forum($database);
$thread_obj = new thread($database);

$user->updateLastActive();

//make sure required data is in the correct format AND they're logged in
if(!$user->isLoggedIn() || !ctype_digit($_REQUEST['forum']) || !ctype_digit($_REQUEST['id']) || !ctype_digit($_REQUEST['type']) || (!ctype_digit($_REQUEST['pid']) && $_REQUEST['type'] == 1)) $base->redirect('index.php');

//set some variables that are used a lot throughout the page
$username = $user->getUsername($_COOKIE['user'], 2);
$rank = $user->getRank($username);
$f = $_REQUEST['forum'];
$thread = $_REQUEST['id'];

//instead of typing it a million times, we're going to set our redirect url
$redirect = 'viewthread.php?forum='. $f .'&id='. $thread;

//make sure they are posting in a forum where they have permission
if($user->checkMute($username) || !$thread_obj->canView($thread, $username, $rank) || !$thread_obj->canReply($thread, $rank)) $base->redirect($redirect);

//extract content for the set type
$data = ($_REQUEST['type'] == 1) ? $database->processQuery("SELECT `content`,`username`,`status` FROM `posts` WHERE `id` = ?", array($_REQUEST['pid']), true) : $database->processQuery("SELECT `username`,`content`,`status`,`title` FROM `threads` WHERE `id` = ?", array($thread), true);

//check if they have permission to edit the thread/post
if($rank < 3 && ($data[0]['username'] != $username || $data[0]['status'] == 1)) $base->redirect($redirect);

$type = $_POST['type'];
                            
if($user->getRank($data[0]['username']) > 3 && $rank < 4)
{
    $content = '<div class="frame e">You can\'t edit an administrator\'s post.</div>';
}
elseif(isset($_POST['message']))
{
    //send them to their newly editted post
    $url = ($type == 1) ? 'viewthread.php?forum='. $f .'&id='. $thread.'&goto='. $_POST['pid'] : 'viewthread.php?forum='. $f .'&id='. $thread.'&goto=start';

    if(isset($_POST['cancel'])) $base->redirect($url);

    if(strlen($_POST['message']) > 2000 && $rank < 3)
    {
        $content = '<div class="frame e">Your post can\'t be larger than 2000 characters.</div>';
    }
    else
    {
        //message var
        $message = nl2br($_POST['message']);

        //set the title accordingly
        if($type == 2) $title = ($rank > 2) ? substr($_POST['title'], 0, 30) : $data[0]['title'];
        
        //update post or thread
        ($type == 1) ? $database->processQuery("UPDATE `posts` SET `content` = ?, `lastedit` = CONCAT('$username@', NOW()) WHERE `id` = ? LIMIT 1", array($message, $_POST['pid']), false) : $database->processQuery("UPDATE `threads` SET `content` = ?, `title` = ?, `lastedit` = CONCAT('$username@', NOW()) WHERE `id` = ? LIMIT 1", array($message, $title, $_POST['id']), false);
        
        $base->redirect($url);
    }
}
else
{
    $chars = ($rank > 2) ? $chars = null : $chars = 2000;
    $title_length = ($rank > 3) ? 50 : 30;

    $content = '
    <div id="nocontrols" class="phold"></div>
    <div id="command">
    <form method="post" action="edit.php">
    <input type="hidden" name="id" value="'. $thread .'">';

    if($_GET['type'] == 1) $content .= '<input type="hidden" name="pid" value="'. $_GET['pid'] .'">';

    $content .= '<input type="hidden" name="forum" value="'. $f .'">
    <input type="hidden" name="type" value="'. $_GET['type'] .'">
    <table>';

    if($rank > 2 && $_REQUEST['type'] == 2)
    {
        $content .= '<tr>
            <td class="commandtitle">Thread Title:</td>
            <td class="commandinput"><input size="40" maxlength="'. $title_length .'" id="charlimit_text_b" type="text" class="textinput" name="title" value="'. htmlentities(stripslashes($data[0]['title'])) .'"/>
            </td>
            </tr>'; 
    }

    $content .= '<tr>
            <td class="commandtwo" colspan="2">You have <span id="charlimit_count_b">'. $title_length .'</span> characters <span id="charlimit_info_b" style="display: none">remaining</span> for your title.</td>
    </tr>
    <tr>
            <td class="commandtwo" colspan="2">
                <textarea id="charlimit_text_a" name="message" rows="20" cols="60">'. htmlentities(stripslashes($base->remBr($data[0]['content']))) .'</textarea><br />
            You have <span id="charlimit_count_a"><?php echo $chars; ?></span> characters <span id="charlimit_info_a" style="display: none">remaining</span> for your message.</td>
    </tr>
    <tr>
    <td class="commandtwo" colspan="2"><br />
            <input type="submit" name="add" value="Edit" /> &nbsp; &nbsp;
            <!--<input type="submit" name="preview" value="Preview" /> &nbsp; &nbsp;-->
            <input type="submit" name="cancel" value="Cancel" /> &nbsp; &nbsp;
    </td>
    </tr>
    </table>
    </form>
    </div>

    <div id="smileylegend">
    <span class="title">Smileys: </span><br>
    <span id="smilytxt" style="display: hidden;">Click to add a smiley to your message (will overwrite selected text).</span><br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <span onclick="addsmiley(\':)\')"><IMG class=sm0 alt=":)" title=":)" src="../img/forum/smileys/smile.gif"> :)</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <span onclick="addsmiley(\';)\')"><IMG class=sm1 alt=";)" title=";)" src="../img/forum/smileys/wink.gif"> ;)</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <span onclick="addsmiley(\':P\')"><IMG class=sm2 alt=":P" title=":P" src="../img/forum/smileys/tongue.gif"> :P</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <span onclick="addsmiley(\':(\')"><IMG class=sm3 alt=":(" title=":(" src="../img/forum/smileys/sad.gif"> :(</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <span onclick="addsmiley(\':|\')"><IMG class=sm4 alt=":|" title=":|" src="../img/forum/smileys/nosmile.gif"> :|</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <span onclick="addsmiley(\'O_o\')"><IMG class=sm5 alt="O_o" title="O_o" src="../img/forum/smileys/o.O.gif"> O_o</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <span onclick="addsmiley(\':D\')"><IMG class=sm6 alt=":D" title=":D" src="../img/forum/smileys/bigsmile.gif"> :D</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <span onclick="addsmiley(\'^^\')"><IMG class=sm7 alt="^^" title="^^" src="../img/forum/smileys/^^.gif"> ^^</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <span onclick="addsmiley(\':O\')"><IMG class=sm8 alt=":O" title=":O" src="../img/forum/smileys/shocked.gif"> :O</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <span onclick="addsmiley(\':@\')"><IMG class=sm9 alt=":@" title=":@" src="../img/forum/smileys/angry.gif"> :@</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    </div>';
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
<link href="../css/basic-3.css" rel="stylesheet" type="text/css" media="all" />
<link href="../css/forum-3.css" rel="stylesheet" type="text/css" media="all" />
<link href="../css/forummsg-1.css" rel="stylesheet" type="text/css" media="all" />
<link rel="shortcut icon" href="../img/favicon.ico" />
<!--[if IE 8]>
<link rel="stylesheet" type="text/css" href="../css/forummsg-ie-1.css" />
<![endif]-->
<?php
include('../js/v_thread_1.html');
include('../js/v_thread_2.html');
include('../includes/google_analytics.html');
?>
</head>
<body>
	<div id="body">
		<?php $forum->getNavBar($username, $rank); ?>
                <br /><br />

		<div style="text-align: center; background: none;">
			<div id="infopane">
				<div class="about">

					<ul class="flat">
						<li><a href="viewthread.php?forum=<?php echo $f; ?>&id=<?php echo $thread; ?>">Return to thread</a></li>
					</ul>

				</div>
			</div>
                    
                        <?php echo $content; ?>
                    
			<br />
				<div class="tandc"><?php echo $data['wb_foot']; ?></div>
		</div>

	</div>
</body>