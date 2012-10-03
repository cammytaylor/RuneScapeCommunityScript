<?php 
require('../includes/config.php');
require('../structure/base.php');
require('../structure/forum.php');
require('../structure/forum.index.php');
require('../structure/database.php');
require('../structure/user.php');

$database = new database($db_host, $db_name, $db_user, $db_password);
$base = new base($database);
$user = new user($database);
$forum = new forum($database);
$forum_index = new forum_index($database);

$user->updateLastActive();

//set some variables that are used a lot throughout the page
$username = $user->getUsername($_COOKIE['user'], 2);
$rank = $user->getRank($username);
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

		<div id="picker" style="width: 440px;">
			<form method="post" action="jump.php">
				<ul class="flat" width>
					<li><a href="#"><img src="../img/forum-icon.png" /> My profile</a></li>
					<li><a href="search.php"><img src="../img/search_threads.png"> Search threads</a></li>
					<li>Jump to Thread: <input size="6" name="qfc" /></li>

					<li><input class="b" name="jump" value="Go" type="submit" /></li>
				</ul>

			</form>
		</div>
                <br/><br/>
		<div id="infopane">
                        <?php
                            $checkMute = $user->checkMute($username);
                        
                            if($checkMute) echo '<span style="color:red">You have received a forum mute. <br/>Your mute will expire in '. $checkMute .' hours. You cannot appeal it at this time.</span><br/><br/>';
                        ?>
			<span class="title"><?php echo $data['wb_name']; ?> Forums</span><br/>
			<div class="about">
					<ul class="flat">
						<li><a href="#"><img src="../img/forum/code_of_conduct.gif" /> Code of Conduct</a></li>						
						<li><a href="mods.php"><img src="../img/forum/forum_mods.gif" /> Forum Mods</a></li>
						<!-- <li><a href="rankings.php"><img src="../img/forum/rankings.png" /> Forum Rankings</a></li> -->
					</ul>
                                        <?php echo '<span style="color:white">'. $forum->getOnlineUsers() .' users online.</span><br/>'; ?>
			</div>
			<br />
			<!-- active users text -->
		</div>
		<br>

		<div id="breadcrumb">
			<a href="../index.php">Home</a> &gt; <a href="index.php"><?php echo $data['wb_name']; ?> Forums</a>
		</div>
		<div id="contentfrm">
			<table>
				<tbody>
					<tr>
						<td width="4%"></td>
						<td width="45%" class="title"></td>
						<td width="10%" class="title num">Threads</td>
						<td width="10%" class="title num">Posts</td>
						<td width="26%" class="title">Last Post</td>
					</tr>
                                        
                                        <?php
                                            //retrieve our categories
                                            foreach($forum_index->retrieveCategories($rank) as $category)
                                            {
                                                echo '<tr><td class="groupname" colspan="5">'. $category['title'] .'</td></tr>';

                                                foreach($forum_index->retrieveSubForums($category['id']) as $s_forum)
                                                {
                                                    if($forum->canView($s_forum['id'], $rank))
                                                    {
                                                        //retrieve the forum's statistics
                                                        $statistics = $forum_index->retrieveFStatistics($s_forum['id']);

                                                        //get appropriate icon for the section
                                                        if($s_forum['type'] == 4 || $s_forum['type'] == 5)
                                                        {
                                                            $icon = ($s_forum['type'] == 4) ? '<img src="../img/forum/icons/mod.png" border="0" alt="">' : '<img src="../img/forum/icons/a_mod.png" border="0" alt="">'; 
                                                        }
                                                        else
                                                        {
                                                            $icon = $forum->getIcon($s_forum['icon']);  
                                                        }


                                                        ?>

                                                            <tr class="border item">
                                                                <td class="icon lefttd"><a href="viewforum.php?forum=<?php echo $s_forum['id']; ?>"><?php echo $icon; ?></a></td>
                                                            <td class="frmname">
                                                                    <span class="bigtitle"><a href="viewforum.php?forum=<?php echo $s_forum['id']; ?>"><?php echo stripslashes($s_forum['title']); ?></a> </span><br />
                                                                    <span class="desc"><?php echo stripslashes($s_forum['description']); ?></span>
                                                            </td>
                                                                <td class="num"><?php echo number_format($statistics['thread_count']); ?></td>
                                                                <td class="num"><?php echo number_format($statistics['post_count']); ?></td>
                                                                <td class="righttd updated"><?php echo $statistics['last_post']; ?></td>
                                                            </tr>


                                                        <?php
                                                    }
                                                }

                                                echo '<tr><td class="groupend" colspan="5">&nbsp;</td></tr>';
                                            }
                                        ?>
                                </tbody>
			</table>
		</div>
                
                <div class="actions" id="top">
			<table>
				<tr>
					<td class="commands center" style="text-align: center;">
						<ul class="flat first-child">
                            <li>Threads: <span class="stats_value"><?php echo $forum->threadCount(); ?></span></li>
							<li>Posts: <span class="stats_value"><?php echo $forum->postCount(); ?></span></li>
							<li>Members: <span class="stats_value"><?php echo $base->userCount(); ?></span></li>
						</ul>
					</td>
				</tr>
			</table>
		</div>

		<div id="breadcrumb">
				<a href="../index.php">Home</a> &gt; <a href="index.php"><?php echo $data['wb_name']; ?> Forums</a>
		</div>
		
		<div class="tandc">
                    <?php echo $data['wb_foot']; ?>
		</div>
		</div>	
	</body>

</html>