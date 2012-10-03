<?php
require('../includes/config.php');
require('../structure/database.php');
require('../structure/base.php');
require('../structure/user.php');
require('../structure/poll.php');

$database = new database($db_host, $db_name, $db_user, $db_password);
$base = new base($database);
$user = new user($database);
$poll = new poll($database);
$user->updateLastActive();

$username = $user->getUsername($_COOKIE['user'], 2);
$rank = $user->getRank($username);
$id = $_GET['id'];

if(!$poll->canVote($id, $username) || !$poll->pollExists($id)) $base->redirect('index.php');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns:IE>
<head>
<meta http-equiv="Expires" content="0">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache">
<meta name="MSSmartTagsPreventParsing" content="TRUE">
<title><?php echo $data['wb_title']; ?></title>
<link href="../css/main.css" rel="stylesheet" type="text/css" media="all">
<link rel="shortcut icon" href="../img/favicon.ico" />
<?php include('../includes/google_analytics.html'); ?>
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

img#poll_bg {
	height: 15px;
	max-width: 400px;
}

img#poll_left {
	height: 15px;
	width: 17px;
	float: left;
}

img#poll_right {
	height: 15px;
	width: 8px;
}
</style>
<style type="text/css">
fieldset {
	text-align: left;
	border: 2px solid #625437;
	width: 95%;
	position: relative;
	margin: 10px;
	padding-left: 10px;
}

legend {
	color: #625437;
	font-weight: bold;
	font-size: 15px;
}

td {
	color: black;
	padding: 2px;
}

.results {
	background-image: url("../img/polls/poll_middle_cap.gif");
	background-repeat: repeat-x;
	height: 15px;
	float: left;
}

.clear {
	clear: both;
}

img.explain {
	float: right;
	margin: 10px;
}

td.shield {
	background: url('../img/polls/shield.gif') no-repeat;
	padding-left: 20px;
}

* html .floatLeft {
	float: left;
	margin: 0 -3px;
	ma\rgin: 0; /* ie5 3px margin hack */
}
</style>
</head>
	<body style="background-image: url('../img/backgrounds/bg-1.jpg');">

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
</div><br />
		<div style="text-align: center;">
			<div class="polltitlebground">
				<div class="polltitleframe">

					<b>Poll</b><br> <a href="../index.php">Main Menu</a> - <a href="index.php">Poll Home</a>
				</div>
			</div>
		</div>

		<img class="widescroll-top" src="../img/scroll/backdrop_765_top.gif" alt="" width="765" height="50" />

		<div class="widescroll">

			<div class="widescroll-bgimg">
				<div class="widescroll-content">
                                     <?php 
                                        $poll_data = $database->processQuery("SELECT `poll_title`,`poll_question` FROM `polls` WHERE `id` = ? LIMIT 1", array($id), true);
                                        
                                        //get the open/close poll option for administrators
                                        if($rank > 3) $manage = ($poll->isClosed($id)) ? '<a href="togglestatus.php?id='. $id .'">[ Open ]</a>' : '<a href="togglestatus.php?id='. $id .'">[ Close ]</a>';
                                        
                                      ?>
                                    
                                            <h2 style="font-size: 20px;">
                                                <?php echo stripslashes($poll_data[0]['poll_title']); echo ' '.$manage; ?>
                                            </h2>
                                            <font size="2px">
                                                <?php echo stripslashes($poll_data[0]['poll_question']); ?>
                                            </font>
                                            <br/>
                                            <br/>
                                            <b>
                                                Total votes:	<?php echo $poll->getNumOfVotes($id); ?>
                                            </b>
                                            <br />
                                            <form action="addvote.php" method="POST">
                                                <input type="hidden" name="id" value="<?php echo $id; ?>">
                                                <fieldset class="question">
                                                <legend><?php echo $poll_data[0]['poll_title']; ?></legend>
                                                <table border="0" width="100%">
                                                        <?php 
                                                                //query poll options for users to choose
                                                                $options = $database->processQuery("SELECT `id`,`option` FROM `poll_options` WHERE `belongs` = ? ORDER BY `id` ASC", array($id), true);
                                                        
                                                                foreach($options as $option)
                                                                {

                                                                        ?>

                                                                        <tr>
                                                                        <td class='shield' style='width: auto;'>&nbsp;<?php echo stripslashes($option['option']); ?></td>
                                                                        <td style='width: 20%;'><input type="radio" name="option" value="<?php echo $option['id']; ?>"></td>
                                                                        <td>
                                                                                <div id='poll'></div>
                                                                        </td>
                                                                        </tr>

                                                        <?php } ?>
                                                </table>
                                                </fieldset>
                                                <center><input type="submit" value="Submit Vote"></center>
                                            </form>
                                            <div style="clear: both;"></div>

				</div>
			</div>
		</div>
		<img class="widescroll-bottom" src="../img/scroll/backdrop_765_bottom.gif" alt="" width="765" height="50" />

		<div class="tandc"><?php echo $data['wb_foot']; ?></div>
	</div>
</body>
</html>
