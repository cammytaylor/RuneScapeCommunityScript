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
if(!isset($_SESSION['age']) || !isset($_SESSION['country'])) $base->redirect('index.php');

//0: no error   
//1: error
$err = 0;

if(isset($_POST['username']))
{
    if(!$register->validateUsername(trim($_POST['username'])))
    {
        $err = 1;
    }
    else
    {
        $_SESSION['username'] = $_POST['username'];
        $base->redirect('terms.php');
    }
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
			<td id="usernameReg" class="cur">&nbsp; Choose a Username &nbsp;</td>
			<td id="termsReg" class="box">&nbsp; Terms and Conditions &nbsp;</td>
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
<?php
    if($err == 1)
    {
        ?>
    
            <form action="username.php" method="POST">
            <center>
            This username is not available.
            <br/><br/>
            Please enter in a different username:
            <br/><br/>
            <input name="username" autocomplete="off" maxlength="12">
            <input type="submit" value="Continue"></center></form>
    
        <?php
    }
    else
    {
        ?>
    
            <form action="username.php" method="POST">
            <table width="100%" cellpadding="5">
            <tbody><tr>
            <td align="left" colspan="2">
            Usernames can be a maximum of 12 characters long and may contain letters, numbers and underscores.<br><br>
            You may use your RuneScape username here, however it is not strongly advised.<br><br>
            It should not be offensive or break our <a href="terms.php">Terms and Conditions</a>.<br><br>
            Underscores in usernames are translated into spaces and first letters are capitalised. For example the username <b>red_rooster</b> would appear as <b>Red Rooster</b>.
            </td>
            </tr>
            <tr>
            <td align="right" width="50%">
            Desired Username:
            </td>
            <td align="left" width="50%">
            <input id="username" name="username" autocomplete="off" maxlength="12">
            </td>
            </tr>
            <tr>
            <td width="50%"></td>
            <td width="50%">
            </td>
            </tr>
            <tr>
            <td align="center" colspan="2">
            <input type="submit" value="Continue">
            </td>
            </tr>
            </tbody></table>
            </div>
    
        <?php
    }
?>
</td>
</tr>
</tbody>
</table>
</div>
</div>

<div class="tandc"><?php echo $data['wb_foot']; ?></div>	
</body></html>