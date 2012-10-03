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

//set config
$config = $base->loadConfig();

$user->updateLastActive();

//set some variables that are used a lot throughout the page
$username = $user->getUsername($_COOKIE['user'], 2);

//make sure the user is logged in and the required data is set
if(!$user->isLoggedIn() || !ctype_digit($_REQUEST['forum']) || !ctype_digit($_REQUEST['id']) || !ctype_digit($_REQUEST['type']) || (!ctype_digit($_REQUEST['pid']) && $_REQUEST['type'] == 1)) $base->redirect('index.php');

//set some variables that are used a lot throughout the page
$rank = $user->getRank($username);
$f = $_REQUEST['forum'];
$thread = $_REQUEST['id'];

//make sure they are reporting a post that isn't locked/hidden or where they can't "see"
if($user->checkMute($username) || !$thread_obj->canView($thread, $username, $rank) || !$thread_obj->canReply($thread, $rank)) $base->redirect('index.php');

$type = $_POST['type'];
$reported = ($type == 1) ? $_POST['pid'].':p' : $_POST['id'].':t';

//make sure what they're reporting isn't already reported
$database->processQuery("SELECT * FROM `reports` WHERE `reported` = ?", array($reported), false);

if($database->getRowCount())
{
    echo '<div class="frame e">A report already exists for this post.</div>';
}
elseif(isset($_POST['message']))
{
    //set redirect URL
    $url = ($type == 1) ? 'viewthread.php?forum='. $f .'&id='. $thread.'&goto='. $_POST['pid'] : 'viewthread.php?forum='. $f .'&id='. $thread.'&goto=start';

    //if they clicked cancel instead of "report"
    if(isset($_POST['cancel'])) $base->redirect($url);

    //make sure the title and message meet the standards
    if(strlen($_POST['message']) > 250)
    {
        echo '<div class="frame e">Your post can\'t be larger than 250 characters.</div>';
    }
    else
    {
        //extract reported username and content and set vars according to post type (thread/post)
        $data = ($type == 1) ? $database->processQuery("SELECT `username`,`content` FROM `posts` WHERE `id` = ?", array($_POST['pid']), true) : $database->processQuery("SELECT `username`,`content` FROM `threads` WHERE `id`= ?", array($_POST['id']), true);
        
        $content = '<a href="'. $url .'" target="_blank">Click here to view the reported post.</a><br/><br/><b>Reported Content: </b><br/>[quote='. $data[0]['username'] .'] '. htmlentities($data[0]['content'], ENT_NOQUOTES) .' [/quote]<br/><br/><b>Comment by Reporter:</b><br/>[quote='. $username .']'. htmlentities(nl2br($_POST['message']), ENT_NOQUOTES) .'[/quote]';
        
        //insert report
        $database->processQuery("INSERT INTO `threads` VALUES (null, ?, ?, ?, ?, NOW(), ?, NOW(), ?, '', NOW(), ?, '0', '0', '0', '', '0', ?, '0')", array($config['reportforum'], 'Report By: '.$username, $content, 'Report', qfc($database), $username, $_SERVER['REMOTE_ADDR'], time()), false);
        $database->processQuery("INSERT INTO `reports` VALUES (null, ?)", array($reported), false);
        
        //send them to their newly editted post
        if($type == 1)
            $base->redirect($url);
        else
            $base->redirect($url);
    }
}
else
{

    $content = '
    <div id="nocontrols" class="phold"></div>
    <div id="command">
    <form method="post" action="report.php">';

    if($_GET['type'] == 1) $content .= '<input type="hidden" name="pid" value="'. $_GET['pid'] .'">';

    $content .= '
    <input type="hidden" name="type" value="'. $_GET['type'] .'">
    <input type="hidden" name="id" value="'. $thread .'">
    <input type="hidden" name="forum" value="'. $f .'">
    <table>
    <tr>
            <td class="commandtwo" colspan="2">You have <span id="charlimit_count_b">30</span> characters <span id="charlimit_info_b" style="display: none">remaining</span> for your title.</td>
    </tr>
    <tr>
            <td class="commandtwo" colspan="2">
            <textarea id="charlimit_text_a" name="message" rows="20" cols="60"></textarea><br />
            You have <span id="charlimit_count_a">250</span> characters <span id="charlimit_info_a" style="display: none">remaining</span> for your message.</td>
    </tr>
    <tr>
    <td class="commandtwo" colspan="2"><br />
            <input type="submit" name="add" value="Report" /> &nbsp; &nbsp;
            <!--<input type="submit" name="preview" value="Preview" /> &nbsp; &nbsp;-->
            <input type="submit" name="cancel" value="Cancel" /> &nbsp; &nbsp;
    </td>
    </tr>
    </table>
    </form>
    </div>';
}

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
                        <?php
                            echo $content;
                        ?>
			<br />
				<div class="tandc"><?php echo $data['wb_foot']; ?></div>
		</div>

	</div>
</body>