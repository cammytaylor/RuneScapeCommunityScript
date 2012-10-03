<?php
require('../includes/config.php');
require('../structure/database.php');
require('../structure/base.php');
require('../structure/user.php');

$database = new database($db_host, $db_name, $db_user, $db_password);
$base = new base($database);
$user = new user($database);
$user->updateLastActive();

$username = $user->getUsername($_COOKIE['user'], 2);
?>
<link href="/lang/en/img/favicon.ico" rel="shortcut icon"><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns:IE>
<head>
<meta http-equiv="Expires" content="0">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache">
<meta name="MSSmartTagsPreventParsing" content="TRUE">
<title><?php echo $data['wb_title']; ?></title>
<link href="../css/basic-3.css" rel="stylesheet" type="text/css" media="all">
<link href="../css/main/title-5.css" rel="stylesheet" type="text/css" media="all">
<link rel="shortcut icon" href="../img/favicon.ico" />
<style>
.polltitlebground {
	margin-left: auto;
	margin-right: auto;
	padding: 4px;
	height: 120px;
	background-image: url('../img/polls/header.gif');
	background-repeat: no-repeat;
	background-position: 62px 15px;
}

.polltitleframe {
	width: 200px;
	margin-left: auto;
	margin-right: auto;
	padding: 4px;
	border: 2px solid #382418;
	background-color: black;
	margin-top: 50px;
}

.current {
	border: 2px solid #625437;
	padding: 10px;
	margin-left: 20px;
	margin-top: 5px;
	margin-right: 20px;
}

.previous {
	padding: 10px;
	margin-left: 20px;
	margin-top: 5px;
	margin-right: 20px;
}

img.explain {
	float: right;
	margin: 10px;
}
</style>
<?php include('../includes/google_analytics.html'); ?>
</head>

<body style="background-image:url('../img/backgrounds/bg-1.jpg');">
	<div id="body">

<div class="frame e">
			<span style="float: right;">
			<?php
				if($user->isLoggedIn())
				{
					echo '<a href="../index.php">Main Page</a> | <a href="../logout.php">Logout</a>';
				}
				else
				{
					echo '<a href="../index.php">Main Page</a> | <a href="../login.php">Login</a>';
				}
			?>
			</span>
			<div>
			<?php 
				if($user->isLoggedIn()) 
				{ 
					echo 'You are logged in as <span style="color: rgb(255, 187, 34);">'. $username .'</span>'; 
				} 
				else 
				{ 
					echo 'You are not logged in.'; 
				} 
			?>
			</div>
		</div><br /><br />

		<img class="widescroll-top" src="../img/scroll/backdrop_765_top.gif" alt="" width="765" height="50" />

		<div class="widescroll">
			<div class="widescroll-bgimg">
				<div class="widescroll-content">
					<div style="text-align: center;">

						<h1 style="font-size: 25px;">Welcome to the Community Polls</h1>
					</div>
					Community polls allow us here at <?php echo $data['wb_abbr']; ?> to make better decisions on content and choices with your votes/feedback
					The content can range from suggestions and changes on the forum to shiny new content in the server! Polls are released weekly!
					<p>Remember: voting in the polls allow us to receive useful information; we can narrow down
					possible updates to what the community wants just with your votes!</p>

					<h2 style="font-size: 20px;">Most recent poll</h2>
					<?php
                                                $polls = $database->processQuery("SELECT `id`,`closed`,`poll_title`,`date`,`poll_question` FROM `polls` ORDER BY `id` DESC LIMIT 8", array(), true);
						
						//get the very first article to make it current
						$x = 0;
						
						foreach($polls as $poll)
						{
							//if x == 0, we know it's the latest post
                                                        $vote_link = ($poll['closed'] == 1) ? '<b><span style="color:red">Voting for this poll has been closed.</span></b>' : '<a href="poll.php?id='. $poll['id'] .'">Click here to vote</a>';
                                                        
                                                        ?>
                                                            <div class="<?php echo ($x == 0) ? 'current' : 'previous'; ?>">
                                                            <b><?php echo $poll['poll_title'].' ('. $poll['date'] .')'; ?></b> <br><?php echo stripslashes($poll['poll_question']); ?>
                                                            <br /><br />
                                                            <?php echo $vote_link; ?><br /> <a href="results.php?id=<?php echo $poll['id']; ?>">Click here to view results</a>
                                                            </div>
                                                        <?php
                                                        
                                                        $x = 1;
						}
					?>
					<div style="clear: both;"></div>
				</div>
				
			</div>
		</div>

		<img class="widescroll-bottom" src="../img/scroll/backdrop_765_bottom.gif" alt="" width="765" height="50" />

                <div class="tandc"><?php echo $data['wb_foot']; ?></div>
	</div>
</body>
</html>