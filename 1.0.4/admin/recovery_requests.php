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
<link href="../css/main/title-5.css" rel="stylesheet" type="text/css" media="all">
<link href="../css/kbase-2.css" rel="stylesheet" type="text/css" media="all" />
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
                            <b>Administration</b><br> <a href="../index.php" class=c>Main Menu</a> - <a href="index.php">Home</a>
                        </div>
                        </div>
                        <br/>
                        <br/>
                        <div class="titleframe e" style="text-align:left; color:white; width:750px; margin-left:auto; margin-right:auto;">
						Recovery requests are displayed one at a time to keep the steady flow.<br/>
                            <?php

								//query details
								$tracking = $database->processQuery("SELECT `id`,`ip`,`date`,`account`,`a1`,`a2`,`a3`,`a4`,`a5`,`a6` FROM `tracking` WHERE `status` = 0 ORDER BY `id` ASC LIMIT 0,1", array(), true);
								
								if($database->getRowCount() == 0)
								{
									?>
										<center><h2>No tracking attempts at this time.</h2></center>
									<?php
								}
								else
								{
									$recovery = $database->processQuery("SELECT `a1`,`a2`,`a3`,`a4`,`a5`,`a6` FROM `recoveries` WHERE `userid` = ?", array($tracking[0]['account']), true);
								
									?>
									
										<center>
										<table width="55%">
											<tr><td style="color:red;">Username</td><td><?php echo $user->dName($user->getNameById($tracking[0]['account'])); ?></td></tr>
											<tr><td style="border-bottom:1px solid white;">I.P</td><td style="border-bottom:1px solid white;"><?php echo $tracking[0]['ip']; ?></td></tr>
											
											<!-- QUESTION ONE -->
											
											<tr>
												<td>Question #1</td>
												<td>What was your first pet's name?</td>
											</tr>
											<tr>
												<td>Actual Answer</td>
												<td><?php echo $recovery[0]['a1']; ?></td>
											</tr>
											<tr>
												<td style="border-bottom:1px dotted yellow;">Supplied Answer</td>
												<td style="border-bottom:1px dotted yellow;"><?php echo $tracking[0]['a1']; ?></td>
											</tr>
											
											<!-- QUESTION TWO -->
											
											<tr>
												<td>Question #2</td>
												<td>Type in a code you can remember.</td>
											</tr>
											<tr>
												<td>Actual Answer</td>
												<td><?php echo $recovery[0]['a2']; ?></td>
											</tr>
											<tr>
												<td style="border-bottom:1px dotted yellow;">Supplied Answer</td>
												<td style="border-bottom:1px dotted yellow;"><?php echo $tracking[0]['a2']; ?></td>
											</tr>
											
											<!-- QUESTION THREE  -->
											
											<tr>
												<td>Question #3</td>
												<td>Where is your favorite vacation spot?</td>
											</tr>
											<tr>
												<td>Actual Answer</td>
												<td><?php echo $recovery[0]['a3']; ?></td>
											</tr>
											<tr>
												<td style="border-bottom:1px dotted yellow;">Supplied Answer</td>
												<td style="border-bottom:1px dotted yellow;"><?php echo $tracking[0]['a3']; ?></td>
											</tr>
											
											<!-- QUESTION FOUR  -->
											
											<tr>
												<td>Question #4</td>
												<td>Who is your favorite author?</td>
											</tr>
											<tr>
												<td>Actual Answer</td>
												<td><?php echo $recovery[0]['a4']; ?></td>
											</tr>
											<tr>
												<td style="border-bottom:1px dotted yellow;">Supplied Answer</td>
												<td style="border-bottom:1px dotted yellow;"><?php echo $tracking[0]['a4']; ?></td>
											</tr>
											
											<!-- QUESTION FIVE  -->
											
											<tr>
												<td>Question #5</td>
												<td>What color was your first bedroom?</td>
											</tr>
											<tr>
												<td>Actual Answer</td>
												<td><?php echo $recovery[0]['a5']; ?></td>
											</tr>
											<tr>
												<td style="border-bottom:1px dotted yellow;">Supplied Answer</td>
												<td style="border-bottom:1px dotted yellow;"><?php echo $tracking[0]['a5']; ?></td>
											</tr>
											
											<!-- QUESTION SIX  -->
											
											<tr>
												<td>Question #6</td>
												<td>What was your first car?</td>
											</tr>
											<tr>
												<td>Actual Answer</td>
												<td><?php echo $recovery[0]['a6']; ?></td>
											</tr>
											<tr>
												<td style="border-bottom:1px dotted yellow;">Supplied Answer</td>
												<td style="border-bottom:1px dotted yellow;"><?php echo $tracking[0]['a6']; ?></td>
											</tr>
											
											<tr>
												<td><a href="recovery_takeaction.php?action=1&id=<?php echo $tracking[0]['id']; ?>">Accept</a></td><td><a href="recovery_takeaction.php?action=2&id=<?php echo $tracking[0]['id']; ?>">Deny</a></td>
											</tr>
										</table>
									</center>
									
									<?php
								}
							?>
                    </div>
		</div>	

		<div class="tandc"><?php echo $data['wb_foot']; ?></div>
</body>
</html>