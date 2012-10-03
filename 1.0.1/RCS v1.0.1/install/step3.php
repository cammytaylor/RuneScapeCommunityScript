<?php
session_start();

if(isset($_SESSION['step3'])){
    header('location: done.php');
}elseif(!isset($_SESSION['step2'])){
    header('location: step2.php');
}else{
    if(isset($_POST['name']) && isset($_POST['title']) && isset($_POST['abbr'])){
        $file = '../includes/config.php';
        $data = file_get_contents($file);

        //data conversion
        $old = array('{wb_name}', '{abbr}', '{title}');
        $new = array($_POST['name'], $_POST['abbr'], $_POST['title']);
        $written = str_replace($old, $new, $data);

        $f = fopen($file, 'w');
        fwrite($f, $written);
        fclose($f);
        
        $_SESSION['step3'] = true;
        header('location: done.php');
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
                            <h2>Site Details</h2>
                            <form action="step3.php" method="POST">
                                <table>
                                   <tr>
                                       <td>Site Name</td>
                                       <td><input type="text" name="name" maxlength="25"></td>
                                   </tr>
                                   <tr>
                                       <td>Site Abbreviation</td>
                                       <td><input type="text" name="abbr" maxlength="10"></td>
                                   </tr>
                                   <tr>
                                       <td>Site Title</td>
                                       <td><input type="text" name="title"></td>
                                   </tr>
                                   <tr>
                                       <td>Finish</td>
                                       <td><input type="submit" value="Complete ->"></td>
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
