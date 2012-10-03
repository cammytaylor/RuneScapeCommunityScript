<?php
session_start();

if(!isset($_SESSION['step3'])){
    header('location: index.php');
}else{

    unlink('index.php');
    unlink('step2.php');
    unlink('step3.php');
    unlink('done.php');
    rmdir('../install');
    
    //write site URL to official RCS installation list
    if(isset($_SESSION['record_url'])){
        $ch = curl_init('http://rcscript.comlu.com/add.php?url='. $_SESSION['path']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        curl_close($ch);
    }
    
    session_unset();
    session_destroy();
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
                            <h2>Finished</h2>
                            You're finally finished with the installation of RCS. Hooray! If you have any problems, feedback, or concerns - please contact our support forums <a href="http://www.rcscript.comlu.com">here</a>.
                            <br/><br/>
                            <a href="../index.php">Go to your site!</a>
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