<?php
require('../includes/config.php');
require('../structure/database.php');
require('../structure/base.php');
require('../structure/forum.php');
require('../structure/forum.thread.php');;
require('../structure/user.php');

$base = new base;
$database = new database($db_host, $db_name, $db_user, $db_password);
$user = new user($database);
$forum = new forum($database);
$thread_obj = new thread($database);

$user->updateLastActive();

//get the user's rank and username, and set the forum variable (less typing)
$username = $user->getUsername($_COOKIE['user'], 0);
$rank = $user->getRank($username);
$f = $_GET['forum'];


//let's also make sure they have the right permissions to view the forum
if($forum->canView($f, $rank) == false) $base->redirect('index.php');

//check if a moderator is taking action against threads
if(isset($_POST['action']) && isset($_POST['selection']) && $rank > 2)
{
        //get all the threads we're going to update
        foreach($_POST['selection'] as $object)
        {
                $threads .= $object.'-';
        }

        //now send them off to action.php to update all the threads selected
        $base->redirect('action.php?forum='. $f .'&action='. $_POST['action'] .'&threads='. $threads);
}

$forum_details = $database->processQuery("SELECT `icon`,`title`,`type` FROM `forums` WHERE `id` = ? LIMIT 1", array($f), true);

//remove slashes from title
$forum_details[0]['title'] = stripslashes($forum_details[0]['title']);

//Check existence of the specified forum
if($database->getRowCount() == 0)
{
    $base->redirect('index.php');
}
    
//pagination
$per_page = 20;

