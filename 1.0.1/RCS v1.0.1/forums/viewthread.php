<?php 
require('../includes/config.php');
require('../structure/base.php');
require('../structure/forum.php');
require('../structure/forum.index.php');
require('../structure/forum.thread.php');
require('../structure/forum.post.php');
require('../structure/database.php');
require('../structure/user.php');

$database = new database($db_host, $db_name, $db_user, $db_password);
$base = new base($database);
$user = new user($database);
$forum = new forum($database);
$forum_index = new forum_index($database);
$thread = new thread($database);
$post = new post($database);
$user->updateLastActive();

//get config
$config = $base->loadConfig();

//set some variables that are used a lot throughout the page
$username = $user->getUsername($_COOKIE['user'], 2);
$rank = $user->getRank($username);

$f = $_GET['forum'];
$i = $_GET['id'];

//preform basic checks
if(!ctype_digit($f) || !ctype_digit($i) || !$thread->checkExistence($i) || !$thread->canView($i, $username, $rank)) $base->redirect('index.php');

//if the GOTO field is set, let's skip to the selected post
if(ctype_digit($_GET['goto']))
{
    $getPageNum = $thread->getPageNum($_GET['goto'], $i);
    if($getPageNum) $base->redirect('viewthread.php?forum='. $f.'&id='.$i.'&page='.$getPageNum.'&highlight='. $_GET['goto'] .'#'.$_GET['goto']);
}

//extract thread details
$detail_query = $database->processQuery("SELECT `id`,`lock`,`sticky`,`title`,`username`,`status`,`content`,`date`,`lastedit`,`qfc`,`moved`,`hidden`,`autohiding` FROM `threads` WHERE `id` = ? LIMIT 1", array($i), true);

//assign data to details[] array
$details['lock']        = $detail_query[0]['lock'];
$details['sticky']      = $detail_query[0]['sticky'];
$details['title']       = stripslashes(htmlentities($detail_query[0]['title']));
$details['username']    = $detail_query[0]['username'];
$details['status']      = $detail_query[0]['status'];
$details['content']     = $detail_query[0]['content'];
$details['date']        = $detail_query[0]['date'];
$details['lastedit']    = $detail_query[0]['lastedit'];
$details['qfc']         = $detail_query[0]['qfc'];
$details['moved']       = $detail_query[0]['moved'];
$details['hidden']      = $detail_query[0]['hidden'];
$details['autohiding']  = $detail_query[0]['autohiding'];

//apply word filter if it's a user thread
if($user->getRank($details['username']) < 3) $details['title'] = $forum->filter($details['title']); $details['content'] = $forum->filter($details['content']);

//get any extra icons
if($details['sticky'] == 1) $extra .= '<img src="../img/forum/sticky.gif"> ';
if($details['lock'] == 1) $extra .= ' <img src="../img/forum/locked.gif">';

//check if the POST has been edited, then adjust the $date variable accordingly
if(empty($details['lastedit']))
{
        $date = $details['date'];
}
else
{
        //get USERNAME:DATE/TIME
        $edit_details = explode('@', $details['lastedit']);
        $date = $details['date'].'<br/>Last edit on '. $edit_details[1] .' by '. $edit_details[0];
}
				

//get forum details
$forum_details = $database->processQuery("SELECT `title` FROM `forums` WHERE `id` = ?", array($f), true);

//pagination
$per_page = 10;

//get # of pages
$database->processQuery("SELECT * FROM `posts` WHERE `thread` = ?", array($i), false);
$pages = ($database->getRowCount() == 0) ? 1 : ceil($database->getRowCount() / $per_page);

//get current page
(!ctype_digit($_GET['page']) || $_GET['page'] > $pages) ? $page = 1 : $page = $_GET['page'];

//get next link
($page < $pages) ? $next = $page+1 : $next = $page;

//get prev link
(($page-1) >= 1) ? $prev = ($page-1) : $prev = $page;

