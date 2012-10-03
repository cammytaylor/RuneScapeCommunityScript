<?php 
require('includes/config.php');
require('structure/database.php');
require('structure/base.php');
require('structure/user.php');
require('structure/user.login.php');

$database = new database($db_host, $db_name, $db_user, $db_password);
$base = new base($database);
$user = new user($database);
$login = new user_login($database);
$login->resetAttempts();

if($user->isLoggedIn())
{
	$content =  'You\'re already logged in! <a href="index.php">Return home...</a>';
}
else
{
        
        if(!$login->canAttempt())
        {
            $content = 'You cannot attempt another login at this time.';
        }
        else
        {
            if(isset($_POST['username']) && isset($_POST['password']))
            {
                $username = $_POST['username'];
                $password = hash(sha256, md5(sha1($_POST['password'])));

                //get the requested user's password
                $details = $database->processQuery("SELECT `password` FROM `users` WHERE `username` = ?", array($username), true);
                $db_password = substr(substr($details[0]['password'], 54), 0, -3);

                if($username == 'Report')
                {
                    $content = 'This type of account cannot be accessed. <input type="button" value="Back" onclick="goBack()" />';
                }
                elseif(($db_password != $password) || $database->getRowCount() == 0)
                {
                    $content = 'Incorrect login details. <input type="button" value="Back" onclick="goBack()" />';
                    
                    $login->failed();
                }
                else
                {
                    if($user->isDisabled($username))
                    {
                        $content = 'This account is disabled. <input type="button" value="Back" onclick="goBack()" />';
                    }
                    else
                    {
                        $database->processQuery("UPDATE `users` SET `lastlogin` = ?, `lastip` = ? WHERE `username` = ? LIMIT 1", array(time(), $_SERVER['REMOTE_ADDR'], $username), false);
                        setcookie('user', session_hash($database, $base, $username), time()+$data['login_time'], '/', '.'.$domain);
                        $base->redirect('index.php');
                    }
                }
            }
            else
            {
                $content = '
                <table>
                <div id="black_fields">
                <form action="login.php" method="POST">
                <tr><td>Username</td><td><input type="text" class="button" name="username" maxlength="12"></td></tr>
                <tr><td>Password</td><td><input type="password" class="button" name="password" maxlength="20"></td></tr>
                <tr><td><input type="submit" class="button" value="Login"></td></tr>
                </form>
                </div>
                </table>
                <br/>
                <font size="1">Remember passwords are case sensitive!</font>';
            }
        }
}

function session_hash(database $database, base $base, $username)
{
    //generate new hash
    $session_hash = $base->randomString(35);

    //update old hash to new one (after checking the hahs doesn't exist)
    $database->processQuery("SELECT * FROM `users` WHERE `cookie` = ?", array($session_hash), false);
    
    if($database->getRowCount() == 0)
    {
        $database->processQuery("UPDATE `users` SET `cookie` = ? WHERE `username` = ? LIMIT 1", array($session_hash, $username), false);
        return $session_hash;
    }
    else
    {
        session_hash();
    }
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns:IE>

<!-- LeeStrong Runescape Website Source --!>
<!-- Added by HTTrack --><meta http-equiv="content-type" content="text/html;charset=ISO-8859-1"><!-- /Added by HTTrack -->
<head>
<?php include('includes/google_analytics.html'); ?>
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache">
<meta name="MSSmartTagsPreventParsing" content="TRUE">
<link href="css/basic-3.css" rel="stylesheet" type="text/css" media="all">
<link rel="shortcut icon" href="img/favicon.ico" />
<title><?php echo $data['wb_title']; ?></title>
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
    <b>Login</b><br>
    <a href="index.php" class=c>Main Menu</a>
    </div>
    </div>
<br>
<br>
<div class="frame wide_e">
<div style="text-align: justify">
<center>
<?php echo $content; ?>
</center>
</div>
</div>
<br>

<div class="tandc"><?php echo $data['wb_foot']; ?></div>

</div>

</body>

<!-- LeeStrong Runescape Website Source --!>
<!-- Added by HTTrack --><meta http-equiv="content-type" content="text/html;charset=ISO-8859-1"><!-- /Added by HTTrack -->
</html>