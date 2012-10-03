<?php
require('includes/config.php');
require('structure/database.php');
require('structure/base.php');
require('structure/user.php');

$database = new database($db_host, $db_name, $db_user, $db_password);
$base = new base($database);
$user = new user($database);

//set some basic vars
$username = $user->getUsername($_COOKIE['user'], 2);
$rank = $user->getRank($username);

$user->updateLastActive();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns:IE>

<!-- LeeStrong Runescape Website Source --!>
<!-- Added by HTTrack --><meta http-equiv="content-type" content="text/html;charset=ISO-8859-1"><!-- /Added by HTTrack -->
<head>
<meta http-equiv="Expires" content="0">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache">
<meta name="MSSmartTagsPreventParsing" content="TRUE">
<title><?php echo $data['wb_title']; ?></title>
<link href="css/basic-3.css" rel="stylesheet" type="text/css" media="all">
<link href="css/main/title-5.css" rel="stylesheet" type="text/css" media="all">
<link rel="shortcut icon" href="img/favicon.ico" />
<?php include('includes/google_analytics.html'); ?>
</head>
<body>

<div id="body">
<div>
<div style="text-align: center; margin-bottom: 10px; position:relative;">
<img src="img/title2/rslogo3.gif" alt="RuneScape"><br>
<?php
	$database->processQuery("SELECT * FROM `users`", array(), false);
	
	echo 'There are currently '. number_format($database->getRowCount()) .' people registered!';
?>
</div>
</div>
<div class="left">
<fieldset class="menu rs">
<legend><?php echo $data['wb_abbr']; ?></legend>
<ul>
<?php
if($user->isLoggedIn()) 
{ 
        ?>
            <li class="i-create"><a href="logout.php">Logout</a></li>
            <li class="i-shop"><a href="donate.php">Donate</a></li>
        <?php
} 
else 
{ 
        ?>
            <li class="i-create"><a href="create/index.php">Create a free account (New user)</a></li>
            <li class="i-play"><a href="login.php">Login with an existing account...</a></li>
        <?php
}
?>
<li class="i-screen"><a href="screenshots.php">View daily Screenshot</a></li>
<li class="i-twitter"><a href="https://twitter.com/#!/Asgarniax">Twitter</a></li>
<li class="i-youtube"><a href="http://www.youtube.com/user/OfficialAsgarniax?feature=g-all-u">YouTube Channel</a></li>

</ul>
</fieldset>
<fieldset class="menu" style="background-image: url('img/title2/gamehelp.gif');">
<legend>Knowledge Base</legend>
<ul>
<li class="i-rules"><a href="kbase/rules.php">Rules</a></li>
<li class="i-bug"><a href="forums/">Report a bug/fault</a></li></ul>
</fieldset>
<fieldset class="menu poll">
				<legend>Latest Poll</legend>
                                <?php
                                    $poll = $database->processQuery("SELECT `poll_title`,`id` FROM `polls` ORDER BY `id` DESC LIMIT 1", array(), true);
                                    
                                    echo stripslashes($poll[0]['poll_title']);
                                ?>
				<ul>
					<li class="i-vote"><a href="polls/poll.php?id=<?php echo $poll[0]['id']; ?>">Vote in this poll</a></li>
					<li class="i-polls"><a href="polls/index.php">Poll Home</a></li>
				</ul>
</fieldset>
<fieldset class="menu web">
<legend>Website Features</legend>
<ul>
<li class="i-world"><a href="img/worldmap.jpeg">World map</a></li>
<li class="i-score"><a href="highscores/index.php">Hiscores</a></li>
<li class="i-forum"><a href="forums/index.php">Forums</a></li>
<li class="i-story"><a href="stories/">Stories and Letters</a></li>
</ul>
</fieldset>

<?php 
if($user->isLoggedIn())
{
	if($rank > 3)
	{
		?>
		
		<fieldset class="menu web">
		<legend>Administration</legend>
		<ul>
		<li class="i-crown"><a href="admin/index.php">Administration</a></li>
                <li class="i-crown"><a href="admin/recovery_requests.php">Recovery Apps</a></li>
		</ul>
		</fieldset>
		
		<?php
	}
        
	if($rank > 1)
	{
		?>
		
			<fieldset class="menu web">
			<legend>Moderation</legend>
			<ul>
			<li class="i-pmod"><a href="mod/fmod/index.php">Moderation</a></li>
			<li class="i-pmod"><a href="forums/viewforum.php?forum=49">View reports</a></li>
			</ul>
			</fieldset>
			
		<?php
	}
        
	?>
		<fieldset class="menu acc">
		<legend>Account Management</legend>
		<ul>
                <li class="i-msg"><a href="msgcenter/">View Message Centre <?php if($user->hasMail($username) >= 1) echo '<b>('.$user->hasMail($username).')</b>' ?></a></li>
		<li class="i-pw"><a href="change_password.php">Change your password</a></li>
                <li class="i-recset"><a href="recovery/set_recov.php">Set new recovery questions</a></li>
		<li class="i-appeal"><a href="offense.php">View Offenses</a></li>
		</ul>
		</fieldset>
	<?php
}
?>
                <fieldset class="menu rec">
		<legend>Account Recovery</legend>
		<ul>
                <li class="i-rec"><a href="recovery/recover.php">Recover a lost password</a></li>
		<li class="i-track"><a href="recovery/track.php">Track a recovery</a></li>
		</ul>
		</fieldset>
