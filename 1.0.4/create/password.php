<?php 
session_start();

require('../includes/config.php');
require('../structure/database.php');
require('../structure/base.php');
require('../structure/user.php');
require('../structure/user.register.php');
require('../includes/recaptchalib.php');

$database = new database($db_host, $db_name, $db_user, $db_password);
$base = new base($database);
$user = new user($database);

//preform basic checks before loading page
if($user->isLoggedIn()) $base->redirect('../index.php');
if(!isset($_SESSION['age']) || !isset($_SESSION['country']) || !isset($_SESSION['username']) || !isset($_SESSION['terms'])) $base->redirect('index.php');

//0: no error   
//1: error
$err = 0;

if(isset($_POST['password']) && isset($_POST['password2']))
{
    if($data['use_recaptcha']){
        $resp = recaptcha_check_answer ($data['private_key'],
        $_SERVER["REMOTE_ADDR"],
        $_POST["recaptcha_challenge_field"],
        $_POST["recaptcha_response_field"]);
        
        if(!$resp->is_valid)
        {
            $err = 1;
        }
    }
    
    if($_POST['password'] != $_POST['password2'])
    {
        $err = 2;
    }
    
    if($err == 0)
    {
        //generate a salt
        $salt = substr(hash(sha256, sha1(time())), 10);
        $password = $salt.hash(sha256, md5(sha1($_POST['password']))).substr($salt, 0, -51);
		
        $_SESSION['salt'] = $salt;
        $_SESSION['password'] = $password;
        $base->redirect('done.php');
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
<script type="text/javascript">
function goBack()
{
	window.history.back();
}	
</script>
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
			<td id="termsReg" class="box">&nbsp; Terms and Conditions &nbsp;</td>
			<td id="passwordReg" class="cur">&nbsp; Choose a Password &nbsp;</td>
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

    if($err == 0)
    {
        ?>
    
        <form action="password.php" method="POST">
        <table width="100%" cellpadding="5">
        <tbody><tr>
        <td colspan="2">
        <ul>
        <li><b>NEVER</b> give anyone your password, not even to <?php echo $data['wb_name']; ?> staff.</li>
        <li><?php echo $data['wb_name']; ?> Moderators will never ask you for your password.</li>
        <li><b><i>DO NOT</i></b> use your RuneScape password here, to better help keep your actual RuneScape account safe.</li>
        <li>Passwords must be between 5 and 20 characters long. We recommend you use a mixture of numbers and letters in your password to make it harder for someone to guess.</li>
        <li>Your password is case sensitive! Don't forget it!</li>
        </ul>
        </td>
        </tr>
        <tr>
        <td width="50%" align="right">
        Desired Password <b><span style="color:red">(CASE SENSITIVE)</span></b>:
        </td>
        <td width="50%" align="left">
        <input id="password1" name="password" type="password" autocomplete="off" value="" maxlength="20">
        </td>
        </tr>
        <tr>
        <td width="50%" align="right">
        Confirm Password <b><span style="color:red">(CASE SENSITIVE)</span></b>:
        </td>
        <td width="50%" align="left">
        <input id="password2" name="password2" type="password" autocomplete="off" value="" maxlength="20">
        </td>
        </tr>
        <?php
            if($data['use_recaptcha']){
                ?>
                    <tr>
                    <td align="right">reCAPTCHA Code:</td>
                    <td align="left">
                    <?php echo recaptcha_get_html($data['public_key']); ?>
                    </td>
                    </tr>
                <?php
            }
        ?>
        <tr>
        <td></td>
        <td align="left">
        <button type="submit" name="submit" id="submit" value="submit">Create Account</button>
        </td>
        </tr>
        </tbody></table>
        </div></form>
    
        <?php
    }
    else
    {
        echo '<center>';
        
        switch($err)
        {
            case 1:
                echo 'Incorrect reCAPTCHA code. <input type="button" value="Back" onclick="goBack()" />';
                break;
            case 2:
                echo 'The passwords did not match. <input type="button" value="Back" onclick="goBack()" />';
                break;
            default:
                echo 'Undefined error. <input type="button" value="Back" onclick="goBack()" />';
                break;
        }
        
        echo '</center>';
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