<?php 
session_start();

require('../includes/config.php');
require('../structure/database.php');
require('../structure/base.php');
require('../structure/user.php');
require('../structure/user.register.php');

$database = new database($db_host, $db_name, $db_user, $db_password);
$base = new base($database);
$user = new user($database);
$register = new user_register($database);

//preform basic checks before loading page
if($user->isLoggedIn()) $base->redirect('../index.php');
if(!isset($_SESSION['age']) || !isset($_SESSION['country']) || !isset($_SESSION['username']) || !isset($_SESSION['terms']) || !isset($_SESSION['salt']) || !isset($_SESSION['password'])) $base->redirect('index.php');

$cookie = $register->generateCookie();

//create new account
$database->processQuery("INSERT INTO `users` VALUES (null, ?, ?, ?, ?, 'nope.avi', ?, 1, ?, '', '', ?, 0, 0, 1, 0, 0, ?)", array($_SESSION['username'], $_SESSION['password'], $_SESSION['age'], $_SESSION['country'], date('M-d-Y'), $_SERVER['REMOTE_ADDR'], $cookie, $_SERVER['REMOTE_ADDR']), false);

$username = $_SESSION['username'];
$register->clear();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<meta http-equiv="Expires" content="0">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache">
<meta name="MSSmartTagsPreventParsing" content="TRUE">
<link rel="shortcut icon" href="../img/favicon.ico" />
<title><?php echo $data['wb_title']; ?></title>
<link href="../css/basic-3.css" rel="stylesheet" type="text/css" media="all">
<link href="../css/register-1.css" rel="stylesheet" type="text/css" media="all">
<?php include('../includes/google_analytics.html'); ?>
<style type="text/css">
#emailSection, #ageSection, #usernameSection, #termsSection, #passwordSection {
        visibility: hidden;
}
</style>
</head>

<body>
<div id="body">
<div style="text-align: center; background: none;">
        <div class="titleframe e">
                <b>Create a free account (New user)</b><br>
                <a href="../index.php">Main Menu</a>
        </div>
</div>
<br>

<div id="reg">
        <table cellspacing="0" cellpadding="0">
                <tbody><tr>
                        <td id="ageReg" class="box">&nbsp; Age and Location &nbsp;</td>
			<td id="usernameReg" class="box">&nbsp; Choose a Username &nbsp;</td>
			<td id="termsReg" class="cur">&nbsp; Terms and Conditions &nbsp;</td>
			<td id="passwordReg" class="box">&nbsp; Choose a Password &nbsp;</td>
			<td id="finishReg" class="box">&nbsp; Finish &nbsp;</td>
                </tr>
        </tbody></table>
</div>

<div class="frame wide_e">
<table width="100%">
<tbody>
<tr>
<td style="text-align: justify; vertical-align: top;">
Your account <b><?php echo $username; ?></b> has now been created with the password you have chosen. We recommend you make a note of it on a bit of paper and keep it somewhere really safe, in case you forget it.<br/><br/>
<center>Click below to go back to the website homepage:<br/><a href="../index.php">Home</a></center>
</td>
</tr>
</tbody>
</table>
</div>
</div>

<div class="tandc"><?php echo $data['wb_foot']; ?></div>
</body></html>