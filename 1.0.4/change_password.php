<?php 
require('includes/config.php');
require('structure/database.php');
require('structure/base.php');
require('structure/user.php');

$database = new database($db_host, $db_name, $db_user, $db_password);
$base = new base($database);
$user = new user($database);

if(!$user->isLoggedIn())
{
	$content = '<center>You need to be logged in to access this page. <a href="index.php">Home</a></center>';
}
else
{
	if(isset($_POST['old_password']) && isset($_POST['password']) && isset($_POST['confirm_password']))
	{
		//get the users current (referred to as "old") password
		$query = $database->processQuery("SELECT `password` FROM `users` WHERE `id` = ? LIMIT 1", array($user->getUserId($_COOKIE['user'])), true);
		
		$old_password = substr(substr($query[0]['password'], 54), 0, -3);
		$entered_password = hash(sha256, md5(sha1($_POST['old_password'])));
		$confirm_password = hash(sha256, md5(sha1($_POST['confirm_password'])));
		$password = hash(sha256, md5(sha1($_POST['password'])));
		
		if($confirm_password != $password)
		{
			$content = '<center>The two passwords didn\'t match!</center>';
		}
		elseif($entered_password != $old_password)
		{
			$content = '<center>The password you entered doesn\'t match your current password.</center>';
		}
		elseif(strlen($_POST['password']) > 20 || strlen($_POST['password']) < 5)
		{
			$content = '<center>You\'re password cannot be greater than twenty characters; you\'re password also cannot be smaller than five characters.</center>';
		}
		else
		{
			//add salt
			$salt = substr(hash(sha256, sha1(time())), 10);
			$password = $salt.hash(sha256, md5(sha1($_POST['password']))).substr($salt, 0, -51);
		
			//update their password
			$database->processQuery("UPDATE `users` SET `password` = ? WHERE `id` = ? LIMIT 1", array($password, $user->getUserId($_COOKIE['user'])), false);
		
			$content = '<center>Your password has successfully been changed! <a href="index.php">Home</a></center>';
		}
		
	}
	else
	{
		$content = '
		<p>Use this form if you want to change the password you use to log into your account. If you do not wish to set a new password at this time, click the link at the top-right of this page.</p>
		<p><b>DO NOT</b> use your <a href="http://runescape.com">RuneScape</a> password here.</p>

		<p>Please note that passwords must be between 5 and 20 characters long. We recommend you use a mixture of numbers and letters in your password to make it harder for someone to guess.</p>
		<form action="change_password.php" method="POST">
		<table>
		<tr>
			<td style="text-align:right;padding-right:10px;padding-top:3px;"><label for="oldpassword">Enter your <b>current</b> password:</label></td>
			<td><input type="password" name="old_password" maxlength="20" size="20"></td>
		</tr>
		<tr>
			<td style="text-align:right;padding-right:10px;padding-top:3px;"><label for="password1">Enter your <b>new</b> password:</label></td>
		<td><input type="password" name="password" maxlength="20" size="20"></td>
			</tr>
		<tr>
			<td style="text-align:right;padding-right:10px;padding-top:3px;"><label for="password2">Confirm your <b>new</b> password:</label></td>
			<td><input type="password" name="confirm_password" maxlength="20" size="20"></td>
		</tr>
		<tr>
			<td colspan="2" align="right">
				<input type="submit" value="Submit Request">
			</td>
		</tr>
		</table><br />
		</form>';
	}
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
<link href="css/basic-3.css" rel="stylesheet" type="text/css" media="all">
<link href="css/main/title-5.css" rel="stylesheet" type="text/css" media="all">
<link href="css/kbase-2.css" rel="stylesheet" type="text/css" media="all" />
<link rel="shortcut icon" href="img/favicon.ico" />
<?php include('includes/google_analytics.html'); ?>
</head>

		<div id="body">
		<div style="text-align: center; background: none;">
				<div class="titleframe e">
					<b>Change your password</b><br />
					<a href="index.php">Main Menu</a>
				</div>
			</div>

			
			<img class="widescroll-top" src="img/scroll/backdrop_765_top.gif" alt="" width="765" height="50" />
			<div class="widescroll">
			<div class="widescroll-bgimg">
			<div class="widescroll-content">
			<?php echo $content; ?>
			<div style="clear: both;"></div>
			</div>
			</div>
			</div>
			<img class="widescroll-bottom" src="img/scroll/backdrop_765_bottom.gif" alt="" width="765" height="50" />
		<div class="tandc"><?php echo $data['wb_foot']; ?></div>
	</div>
	</body>
</html>