//get # of pages
$database->processQuery("SELECT * FROM `threads` WHERE `parent` = ?", array($f), false);
$pages = ceil($database->getRowCount() / $per_page);

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
<link rel="shortcut icon" href="../img/favicon.ico" />
<?php include('../includes/google_analytics.html'); ?>
</head>

	<div id="body">
            <?php $forum->getNavBar($username, $rank); ?>
            <br /><br />
		
		<div id="picker">
			<form method="post" action="jump.php">
				<ul class="flat">
					<!--<li><a href=""><img src="forums.ws_files/search_threads.gif"> Search threads</a></li>-->
					<li>Jump to Thread: <input size="8" name="qfc" /></li>
					<li><input class="b" name="jump" value="Go" type="submit" /></li>
				</ul>
			</form>

		</div>
		<div id="infopane">
                    <span class="title"><?php echo $forum->getIcon($forum_details[0]['icon']).' '.$forum_details[0]['title'].' '.$forum->getIcon($forum_details[0]['icon']); ?></span>
			<div class="about">
				<ul class="flat">
					<!--<li><a href="#"><img src="../img/forum/code_of_conduct.gif" /> Code of Conduct</a></li>-->
				</ul>
			</div>
		</div>

		<!--<div id="nocontrols" class="phold"></div>-->

		<br>
		<div id="breadcrumb">
                    <a href="../index.php">Home</a> &gt; <a href="index.php"><?php echo $data['wb_name']; ?> Forums</a> &gt; <a href="viewforum.php?forum=<?php echo $f; ?>"><?php echo $forum_details[0]['title']; ?></a>
		</div>
				<div class="actions" id="top">
			<table>
				<tbody>
					<tr>
						<td class="nav">
							<form action="viewforum.php" method="GET" style="display: inline;">
								<input type="hidden" name="forum" value="<?php echo $f; ?>" />
								<ul class="flat">
									<li><a href="viewforum.php?forum=<?php echo $f; ?>">&lt;&lt; first</a></li>
									<li><a href="viewforum.php?forum=<?php echo $f.'&page='.$prev; ?>">&lt; prev</a></li>
									<li>Page <input type="text" class="textinput" id="page" name="page" value="<?php echo $page; ?>" size="2" maxlength="3" /> of <?php echo $pages; ?></li>
									<li><a href="viewforum.php?forum=<?php echo $f.'&page='.$next; ?>">next &gt;</a></li>
									<li><a href="viewforum.php?forum=<?php echo $f.'&page='.$pages; ?>">last &gt;&gt;</a></li>
								</ul>
							</form>
						</td>
						<td class="commands">
							<ul class="flat">
								<li><a href=""><img src="../img/forum/refresh.gif" alt=""> Refresh</a></li>
								<?php if($user->isLoggedIn() && $forum->canCreate($f, $rank) && !$user->checkMute($username)) echo '<li><a href="create.php?forum='. $f .'"><img src="../img/forum/new_thread.gif" alt=""> Create a New Thread</a></li>';?>
															</ul>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
			<div id="content" class="border">
				<table id="t_holder">
					<tr height="20px">
						<td width="63%"></td>
						<td width="15%" class="title num">Posts</td>
						<td width="22%" class="title">Last Post</td>
					</tr>
					
					<?php
                                                if($rank > 2) echo '<form action="viewforum.php?forum='. $f .'" method="POST">';
                                        
                                                //query threads
                                                $query_threads = $database->processQuery("SELECT `id`,`title`,`username`,`lastpost`,`date`,`lastposter`,`sticky`,`lock`,`hidden`,`moved` FROM `threads` WHERE `parent` = ? ORDER BY `sticky` DESC, `lastpost` DESC LIMIT $start,$per_page", array($f), true);
                                                
                                                foreach($query_threads as $thread)
                                                {
                                                   $id = $thread['id'];
                                                   
                                                   //set $moved as a boolean to avoid retyping this statement
                                                   $moved = (empty($thread['moved'])) ? false : true;
                                                    
                                                   (empty($thread['lastpost'])) ? $last_post = $thread['date'] : $last_post = $thread['lastpost'];
                                                   (empty($thread['lastposter'])) ? $last_poster = $thread['username'] : $last_poster = $thread['lastposter'];
                                                   
                                                   //set thread display type
                                                   $type = ($thread['hidden'] == 1) ? 'thdhid' : 'thdnrml';
                                                   
                                                   if($thread['hidden'] == 1 && $rank < 3)
                                                   {
                                                       ?>
                                        
                                                        <tr class="thdhid">
                                                        <td width="63%"><img src="../img/forum/locked.gif"> This thread has been hidden<br /></td>
                                                        <td class="num"></td>
                                                        <td class="righttd updated"></td>
                                                        </tr>
                                                        <tr height="20px"></tr>
                                                       
                                                       <?php
                                                   }
                                                   else
                                                   {
                                                        //get # of replies the thread has
                                                        $database->processQuery("SELECT * FROM `posts` WHERE `thread` = ?", array(($moved) ? $thread['moved'] : $id), false);
                                                        $replies = $database->getRowCount();
                                                        $title = stripslashes(htmlentities($thread['title']));
                                                        
                                                        //apply filter is it's a user's thread
                                                        if($user->getRank($thread['username']) < 3) $title = $forum->filter($title);
                                                        
                                                        //append "(moved)" to the title if the thread was moved
                                                        if($moved) $title .= '&nbsp;(moved)';
                                                       
                                                       ?>
                                        
                                                        <tr class="<?php echo $type; ?>">
                                                        <td width="63%" id="td-<?php echo $id; ?>" title="<?php echo $title; ?>">
                                                            <?php echo $thread_obj->preTitle($id, $rank, 0); ?> <a href="viewthread.php?forum=<?php echo $f; ?>&id=<?php echo $id; ?>"><?php echo $title; ?></a>
                                                            &nbsp;<mes id="mes-<?php echo $id; ?>" style="display:none;"><b>Thread Updated!</b></mes>
                                                            <br />created by <?php echo $thread['username']; ?>
                                                        </td>
                                                        <td class="num"><?php echo $replies; ?></td>
                                                        <td class="righttd updated"><?php echo $last_post.'<br/> by '.$last_poster; ?></td>
                                                        </tr>
                                                        <tr height="20px"></tr>
                                        
                                                       <?php
                                                   }
                                                }
					?>
					
				</table>
			</div>
		<?php 
					if($rank > 2)
					{
						?>
			
							<div id="content" class="border">
								<div id="no_form_mess">
									<input type="hidden" name="forum" value="<?php echo $f; ?>">
									<select name="action">
									<option value="1">Hide</option>
									<option value="2">Lock</option>
									<option value="3">Move</option>
									<?php if($rank > 3) echo '<option value="4">Auto-Hide</option><option value="5">Sticky</option><option value="6">Delete</option>'; ?>
									</select>
									<input type="submit" value="Update Selected Threads">
									<br/>
								</div>
							</div>
							</form>
						<?php
					}
				?>

		<div id="breadcrumb">
			<a href="../index.php">Home</a> &gt; <a href="index.php"><?php echo $data['wb_name']; ?> Forums</a> &gt; <a href="viewforum.php?forum=<?php echo $f; ?>"><?php echo $forum_details[0]['title']; ?></a>
		</div>
		<div class="actions" id="top">
			<table>
				<tbody>
					<tr>
						<td class="nav">
							<form action="viewforum.php" method="GET" style="display: inline;">
								<input type="hidden" name="forum" value="<?php echo $f; ?>" />
								<ul class="flat">
									<li><a href="viewforum.php?forum=<?php echo $f; ?>">&lt;&lt; first</a></li>
									<li><a href="viewforum.php?forum=<?php echo $f.'&page='.$prev; ?>">&lt; prev</a></li>
									<li>Page <input type="text" class="textinput" id="page" name="page" value="<?php echo $page; ?>" size="2" maxlength="3" /> of <?php echo $pages; ?></li>
									<li><a href="viewforum.php?forum=<?php echo $f.'&page='.$next; ?>">next &gt;</a></li>
									<li><a href="viewforum.php?forum=<?php echo $f.'&page='.$pages; ?>">last &gt;&gt;</a></li>
								</ul>
							</form>
						</td>
						<td class="commands">
							<ul class="flat">
								<li><a href=""><img src="../img/forum/refresh.gif" alt=""> Refresh</a></li>
								<?php if($user->isLoggedIn() && $forum->canCreate($f, $rank) && !$user->checkMute($username)) echo '<li><a href="create.php?forum='. $f .'"><img src="../img/forum/new_thread.gif" alt=""> Create a New Thread</a></li>'; ?>
															</ul>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		
		<br>

		<div class="tandc">
                    <?php echo $data['wb_foot']; ?>
		</div>
	</div>
        <script type="text/javascript" src="../js/jquery.js"></script>
        <script type="text/javascript" src="../js/threadmod.js"></script>
</body>
</html>