</div>
<div class="newscontainer">
<div class="buttons">
<a href="create/" class="button" id="button-left"><span class="lev1"></span><br style="line-height: 200%">Create a free account<br>(New user)</a>
<a href="login.php" class="button" id="button-right"><span class="lev1"></span><br style="line-height: 200%">Play <?php echo $data['wb_abbrev']; ?><br>(Existing user)</a>
</div>
<img class="narrowscroll-top" src="img/scroll/scroll457_top.gif" alt="" width="466" height="50">
<div class="narrowscroll-bg">
<div class="narrowscroll-bgimg">
<div class="narrowscroll-content">
<dl class="news scroll">
<dt style="text-align: center;"><img src="img/title2/recent_news.gif" alt="Recent News"></dt>
<?php
    
	//$query = mysql_query("SELECT id,icon,title,content,date FROM news ORDER BY id DESC LIMIT 4");
        $news = $database->processQuery("SELECT `id`,`icon`,`title`,`content`,`date` FROM `news` ORDER BY `id` DESC LIMIT 4", array(), true);

	if($database->getRowCount() < 1)
	{
		echo '<b>No news to display.</b>';
	}
	else
	{
		foreach($news as $news)
		{
		
			switch($news['icon'])
			{
				case 1:
				$img = '<img src="img/news/behind_the-scenes_2.gif">';
				break;
				
				case 2:
				$img = '<img src="img/news/fris_kingly_helm.gif">';
				break;
				
				case 3:
				$img = '<img src="img/news/shop_2.gif">';
				break;
				
				case 4:
				$img = '<img src="img/news/technical_3.gif">';
				break;
				
				case 5:
				$img = '<img src="img/news/world.gif">';
				break;
				
				case 6:
				$img = '<img src="img/news/green_cauldron.gif">';
				break;
				
				case 7:
				$img = '<img src="img/news/goblin.gif">';
				break;
				
				case 8:
				$img = '<img src="img/news/scroll.gif">';
				break;
				
				case 9:
				$img = '<img src="img/news/mail.gif">';
				break;
				
				case 10:
				$img = '<img src="img/news/bug_tracking_3.gif">';
				break;
				
				case 11:
				$img = '<img src="img/news/cabbage.gif">';
				break;
				
				case 12:
				$img = '<img src="img/news/coal.gif">';
				break;
				
				case 13:
				$img = '<img src="img/news/demon.gif">';
				break;
				
				case 14:
				$img = '<img src="img/news/macaroni_penguin.gif">';
				break;
				
				case 15:
				$img = '<img src="img/news/gold.gif">';
				break;
				
				case 16:
				$img = '<img src="img/news/high_priority.gif">';
				break;
				
				case 17:
				$img = '<img src="img/news/santa.gif">';
				break;
				
				case 18:
				$img = '<img src="img/news/scroll.gif">';
				break;
				
				case 19:
				$img = '<img src="img/news/shifter.gif">';
				break;
                            
                                case 20:
				$img = '<img src="img/news/squirrel.gif">';
				break;
                            
                                case 21:
				$img = '<img src="img/news/pohousing.gif">';
				break;
                            
                                case 22:
				$img = '<img src="img/news/presents.gif">';
				break;
                            
                                case 23:
				$img = '<img src="img/news/clock.gif">';
				break;
			}
		
			?>
			
			<dt>&nbsp;</dt>
			<dt><span class="newsdate">
                            <?php echo $news['date']; ?></span><?php echo htmlentities($news['title']); if($rank > 3) { ?> <a href="admin/edit_news.php?id=<?php echo $news['id']; ?>">[Edit]</a> <?php } ?></dt>
			<dd>
			<table width="100%"><tr>
			<td style="text-align: justify; vertical-align: top;"><?php echo stripslashes($base->shorten($news['content'], 150, true)).'. . .'; ?></td>
			<td style="padding-left: 1em; text-align: right; vertical-align: top;">
			<?php echo $img; ?>
			</td></tr></table>
			<div style="margin-top: 0.5em;"><a href="news/viewarticle.php?id=<?php echo $news['id']; ?>">Read more...</a></div>
			</dd> 
			
			<?php
		}
	}
?>
</dd> </dl>
<div class="right" style="margin-bottom: 0.5em"><a href="news/index.php">Browse the news archives</a></div>
</div>
</div>
</div>
<img class="narrowscroll-bottom" src="img/scroll/scroll457_bottom.gif" alt="" width="466" height="50">
</div>
<div class="tandc"><?php echo $data['wb_foot']; ?></div>
</div>

</body>
<!-- Added by HTTrack --><meta http-equiv="content-type" content="text/html;charset=ISO-8859-1"><!-- /Added by HTTrack -->
</html>