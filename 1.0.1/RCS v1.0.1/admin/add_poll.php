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
$rank = $user->getRank($username);

if($rank < 4) $base->redirect('../index.php');
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
<link rel="shortcut icon" href="../img/favicon.ico" />
<?php include('../includes/google_analytics.html'); ?>
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
                            <b>Administration</b><br> <a href="../index.php" class=c>Main Menu</a>
                        </div>
                        </div>

		<img class="widescroll-top" src="../img/scroll/backdrop_765_top.gif" alt="" width="765" height="50" />
		<div class="widescroll">
			<div class="widescroll-bgimg">

				<div class="widescroll-content">
                                    <?php
                                        if(!ctype_digit($_POST['amount']))
                                        {
                                            ?>
                                                <form action="add_poll.php" method="POST">
                                                    <font size="1">Enter in the amount of options you wish for there to be.</font><br/>
                                                    <input type="text" name="amount" class="button"> <input type="submit" value="Continue" class="button">
                                                </form>
                                            <?php
                                        }
                                        elseif(!isset($_POST['title']) || !isset($_POST['question']) || !isset($_POST['options']))
                                        {
                                            ?>

                                                <form action="add_poll.php" method="POST">
                                                    <table>
                                                    <input type="hidden" name="amount" value="<?php echo $_POST['amount']; ?>">
                                                    <tr><td>Poll Title</td><td><input type="text" name="title" maxlength="35"></td></tr>
                                                    <tr><td>Poll Question</td><td><textarea name="question" cols="45" rows="10" maxlength="250"></textarea></td></tr>
                                                    <?php
                                                        for($x = 0; $x < $_POST['amount']; $x++)
                                                        {
                                                            ?>
                                                                <tr><td>Option <?php echo $x; ?>:</td><td><input type="text" name="options[]"> Leave blank to delete</td></tr>
                                                            <?php
                                                        }
                                                    ?>
                                                    <tr><td><input type="submit" value="Submit"></td></tr>
                                                    </table>
                                                </form>

                                            <?php
                                        }
                                        elseif(strlen($_POST['title']) > 35 && strlen($_POST['question']) > 250)
                                        {
                                            echo 'Please don\'t bypass the character limit. <input type="button" value="Back" onclick="goBack()" />';
                                        }
                                        else
                                        {
                                            //insert poll
                                            $database->processQuery("INSERT INTO `polls` VALUES (null, ?, ?, ?, 0)" , array($_POST['title'], $_POST['question'], date('M-d-Y')), false);
                                            $id = $database->getInsertId();
                                            
                                            //insert all the poll options
                                            foreach($_POST['options'] as $option)
                                            {
                                                if(strlen($option) > 1) $database->processQuery("INSERT INTO `poll_options` VALUES (null, ?, ?)", array($id, $option), false);
                                            }
                                            
                                            echo 'Your poll has successfully been created. View it <a href="../polls/poll.php?id='. $id .'">here</a> or <a href="index.php">return to ACP home</a>.';
                                        }
                                    ?>
				</div>
			</div>
		</div>
		<img class="widescroll-bottom" src="../img/scroll/backdrop_765_bottom.gif" alt="" width="765" height="50" />	

		<div class="tandc"><?php echo $data['wb_foot']; ?></div>
</body>
</html>
