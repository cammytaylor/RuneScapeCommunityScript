<?php
require('../includes/config.php');
require('../structure/database.php');
require('../structure/forum.php');
require('../structure/base.php');
require('../structure/user.php');

$database = new database($db_host, $db_name, $db_user, $db_password);
$base = new base($database);
$user = new user($database);
$forum = new forum($database);

$user->updateLastActive();

//make sure the user is logged in and required data is set
if(!ctype_digit($_REQUEST['forum']) || !$user->isLoggedIn()) $base->redirect('index.php');

//set some variables that are used a lot throughout the page
$username = $user->getUsername($_COOKIE['user'], 2);
$rank = $user->getRank($username);
$f = $_REQUEST['forum'];

//make sure they are posting in a forum where they have permission
if($user->checkMute($username) || !$forum->canView($f, $rank) || !$forum->canCreate($f, $rank)) $base->redirect('index.php');

//floodlimit time
$flood_limit = $database->processQuery("SELECT `floodlimit` FROM `config` LIMIT 1", array(), true);

//get the user's last post (time)
$last_post = $database->processQuery("SELECT `lastpost` FROM `users` WHERE `username` = ? LIMIT 1", array($username), true);

if(isset($_POST['cancel'])) $base->redirect('viewforum.php?forum='. $f);
                        
if(isset($_POST['message']) && isset($_POST['title']))
{
    $message = nl2br($_POST['message']);
    $title = $_POST['title'];

    //make sure the title and message meet the standards
    if(strlen($message) > 2000 && $rank < 3)
    {
        $content = '<div class="frame e">Your post can\'t be larger than 2000 characters.</div>';
    }
    elseif(strlen($title) > 30 && $rank < 3)
    {
        $content = '<div class="frame e">Your title can\'t be larger than 30 characters.</div>';
    }
    elseif(strlen($message) == 0)
    {
            $content = '<div class="frame e">You have to have a least one character in your post.</div>';
    }
    elseif((time()-$last_post[0]['lastpost']) < $flood_limit[0]['floodlimit'] && $rank < 3)
    {
        $content = '<div class="frame e">You\'re attempting to post too soon.</div>';
    }
    else
    {
        //check if the thread's parent forces any specific actions
        $forum = $database->processQuery("SELECT `type` FROM `forums` WHERE `id` = ? LIMIT 1", array($f), true);

        $hide = ($forum[0]['type'] == 3) ? 1 : 0;

        //insert thread
        $database->processQuery("INSERT INTO `threads` VALUES (null, ?, ?, ?, ?, NOW(), ?, NOW(), ?, '', ?, ?, '0', '0', '0', '0', ?, ?, '0')", array($f, $title, $message, $username, qfc($database), $username, time(), $_SERVER['REMOTE_ADDR'], $hide, time()), false);

        $creation_id = $database->getInsertId();

        //update their last post and lastip fieldss
        $database->processQuery("UPDATE `users` SET `lastpost` = ?, `lastip` = ? WHERE `username` = ? LIMIT 1", array(time(), $_SERVER['REMOTE_ADDR'], $username), false);

        //send them to their thread
        $base->redirect('viewthread.php?forum='. $f .'&id='. $creation_id.'&goto=start');
    }
}
else
{
    $chars = ($rank > 2) ? $chars = 100000 : $chars = 2000; 
    $title_length = ($rank > 3) ? 50 : 30;

    $content = '
    <div id="nocontrols" class="phold"></div>
    <div id="command">
    <form method="post" action="create.php">
    <input type="hidden" name="forum" value="'. $f .'">
    <table>
    <tr>
        <td class="commandtitle">Thread Title:</td>
        <td class="commandinput"><input size="40" maxlength="'. $title_length .'" id="charlimit_text_b" type="text" class="textinput" name="title"/>
        </td>
    </tr>
    <tr>
        <td class="commandtwo" colspan="2">You have <span id="charlimit_count_b">'. $title_length .'</span> characters <span id="charlimit_info_b" style="display: none">remaining</span> for your title.</td>
    </tr>
    <tr>
        <td class="commandtwo" colspan="2">
        <textarea id="charlimit_text_a" name="message" rows="20" cols="60"></textarea><br />
        You have <span id="charlimit_count_a">'. $chars .'</span> characters <span id="charlimit_info_a" style="display: none">remaining</span> for your message.</td>
    </tr>
    <tr>
    <td class="commandtwo" colspan="2"><br />
        <input type="submit" name="add" value="Add thread" /> &nbsp; &nbsp;
        <!--<input type="submit" name="preview" value="Preview" /> &nbsp; &nbsp;-->
        <input type="submit" name="cancel" value="Cancel" /> &nbsp; &nbsp;
    </td>
    </tr>
    </table>
    </form>
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
						<li><a href="viewforum.php?forum=<?php echo $f; ?>">Return to forums page</a>
						</li>
					</ul>

				</div>
			</div>
                        
                        <?php
                            echo $content;
                            
                            function qfc(database $database)
                            {
                                $qfc = rand(0,9).'-'.rand(0,9).'-'.rand(1000,9999).'-'.rand(10000,19999);
                                
                                //make sure this qfc doesn't already exist (even though it's not likely to happen)
                                $database->processQuery("SELECT * FROM `threads` WHERE `qfc` = ?", array($qfc), false);
                                
                                if($database->getRowCount() >= 1)
                                    qfc();
                                else
                                    return $qfc;
                            }
                        ?>
			<div id="smileylegend">
				<span class="title">Smileys: </span><br>
				<span id="smilytxt" style="display: hidden;">Click to add a smiley to your message (will overwrite selected text).</span><br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<span onclick="addsmiley(':)')"><IMG class=sm0 alt=":)" title=":)" src="../img/forum/smileys/smile.gif"> :)</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<span onclick="addsmiley(';)')"><IMG class=sm1 alt=";)" title=";)" src="../img/forum/smileys/wink.gif"> ;)</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<span onclick="addsmiley(':P')"><IMG class=sm2 alt=":P" title=":P" src="../img/forum/smileys/tongue.gif"> :P</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<span onclick="addsmiley(':(')"><IMG class=sm3 alt=":(" title=":(" src="../img/forum/smileys/sad.gif"> :(</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<span onclick="addsmiley(':|')"><IMG class=sm4 alt=":|" title=":|" src="../img/forum/smileys/nosmile.gif"> :|</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<span onclick="addsmiley('O_o')"><IMG class=sm5 alt="O_o" title="O_o" src="../img/forum/smileys/o.O.gif"> O_o</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<span onclick="addsmiley(':D')"><IMG class=sm6 alt=":D" title=":D" src="../img/forum/smileys/bigsmile.gif"> :D</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<span onclick="addsmiley('^^')"><IMG class=sm7 alt="^^" title="^^" src="../img/forum/smileys/^^.gif"> ^^</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<span onclick="addsmiley(':O')"><IMG class=sm8 alt=":O" title=":O" src="../img/forum/smileys/shocked.gif"> :O</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<span onclick="addsmiley(':@')"><IMG class=sm9 alt=":@" title=":@" src="../img/forum/smileys/angry.gif"> :@</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			</div>
			<br />
				<div class="tandc"><?php echo $data['wb_foot']; ?></div>
		</div>

	</div>
</body>