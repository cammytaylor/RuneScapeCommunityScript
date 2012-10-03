<?php 
require('../includes/config.php');
require('../structure/database.php');
require('../structure/user.php');

$database = new database($db_host, $db_name, $db_user, $db_password);
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
                            if(!isset($_GET['tracking_id']))
                            {
                                ?>
                                
                                    <fieldset class="question">
                                            <legend>Track</legend>
                                            Please enter in the the tracking ID you were given.
                                    </fieldset>
                                    <form action="track.php" method="GET">
                                        <input type="text" name="tracking_id" maxlength="12"><input type="submit" value="Track">
                                    </form>
                                <?php
                            }
                            else
                            {
                                $info = $database->processQuery("SELECT `status`,`ip`,`account` FROM `tracking` WHERE `tracking_id` = ?", array($_GET['tracking_id']), true);
                                
                                if($database->getRowCount() == 0)
                                {
                                    echo 'No recovery request exists with this tracking ID. <input type="button" value="Back" onclick="goBack()" />';
                                }
                                elseif($_SERVER['REMOTE_ADDR'] != $info[0]['ip'])
                                {
                                    echo 'This isn\'t yours to check. <input type="button" value="Back" onclick="goBack()" />';
                                }
                                elseif($info[0]['status'] == 1)
                                {
                                    
                                    if(!isset($_POST['password']) || !isset($_POST['confirm']))
                                    {
                                        ?>
                                    
                                            <fieldset class="question">
                                                <legend>Accepted</legend>
                                                Your recovery was accepted. Please enter in the new details of your account.
                                            </fieldset>

                                            <form action="track.php?tracking_id=<?php echo $_GET['tracking_id']; ?>" method="POST">
                                                <table>
                                                    <tr><td>New Password</td><td><input type="password" name="password"></td></tr>
                                                    <tr><td>Confirm Password</td><td><input type="password" name="confirm"></td></tr>
                                                    <tr><td><input type="submit" value="Update Password"></td></tr>
                                                </table>
                                            </form>
                            
                                        <?php
                                    }
                                    elseif($_POST['password'] != $_POST['confirm'])
                                    {
                                        echo 'The two passwords didn\'t match! <input type="button" value="Back" onclick="goBack()" />';
                                    }
                                    else
                                    {
                                        //generate a salt
                                        $salt = substr(hash(sha256, sha1(time())), 10);
                                        $password = $salt.hash(sha256, md5(sha1($_POST['password']))).substr($salt, 0, -51);
                                        
                                        //update user password
                                        $database->processQuery("UPDATE `users` SET `password` = ? WHERE `id` = ? LIMIT 1", array($password, $user->getIdByName($info[0]['account'])), false);
                                        
                                        //delete the recovery request
                                        $database->processQuery("DELETE FROM `tracking` WHERE `tracking_id` = ?", array($_GET['tracking_id']), false);
                                        
                                        echo 'Your account has successfully been updated. <a href="../index.php">Home</a>';
                                    }
                                }
                                elseif($info[0]['status'] == 2)
                                {
                                    echo 'Your request has been <b>denied</b>.';
                                }
                                else
                                {
                                    echo 'Your request is still <b>pending</b>.';
                                }
                            }
                        ?>
                        <br/><br/>
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