//start
$start = ($page-1)*$per_page;
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
<script src="../js/v_thread_2.js"></script>
<!--[if IE 8]>
<link rel="stylesheet" type="text/css" href="../css/forummsg-ie-1.css" />
<![endif]-->
<?php include('../includes/google_analytics.html'); ?>
</head>
	<body>
		<div id="body">
		<?php $forum->getNavBar($username, $rank); ?>
                <br /><br />

		<div id="picker">
			<form method="post" action="jump.php">
				<ul class="flat">
					<!--<li><a href=""><img src="../img/forums/search_threads.gif"> Search threads</a></li>-->
					<li>Jump to Thread: <input size="8" name="qfc" maxlength="14" /></li>
					<li><input class="b" name="jump" value="Go" type="submit" /></li>

			</ul>
			</form>
		</div>
		<div id="infopane">
			<span class="title"><?php echo $extra.' '.$details['title']; ?></span>
			<div class="about">
				<ul class="flat">
					<!--<li><a href="#"><img src="../img/forum/code_of_conduct.gif" /> Code of Conduct</a></li>-->
				</ul>
			</div>
		</div>

		<!--<div id="nocontrols" class="phold"></div>-->
                <br>
                <?php
                    if($rank > 2)
                    {
                        ?>
                
                            <div id="mod_controls">
                                <a href="actions/lockthread.php?forum=<?php echo $f; ?>&id=<?php echo $i; ?>"><?php echo ($details['lock'] == 1) ? 'Unlock' : 'Lock'; ?></a>&nbsp;&nbsp;
                                <a href="actions/hidethread.php?forum=<?php echo $f; ?>&id=<?php echo $i; ?>&type=2"><?php echo ($details['status'] == 1) ? 'Uncover' : 'Cover'; ?></a>&nbsp;&nbsp;
                                <a href="actions/hidethread.php?forum=<?php echo $f; ?>&id=<?php echo $i; ?>"><?php echo ($details['hidden'] == 1) ? 'Unhide' : 'Hide'; ?></a>&nbsp;&nbsp;
                                <?php
                                    if($rank > 3) 
                                    {
                                        ?>
                                            <a href="actions/toggle_autohide.php?forum=<?php echo $f; ?>&id=<?php echo $i; ?>"><?php echo ($details['autohiding'] == 1) ? 'Auto-Show' : 'Auto-Hide'; ?></a>&nbsp;&nbsp;
                                            <a href="actions/stickthread.php?forum=<?php echo $f; ?>&id=<?php echo $i; ?>"><?php echo ($details['sticky'] == 1) ? 'Unstick' : 'Sticky'; ?></a>&nbsp;&nbsp;
                                        <?php
                                    }
                                ?>
                                <form action="action.php" method="GET">
                                    <input type="hidden" name="action" value="3">
                                    <input type="hidden" name="threads" value="<?php echo $i; ?>">
                                    <select name="moveto">
                                        <?php
                                            $categories = $forum_index->retrieveCategories();

                                            foreach($categories as $category)
                                            {
                                                $listing = $forum_index->retrieveSubForums($category['id']);

                                                echo '<option disabled="disabled">'. $category['title'] .'</option>';

                                                foreach($listing as $list_f)
                                                {
                                                    echo '<option value="'. $list_f['id'] .'">'. $list_f['title'] .'</option>';
                                                }
                                            }
                                        ?>
                                    </select>
                                    <input type="submit" value="Move">
                                </form>
                            </div>
                
                        <?php
                    }
                    
                ?>
		<br>
		
		<div id="breadcrumb">
			<a href="../index.php">Home</a> &gt; <a href="index.php"><?php echo $data['wb_name']; ?></a> &gt; <a href="viewforum.php?forum=<?php echo $f; ?>"><?php echo $forum_details[0]['title']; ?></a> &gt; <a href="viewthread.php?forum=<?php echo $f.'&id='.$i; ?>"><?php echo $details['title']; ?></a>
		</div>
				<div class="actions" id="top">
			<table>
				<tbody>
					<tr>
						<td class="nav">
							<form action="viewthread.php" method="get" style="display: inline;">
								<input type="hidden" name="id" value="<?php echo $i; ?>" />
								<input type="hidden" name="forum" value="<?php echo $f; ?>" />
								<ul class="flat">
									<li><a href="viewthread.php?forum=<?php echo $f.'&id='.$i; ?>">&lt;&lt; first</a></li>
									<li><a href="viewthread.php?forum=<?php echo $f.'&id='.$i; ?>&page=<?php echo $prev; ?>">&lt; prev</a></li>
									<li>Page <input type="text" class="textinput" id="page" name="page" value="<?php echo $page; ?>" size="2" maxlength="3" /> of <?php echo $pages; ?></li>
									<li><a href="viewthread.php?forum=<?php echo $f.'&id='.$i; ?>&page=<?php echo $next; ?>">next &gt;</a></li>
									<li><a href="viewthread.php?forum=<?php echo $f.'&id='.$i; ?>&page=<?php echo $pages; ?>">last &gt;&gt;</a></li>
								</ul>
							</form>
						</td>
						<td class="commands">
							<ul class="flat">
                                                                    <li><a href=""><img src="../img/forum/refresh.gif" alt=""> Refresh</a></li>
                                                                    <?php if($thread->canReply($i, $rank) && !$user->checkMute($username)) echo '<li><a href="reply.php?forum='. $f .'&id='. $i .'"><img src="../img/forum/new_thread.gif" alt="T" /> Reply</a></li>'; ?>
                                                                    <?php if($thread->canReply($i, $rank) && !$user->checkMute($username) && $details['username'] == $username) echo '<li><a href="actions/bumpthread.php?&forum='. $f .'&id='. $i .'"><img src="../img/forum/bump_thread.gif" alt=""> Bump Thread</a></li>'; ?>
                                                        </ul>
						</td>
					</tr>

				</tbody>
			</table>
		</div>
		<!--<form action="javascript://" method="post">-->
		<div id="contentmsg">
					<a name="188090"></a>
                                        <?php
                                        //set the action bar
                                        $action_bar = (($rank < 3 && $details['status'] == 0 && $details['lock'] == 0) || $rank > 2) ? $thread->getActionBar($rank, $i, $f) : null;
                                        
                                        //only display thread on page 1
                                        if($page == 1)
                                        {
                                            if($details['status'] == 1 && $rank < 3)
                                            {
                                                ?>
                                        
                                                <table class="<?php echo $thread->getPostType($details['status'], $details['username']); ?>">
                                                <tbody>
                                                                        <tr>
                                                                                <td class="leftpanel">
                                                                                        <div class="msgcreator uname"> </div>
                                                                                        <div class="modtype"> </div>
                                                                                        <div class="msgcommands"> </div>
                                                                                </td>
                                                                                <td class="rightpanel">
                                                                                        <div class="msgtime"><br/></div>
                                                                                        <div class="msgcontents">
                                                                                                <i>The contents of this message is hidden</i>
                                                                                                </div>
                                                                                        <span style="float:right; margin-right: 5px; margin-bottom: 5px; margin-top: -20px;"><?php if($rank > 2) echo $action_bar; ?></span>
                                                                                </td>
                                                                        </tr>
                                                </tbody>
                                                </table>
                                        
                                                <?php
                                            }
                                            else
                                            {
                                                ?>
                                               
                                                <table class="<?php echo $thread->getPostType($details['status'], $details['username'], (empty($details['moved'])) ? false : true, ($_GET['goto'] == 'start') ? true : false); ?>">
                                                <tbody>
                                                                        <tr>
                                                                                <td class="leftpanel">
                                                                                        <div class="msgcreator uname"><?php echo (empty($details['moved'])) ? $user->dName($details['username']) : $details['username']; ?></div>
                                                                                        <div class="modtype">
                                                                                            <?php echo $forum->getPostTitle($details['username']); ?>
                                                                                            <?php if($config['postcount'] == 1) { ?><br/><br/>Post Count: <?php echo $user->postcount($details['username']); } ?>
                                                                                        </div>
                                                                                        <div class="msgcommands"></div>
                                                                                </td>
                                                                                <td class="rightpanel">
                                                                                        <div class="msgtime"><?php echo $date; ?></div>
                                                                                        <div class="msgcontents">
                                                                                                <?php echo $base->br2nl((empty($details['moved'])) ? $thread->formatPost($details['content'], $details['username'], $forum) : $details['content']); ?>
                                                                                                </div>
                                                                                        <span style="float:right; margin-right: 5px; margin-bottom: 5px; margin-top: -20px;"><?php echo ($details['username'] == $username && $rank < 3 && $details['status'] == 0 && $details['lock'] == 0) ? '<a href="edit.php?forum='. $f .'&id='. $i .'&type=2">Edit</a> | '.$action_bar : $action_bar; ?></span>
                                                                                </td>
                                                                        </tr>
                                                </tbody>
                                                </table>
                                        
                                                <?php
                                            }
                                        }
                                        
                                        //don't extract posts if the thread is a moved thread
                                        if(empty($details['moved']))
                                        {
                                            //display posts
                                            $replies = $database->processQuery("SELECT `id`,`username`,`content`,`lastedit`,`date`,`status` FROM `posts` WHERE `thread` = ? ORDER BY `id` ASC LIMIT $start,$per_page", array($i), true);

                                            foreach($replies as $reply)
                                            {
                                                //set the user's action bar
                                                $action_bar = (($rank < 3 && $details['status'] == 0 && $details['lock'] == 0) || $rank > 2) ? $post->getActionBar($rank, $reply['id'], $i, $f) : null;



                                                //check if the POST has been edited, then adjust the $date variable accordingly
                                                if(empty($reply['lastedit']))
                                                {
                                                        $date = $reply['date'];
                                                }
                                                else
                                                {
                                                        //get USERNAME:DATE/TIME
                                                        $edit_details = explode('@', $reply['lastedit']);
                                                        $date = $reply['date'].'<br/>Last edit on '. $edit_details[1] .' by '. $edit_details[0];
                                                }

                                                if($reply['status'] == 1 && $rank < 3)
                                                {
                                                    ?>
                                                    <a name="<?php echo $reply['id']; ?>"></a>
                                                    <table class="<?php echo $thread->getPostType($reply['status'], $reply['username']); ?>">
                                                    <tbody>
                                                                            <tr>
                                                                                    <td class="leftpanel">
                                                                                            <div class="msgcreator uname"> </div>
                                                                                            <div class="modtype"> </div>
                                                                                            <div class="msgcommands"> </div>
                                                                                    </td>
                                                                                    <td class="rightpanel">
                                                                                            <div class="msgtime"><br/></div>
                                                                                            <div class="msgcontents">
                                                                                                    <i>The contents of this message is hidden</i>
                                                                                                    </div>
                                                                                            <span style="float:right; margin-right: 5px; margin-bottom: 5px; margin-top: -20px;"><?php if($rank > 2) echo $action_bar; ?></span>
                                                                                    </td>
                                                                            </tr>
                                                    </tbody>
                                                    </table>

                                                    <?php
                                                }
                                                else
                                                {
                                                ?>
                                                    <a name="<?php echo $reply['id']; ?>"></a>
                                                    <table class="<?php echo $thread->getPostType($reply['status'], $reply['username'], false, ($_GET['highlight'] == $reply['id']) ? true : false); ?>">
                                                    <tbody>
                                                                            <tr>
                                                                                    <td class="leftpanel">
                                                                                            <div class="msgcreator uname"><?php echo $user->dName($reply['username']); ?></div>
                                                                                            <div class="modtype">
                                                                                                <?php echo $forum->getPostTitle($reply['username']); ?>
                                                                                                <?php if($config['postcount'] == 1) { ?><br/><br/>Post Count: <?php echo $user->postcount($reply['username']); } ?>
                                                                                            </div>
                                                                                            <div class="msgcommands"></div>
                                                                                    </td>
                                                                                    <td class="rightpanel">
                                                                                            <div class="msgtime"><?php echo $date; ?></div>
                                                                                            <div class="msgcontents">
                                                                                                    <?php echo $base->br2nl($thread->formatPost($reply['content'], $reply['username'])); ?>
                                                                                                    </div>
                                                                                            <span style="float:right; margin-right: 5px; margin-bottom: 5px; margin-top: -20px;"><?php echo ($reply['username'] == $username && $rank < 3 && $details['status'] == 0 && $details['lock'] == 0) ? '<a href="edit.php?forum='. $f .'&id='. $i .'&type=1&pid='. $reply['id'] .'">Edit</a> | '.$action_bar : $action_bar; ?></span>
                                                                                    </td>
                                                                            </tr>
                                                    </tbody>
                                                    </table>
                                                    <?php
                                                }
                                            }
                                        }
                                        ?>
                </div>
		<!--
				-->
		<div id="breadcrumb">
			<a href="../index.php">Home</a> &gt; <a href="index.php"><?php echo $data['wb_name']; ?></a> &gt; <a href="viewforum.php?forum=<?php echo $f; ?>"><?php echo $forum_details[0]['title']; ?></a> &gt; <a href="viewthread.php?forum=<?php echo $f.'&id='.$i; ?>"><?php echo $details['title']; ?></a>
		</div>

			<div class="actions" id="top">
				<table>
					<tbody>
						<tr>
							<td class="nav">
							<form action="viewthread.php" method="get" style="display: inline;">
								<input type="hidden" name="id" value="<?php echo $i; ?>" />
								<input type="hidden" name="forum" value="<?php echo $f; ?>" />
								<ul class="flat">
									<li><a href="viewthread.php?forum=<?php echo $f.'&id='.$i; ?>">&lt;&lt; first</a></li>
									<li><a href="viewthread.php?forum=<?php echo $f.'&id='.$i; ?>&page=<?php echo $prev; ?>">&lt; prev</a></li>
									<li>Page <input type="text" class="textinput" id="page" name="page" value="<?php echo $page; ?>" size="2" maxlength="3" /> of <?php echo $pages; ?></li>
									<li><a href="viewthread.php?forum=<?php echo $f.'&id='.$i; ?>&page=<?php echo $next; ?>">next &gt;</a></li>
									<li><a href="viewthread.php?forum=<?php echo $f.'&id='.$i; ?>&page=<?php echo $pages; ?>">last &gt;&gt;</a></li>
								</ul>
							</form>
                                                        </td>
                                                        
							<td class="commands">
                                                            <ul class="flat">
                                                                    <li><a href=""><img src="../img/forum/refresh.gif" alt=""> Refresh</a></li>
                                                                    <?php if($thread->canReply($i, $rank) && !$user->checkMute($username)) echo '<li><a href="reply.php?forum='. $f .'&id='. $i .'"><img src="../img/forum/new_thread.gif" alt="T" /> Reply</a></li>'; ?>
                                                                    <?php if($thread->canReply($i, $rank) && !$user->checkMute($username) && $details['username'] == $username) echo '<li><a href="actions/bumpthread.php?&forum='. $f .'&id='. $i .'"><img src="../img/forum/bump_thread.gif" alt=""> Bump Thread</a></li>'; ?>
                                                            </ul>
                                                        </td>
						</tr>
					</tbody>
				</table>
			</div>

			<div id="uid">Quick find code: <?php echo $details['qfc']; ?></div>
			
			<br>

			<div class="tandc"><?php echo $data['wb_foot']; ?></div>
	</div>	
	</body>
</html>