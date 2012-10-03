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

if(isset($_GET['id']) && isset($_GET['action']))
{
        $database->processQuery("UPDATE `tracking` SET `status` = ? WHERE `id` = ? LIMIT 1", array(($_GET['action'] == 1) ? 1 : 2, $_GET['id']), false);
        $base->appendToFile('../forums/logs.txt', array($username.($_GET['action'] == 1) ? 'accepted' : 'denied'.' a recovery request'));
}
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
<link href="../css/register-1.css" rel="stylesheet" type="text/css" media="all">
<link rel="shortcut icon" href="../img/favicon.ico" />
<style>
div.topnav {
	text-align: center;
	margin-bottom: 5px;
}

div.topnav img {
	vertical-align: bottom;
}

div.bottomnav {
	text-align: center;
	margin-top: 10px;
}

div.bottomnav img {
	vertical-align: bottom;
}

.newstitlebground {
	margin-left: auto;
	margin-right: auto;
	padding: 4px;
	width: 759px;
	height: 130px;
	background: url('../img/news/header.gif') no-repeat;
}

.newstitleframe {
	width: 165px;
	margin-left: auto;
	margin-right: auto;
	padding: 4px;
	border: 2px solid #382418;
	background-color: black;
	margin-top: 50px;
}
</style>
<?php include('../includes/google_analytics.html'); ?>
<script type="text/javascript">
function goBack()
{
	window.history.back();
}	
</script>
</head>
	<div id="body">

			<div style="text-align: center;">
			<div class="newstitlebground">
				<div class="newstitleframe">
					<b>Website News</b><br /> <a
						href="../index.php">Main Menu</a> - <a href="index.php">News List</a>
				</div>
			</div>
		</div>

		<img class="widescroll-top" src="../img/scroll/backdrop_765_top.gif" alt="" width="765" height="50" />
		<div class="widescroll">
			<div class="widescroll-bgimg">

				<div class="widescroll-content">
                                    <div id="black_fields">
                                        Recovery request <b><?php echo ($_GET['action'] == 1) ? 'accepted' : 'denied'; ?></b>...<br/><br/>
										<a href="recovery_requests.php">Click here to go back.</a>
                                    </div>
				</div>
			</div>
		</div>
		<img class="widescroll-bottom" src="../img/scroll/backdrop_765_bottom.gif" alt="" width="765" height="50" />	

		<div class="tandc"><?php echo $data['wb_foot']; ?></div>
</body>
</html>
