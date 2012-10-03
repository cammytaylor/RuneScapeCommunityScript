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
<link href="../css/kbase-2.css" rel="stylesheet" type="text/css" media="all" />
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
                            if(!$user->isLoggedIn())
                            {
                                    echo 'You must be logged in to access this content.';
                            }
                            else
                            {
                                $info = $database->processQuery("SELECT `cancel` FROM `recoveries` WHERE `userid` = ?", array($user->getUserId($_COOKIE['user'])), true);
                                
                                if($database->getRowCount() == 0)
                                {
                                    echo 'You need to have recovery questions in order to cancel them. You can <a href="set_recov.php">set them</a> or <a href="../index.php">go home</a>.';
                                }
                                elseif($info[0]['cancel'] >= 1)
                                {
                                    echo 'Your questions have already been set to cancel.';
                                }
                                elseif(isset($_GET['confirm']))
                                {
                                    //cancel the recovery questions
                                    $database->processQuery("UPDATE `recoveries` SET `cancel` = ? WHERE `userid` = ?", array(time(), $user->getUserId($_COOKIE['user'])), false);
                                    
                                    echo 'Your recovery questions have successfully been set to cancel. <a href="set_recov.php">Return</a>';
                                }
                                else
                                {
                                    ?> 
                            
                                        Are you sure you wish to cancel your recovery questions? Once you confirm this action, you're recovery questions will be set for two more weeks. After that, they'll be
                                        deleted. This action can be canceled by clicking "Set recovery questions" on the website homepage.
                                        <br/><br/>
                                        <a href="cancel.php?confirm=1">I wish to continue</a> | <a href="set_recov.php">I wish to go back</a>
                            
                                    <?php
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
