<?php
require('../structure/base.php');
require('../includes/config.php');
require('../structure/database.php');
require('../structure/forum.php');
require('../structure/forum.thread.php');
require('../structure/user.php');

$database = new database($db_host, $db_name, $db_user, $db_password);
$base = new base($database);
$user = new user($database);
$forum = new forum($database);
$thread_obj = new thread($database);

//make sure the user is logged in and the required data is set
if(!$user->isLoggedIn() || !ctype_digit($_REQUEST['forum']) || !ctype_digit($_REQUEST['id'])) $base->redirect('index.php');

//set some variables that are used a lot throughout the page
$username = $user->getUsername($_COOKIE['user'], 2);
$rank = $user->getRank($username);
$f = $_REQUEST['forum'];
$thread = $_REQUEST['id'];

//make sure they are posting in a forum where they have permission
if($user->checkMute($username) || !$thread_obj->canView($thread, $username, $rank) || !$thread_obj->canReply($thread, $rank)) $base->redirect('index.php');

//floodlimit time
$flood_limit = $database->processQuery("SELECT `floodlimit` FROM `config` LIMIT 1", array(), true);

//get the user's last post (time)
$last_post = $database->processQuery("SELECT `lastpost` FROM `users` WHERE `username` = ? LIMIT 1", array($username), true);

if(isset($_POST['message']))
{
	//if they clicked cancel instead of "reply"
	if(isset($_POST['cancel'])) $base->redirect('viewthread.php?forum='. $f.'&id='. $thread);

	//make sure the title and message meet the standards
	if(strlen($_POST['message']) > 2000 && $rank < 3)
	{
		$content = '<div class="frame e">Your post can\'t be larger than 2000 characters.</div>';
	}
        elseif(strlen($_POST['message']) == 0)
        {
                $content = '<div class="frame e">You have to have a least one character in your post.</div>';
        }
	elseif((time()-$last_post[0]['lastpost']) < $flood_limit[0]['floodlimit'] && $rank < 3)
	{
		$content = '<div class="frame e">You\'re attempting to post too soon.</div>';
	}
	else
	{
		//auto-hiding?
		$data = $database->processQuery("SELECT `autohiding` FROM `threads` WHERE `id` = ?", array($thread), true);
		$status = ($data[0]['autohiding'] == 1) ? 1 : 0;
		
		//insert post
		$database->processQuery("INSERT INTO `posts` VALUES (null, ?, ?, ?, NOW(), ?, '', ?, ?)", array($username, nl2br($_POST['message']), $thread, $status, $_SERVER['REMOTE_ADDR'], time()), false);
		
		$creation_id = $database->getInsertId();
		
		//update thread
		$database->processQuery("UPDATE `threads` SET `lastposter` = ?, `lastpost` = NOW() WHERE `id` = ?", array($username, $thread), false);
		
		//update their last post and lastip fieldss
                $database->processQuery("UPDATE `users` SET `lastpost` = ?, `lastip` = ? WHERE `username` = ? LIMIT 1", array(time(), $_SERVER['REMOTE_ADDR'], $username), false);
                
                //if the lock option was set, lock the thread!
                if($_POST['lock'] == 1) $thread_obj->lock($thread, $rank); $base->appendToFile('logs.txt', array($username.' locked the thread '. $thread));
		
		//send them to the thread they posted on
		$base->redirect('viewthread.php?forum='. $f .'&id='. $thread.'&goto='. $creation_id);
	}
}
else
{
	$chars = ($rank > 2) ? $chars = 100000 : $chars = 2000; 

	if(isset($_GET['quote']) && isset($_GET['qt']) && $rank > 3)
	{
		$quote = ($_GET['qt'] == 1) ? $database->processQuery("SELECT `content`,`username` FROM `posts` WHERE `id` = ?", array($_GET['quote']), true) : $database->processQuery("SELECT `content`,`username` FROM `threads` WHERE `id` = ?", array($_GET['quote']), true);
		
		$text = $base->remBr('[quote='. $quote[0]['username'] .']'. $quote[0]['content'] .'[/quote]');
	}
        
        $content = '                    
        <div id="nocontrols" class="phold"></div>
        <div id="command">
        <form method="post" action="reply.php">
        <input type="hidden" name="id" value="'. $thread .'">
        <input type="hidden" name="forum" value="'. $f .'">
        <table>';
        
        if($rank > 2) $content .= '<tr><td class="commandtwo" colspan="2"><input type="checkbox" name="lock" value="1"> Toggle Lock</td></tr>';
        
        
        $content .='
        <tr>
                        <td class="commandtwo" colspan="2">
                        <textarea id="charlimit_text_a" name="message" rows="20" cols="60">'. htmlentities($text) .'</textarea><br />
                        You have <span id="charlimit_count_a">'. $chars .'</span> characters <span id="charlimit_info_a" style="display: none">remaining</span> for your message.</td>
        </tr>
        <tr>
        <td class="commandtwo" colspan="2"><br />
                        <input type="submit" name="add" value="Add reply" /> &nbsp; &nbsp;
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