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
if(!isset($_SESSION['age']) || !isset($_SESSION['country']) || !isset($_SESSION['username'])) $base->redirect('index.php');

//0: no error   
//1: error
$err = 0;

if(isset($_POST['agree']))
{
    $_SESSION['terms'] = true;
    $base->redirect('password.php');
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!-- saved from url=(0034)https://secure.rs-2007.com/create/ -->
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
<form action="terms.php" method="POST">
    <p style="text-align: center;">The username <span style="color: #ffbb22;"><?php echo $_SESSION['username']; ?></span> is currently available. To sign up to an account
    you must agree to to our Terms and Conditions.</p>
    <div style="text-align: justify">
                    <p align=right>Effective Date: June 28th 2012</p>
                    <p>
                            <b>Terms and Conditions</b>
                    </p>
                    <p>Usage:</p>
                    <ul>
                            <li>You will be using Asgarniax for entertainment, educational
                                    or research purposes only.</li>
                            <li>You will not in any way profit from our game server or
                                    website.</li>
                            <li>You will not break any of your local/national laws on
                                    this website and game server.</li>
                            <li>You will not break any laws that apply in the following
                                    countries:
                                    <ul>
                                            <li>Canada</li>
                                            <li>England</li>
                                            <li>United States of America</li>
                                    </ul></li>
                            <li>You intend on using this website in a non destructive
                                    manner.</li>
                    </ul>
            </div>
<div style="text-align: center">
<input type="checkbox" id="terms" name="agree" value="yes"> I will agree and abide by the Terms and Conditions
<br><input type="submit" value="Continue">
</div>
</div>
</form>
</td>
</tr>
</tbody>
</table>
</div>
</div>

<div class="tandc"><?php echo $data['wb_foot']; ?></div>	
</body></html>