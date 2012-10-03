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
			<?php
                            //all the recovery questions we're working with
                            $questions[] = array(); 
                            $questions[0] = 'What was your first pet\'s name?';
                            $questions[1] = 'Type in a code you can remember.';
                            $questions[2] = 'Where is your favorite vacation spot?';
                            $questions[3] = 'Who is your favorite author?';
                            $questions[4] = 'What color was your first bedroom?';
                            $questions[5] = 'What was your first car?';
                            
                            
                            if(!$user->isLoggedIn())
                            {
                                    echo 'You must be logged in to access this content.';
                            }
                            else
                            {
                                //check if they have recoveries set
                                //variable is set to x to save time typing
                                $x = $database->processQuery("SELECT `cancel`,`a1`,`a2`,`a3`,`a4`,`a5` FROM `recoveries` WHERE `userid` = ?", array($user->getUserId($_COOKIE['user'])), true);
                                
                                if(isset($_POST['answer']))
                                {
                                    if($database->getRowCount() == 0)
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
                                            //set their recovery questions
                                            $database->processQuery("INSERT INTO `recoveries` VALUES (null, ?, ?, ?, ?, ?, ?, ?, ?)", array($user->getUserId($_COOKIE['user']), 0, $answers[0], $answers[1], $answers[2], $answers[3], $answers[4], $answers[5]), false);
                                            
                                            ?>
                                                Your recovery questions have successfully been set. Please make sure you have written them down somewhere incase you need to use them. <a href="../index.php">Home</a>
                                            <?php
                                        }
                                    }
                                    else
                                    {
                                        //they already have recoveries, they can't set them
                                        ?>
                                            It appears you already have recovery questions set. You cannot change your current recovery questions until you successfully canceled your old ones. 
                                            If you wish to do so, you can cancel your current questions <a href="cancel.php">here</a>. Otherwise, you can <a href="../index.php">go home</a>.
                                        <?php
                                    }
                                }
                                elseif($database->getRowCount() == 0)
                                {
                                    ?>
                            
                                        <b>You currently don't have any recovery questions set. Please set them for your own security..</b>
                                        <br/><br/>
                                        <table>
                                            <fieldset class="question">
                                                <legend>Recovery Questions</legend>
                                                All recovery questions must be answered. These will be used to recover your account if you ever lose access. Keep this questions written down somewhere <b>safe</b>
                                                so you can remember them when it's necessary.
                                                <br/><br/>
                                                <b>Please Note:</b> Only the characters a-z, A-Z, 0-9, space, and $ are allowed.
                                            </fieldset>
                                            
                                            <form action="set_recov.php" method="POST">
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
                                                <tr><td><input type="submit" value="Set Recovery Questions"</td></tr>
                                                </table>
                                            </form>
                                        </table>
                                    <?php
                                }
                                else
                                {
                                    if($x[0]['cancel'] >= 1)
                                    {
                                        //time left until questions are cancelled
                                        $wait = $base->seconds_to_time($x[0]['cancel']-(time()-1209600));
                                        
                                        echo 'Your recovery questions are currently pending cancellation. <b>'. $wait .'</b> until your questions are deleted.';
                                    }
                                    else
                                    {
                                        //they have existing recoveries
                                        ?>
                                            <center><a href="cancel.php">Cancel Recovery Questions</a></center>
                                            Your recovery questions are set.<br/>
                                        <?php
                                    }
                                }
                            }
                        ?>
                        <center><img src="../img/kbase/scroll_spacer.gif" ></center>
			<div style="clear: both;"></div>
			</div>
			</div>
			</div>
			<img class="widescroll-bottom" src="../img/scroll/backdrop_765_bottom.gif" alt="" width="765" height="50" />
		<div class="tandc"><?php echo $data['wb_foot']; ?></div>
	</div>
	</body>
</html>
