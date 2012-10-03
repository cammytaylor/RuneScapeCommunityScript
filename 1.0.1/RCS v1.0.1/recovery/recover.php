<?php 
require('../includes/config.php');
require('../structure/database.php');
require('../structure/base.php');
require('../structure/user.php');

$database = new database($db_host, $db_name, $db_user, $db_password);
$base = new base($database);
$user = new user($database);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns:IE>

<head>
<meta http-equiv="Expires" content="0">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache">
<meta name="MSSmartTagsPreventParsing" content="TRUE">
<title><?php echo $data['wb_title']; ?></title>
<link href="../css/basic-3.css" rel="stylesheet" type="text/css" media="all">
<link href="../css/main/title-5.css" rel="stylesheet" type="text/css" media="all">
<link href="../css/kbase/kbase-2.css" rel="stylesheet" type="text/css" media="all" />
<link rel="shortcut icon" href="../img/favicon.ico" />
<?php include('../includes/google_analytics.html'); ?>
<style>
fieldset {
	text-align: left;
	border: 2px solid #625437;
	width: 95%;
	position: relative;
	margin: 10px;
	padding-left: 10px;
        background-color:transparent;
}

legend {
	color: #625437;
	font-weight: bold;
	font-size: 15px;
}

</style>
<script type="text/javascript">
function goBack()
{
	window.history.back();
}	
</script>
</head>

		<div id="body">
		<div style="text-align: center; background: none;">
				<div class="titleframe e">
					<b>Set new recovery questions</b><br />
					<a href="../index.php">Main Menu</a>
				</div>
			</div>

			
			<img class="widescroll-top" src="../img/scroll/backdrop_765_top.gif" alt="" width="765" height="50" />
			<div class="widescroll">
			<div class="widescroll-bgimg">
			<div class="widescroll-content">
                        <center>
			<?php
                            //make sure user doesn't already have a recovery request submitted
                            $database->processQuery("SELECT * FROM `tracking` WHERE ". time() ." - `time` < 7200 AND `ip` = ? LIMIT 1", array($_SERVER['REMOTE_ADDR']), false);
                        
                            if($database->getRowCount() >= 1)
                            {
                                echo 'You can\'t use this service so soon.';
                            }
                            elseif(!isset($_POST['username']))
                            {
                                ?>
                                    <fieldset class="question">
                                        <legend>Recovery Notice</legend>
                                        Lost access to your account and you're attempting to recover it? Please proceed by entering in the username of the account you wish to recover. You will then be asked to 
                                        answer the questions you set for your account. An administrator will then review your request, and judge it from there.
                                    </fieldset>
                                    <br/>

                                    <form action="recover.php" method="POST">
                                        <input type="text" name="username" maxlength="12"><input type="submit" value="Continue">
                                    </form>
                                <?php
                            }
                            elseif(!$user->doesExist($_POST['username']))
                            {
                                echo 'No user exists with that username! <input type="button" value="Back" onclick="goBack()" />';
                            }
                            else
                            {
                                //all the recovery questions we're working with
                                $questions[] = array(); 
                                $questions[0] = 'What was your first pet\'s name?';
                                $questions[1] = 'Type in a code you can remember.';
                                $questions[2] = 'Where is your favorite vacation spot?';
                                $questions[3] = 'Who is your favorite author?';
                                $questions[4] = 'What color was your first bedroom?';
                                $questions[5] = 'What was your first car?';
                                
                                //extract data
                                $data = $database->processQuery("SELECT * FROM `recoveries` WHERE `userid` = ? LIMIT 1", array($user->getIdByName($_POST['username'])), false);
                                
                                if($database->getRowCount() == 0 || $user->getUsername($_COOKIE['user'], 2) == $_POST['username'])
                                {
                                    echo 'You can\'t do that for this user! <input type="button" value="Back" onclick="goBack()" />';
                                }
                                elseif(!isset($_POST['answer']))
                                {
                                    ?>
                            
                                        <fieldset class="question">
                                                <legend>Answer Recovery Question</legend>
                                                Now is the time when you wrote down your questions is useful. To the best of your ability, feel out all questions with the answers you 
                                                <br/><br/>
                                                <b>Please Note:</b> Only the characters a-z, A-Z, 0-9, space, and $ are allowed.
                                        </fieldset>
                                    
                                        <form action="recover.php" method="POST">
                                            <input type="hidden" name="username" value="<?php echo $_POST['username']; ?>">
                                            <table cellpadding="6" style="margin-left:auto;margin-right:auto;">
                                                <?php 
                                                    $i = 0;
                                                    foreach($questions as $question)
                                                    {
                                                        $i++;
                                                        ?> 
                                                            <tr><td><b>Question #<?php echo $i; ?></b></td></tr>
                                                            <tr>
                                                                <td><?php echo $question; ?></td>
                                                            </tr> 
                                                            <tr>
                                                                <td><input tpye="text" name="answer[]" maxlength="40"></td>
                                                            </tr>
                                                        <?php
                                                    }
                                                ?>
                                            <tr><td><input type="submit" value="Submit Recovery"</td></tr>
                                            </table>
                                        </form>
                                    
                                    <?php
                                }
                                else
                                {
                                    //validate answers
                                    $errors = array();
                                    $answers = $_POST['answer'];
                                    $i = 0;
                                    
                                    foreach($answers as $answer)
                                    {
                                        $i++;

                                        if(strlen($answer) < 3 || strlen($answer) > 35)
                                        {
                                            $errors[] = 'Question #'. $i .' must be at least three characters and no more than 26 characters.';
                                        }

                                        if(preg_match('#[^a-zA-Z0-9$ ]#', $answer))
                                        {
                                            $errors[] = 'Question #'. $i .' contains illegal characters.';
                                        }
                                    }

                                    if(count($errors) >= 1)
                                    {
                                        //back button
                                        ?> <center><input type="button" value="Back" onclick="goBack()" /></center> <?php

                                        //display errors
                                        foreach($errors as $error) { echo $error.'<br/>'; }
                                    }
                                    else
                                    {
                                        //generate a tracking ID
                                        $rand_hash = $base->randomString(11);
                                        $tracking_id = substr($rand_hash, 0, 3).'-'.substr($rand_hash, 4, 3).'-'.substr($rand_hash, 7, 3);
                                        
                                        //create the recovery request
                                        $database->processQuery("INSERT INTO `tracking` VALUES (null, ?, ?, NOW(), ?, ?, 0, ?, ?, ?, ?, ?, ?)", array($user->getIdByName($_POST['username']), $_SERVER['REMOTE_ADDR'], time(), $tracking_id, $answers[0],  $answers[1], $answers[2], $answers[3], $answers[4], $answers[5]), false);
                                        
                                        ?>
                                            <fieldset class="question">
                                                <legend>Success!</legend>
                                                You have successfully submitted a recovery request to the Asgarniax team. Within 24 hours, your request will be reviewed. To get the status of the administrator's review, 
                                                you can track the recovery by the tracking ID given to you below. <b>Write the tracking ID down so you don't forget.</b>
                                            </fieldset>
                                            <br/>
                                            <br/>
                                            <b>Tracking ID: <?php echo $tracking_id; ?></b>
                                        
                                        <?php
                                    }
                                }
                            }
                        ?>
                        <br/>
                        <br/>
                        <img src="../img/kbase/scroll_spacer.gif" >
                        </center>
			<div style="clear: both;"></div>
			</div>
			</div>
			</div>
			<img class="widescroll-bottom" src="../img/scroll/backdrop_765_bottom.gif" alt="" width="765" height="50" />
		<div class="tandc"><?php echo $data['wb_foot']; ?></div>
	</div>
	</body>
</html>
