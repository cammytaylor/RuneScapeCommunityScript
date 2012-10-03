<?php
session_start();

if(isset($_SESSION['step2'])){
    header('location: step3.php');
}elseif(!isset($_SESSION['step1'])){
    header('location: index.php');
}else{
    $err = array();
    if(isset($_POST['username']) && isset($_POST['password'])){
        $username = $_POST['username'];
        
        if(preg_match('/[^a-zA-Z0-9_ ]/', $username)){
            $err[1] = true;
        }elseif(strlen($username) > 12){
            $err[2] = true;
        }else{
            //generate a salt
            $salt = substr(hash(sha256, sha1(time())), 10);
            $password = $salt.hash(sha256, md5(sha1($_POST['password']))).substr($salt, 0, -51);
            
            $db = new PDO("mysql:host={$_SESSION['mysql_host']};dbname={$_SESSION['mysql_name']}", $_SESSION['mysql_user'], $_SESSION['mysql_pass']);
            $db->query("INSERT INTO `users` VALUES (null, '$username', '$password', '13-18', '225', 'fdsfdsf@gmail.com', '". date('M/d/Y') ."', 4, '". $_SERVER['REMOTE_ADDR'] ."', 0, ". time() .", '". substr(hash(sha256, time()), 35) ."', 0, 0, 1, 0, 0, '". $_SERVER['REMOTE_ADDR'] ."')");
            
            $_SESSION['step2'] = true;
            header('location: step3.php');
        }
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
<title>Install RCS</title>
<link href="../css/basic-3.css" rel="stylesheet" type="text/css" media="all">
<link href="../css/main/title-5.css" rel="stylesheet" type="text/css" media="all">
<link href="../css/kbase-2.css" rel="stylesheet" type="text/css" media="all" />
<link rel="shortcut icon" href="img/favicon.ico" />
</head>

		<div id="body">
		<div style="text-align: center; background: none;">
				<div class="titleframe e">
					<b>Install RuneScape Community Script</b>
				</div>
			</div>

			
			<img class="widescroll-top" src="../img/scroll/backdrop_765_top.gif" alt="" width="765" height="50" />
			<div class="widescroll">
			<div class="widescroll-bgimg">
			<div class="widescroll-content">
                            <div style="margin-left:auto;margin-right:auto;text-align:left;width:325px;">
                            <h2>Create owner account</h2>
                            <form action="step2.php" method="POST">
                                <table>
                                   <?php
                                    if($err[1]){
                                        ?>
                                            <tr style="color:red;">
                                                <td>Error</td>
                                                <td>Username contains illegal characters.</td>
                                            </tr>
                                        <?php
                                    }
                                    
                                    if($err[2]){
                                        ?>
                                            <tr style="color:red;">
                                                <td>Error</td>
                                                <td>Username is too long.</td>
                                            </tr>
                                        <?php
                                    }
                                   ?>
                                   <tr>
                                       <td>Username</td>
                                       <td><input type="text" name="username" maxlength="12"></td>
                                   </tr>
                                   <tr>
                                       <td>Password</td>
                                       <td><input type="password" name="password"></td>
                                   </tr>
                                   <tr>
                                       <td>Continue</td>
                                       <td><input type="submit" value="Step 3 ->"></td>
                                   </tr>
                                </table>
                            </form>
                            </div>
			<div style="clear: both;"></div>
			</div>
			</div>
			</div>
			<img class="widescroll-bottom" src="../img/scroll/backdrop_765_bottom.gif" alt="" width="765" height="50" />
		<div class="tandc">This website and its contents are copyright &copy; 1999 - 2007 Jagex Ltd.<br/>
Use of this website is subject to our Terms+Conditions and Privacy policy<br/>Powered by RuneScape Community Script (RCS)</div>
	</div>
	</body>
</html>
