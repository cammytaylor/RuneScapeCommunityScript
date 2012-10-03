<?php 
include('includes/config.php');
include('structure/database.php'); 
include('structure/user.php');

$database = new database($db_host, $db_name, $db_user, $db_password);
$user = new user($database);
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
<style>
.donorbox
{
width:550px;
text-align:left;
}

#rbox_r
{
	width:550px;
	border:1.5px solid #900000;
	background-color:#500000;
	padding:5px;
} 
</style>
<?php include('includes/google_analytics.html'); ?>
</head>

		<div id="body">
		<div style="text-align: center; background: none;">
				<div class="titleframe e">
				<b>Donate</b><br>
				<a href="index.php" class=c>Main Menu</a>
				</div>
			</div>

			
			<img class="widescroll-top" src="img/scroll/backdrop_765_top.gif" alt="" width="765" height="50" />
			<div class="widescroll">
			<div class="widescroll-bgimg">
			<div class="widescroll-content">
			<center>
			<div class="donorbox">
			<div id="rbox_r"><font color="white">We do not force or require you to donate. It is optional and out of your own free will.</font></div>
			<br/>
			Donating any amount of money to support the <?php echo $data['wb_name']; ?> project is a wonderful thing. As of right now, the money to pay for hosting is coming out of the developers' pockets, so any sort of donation is a big weight lifted off our backs. <b>Donating is NOT required. If someone donates, it's their own decision.</b>
			<br/><br/>
			<font size="1">If you have donated, post here: <a href="#">I've donated</a></font>
			</div>
			<br/>
			<br/>
			<?php
			if(!$user->isLoggedIn())
			{
				echo 'You must be logged in to access this feature.';
			}
			else
			{
				?>
				
                                    <a href="#"><img src="img/title2/paypalFalse.gif" border="0"></a>
				
				<?php
			}
			?>
			<br/>
			</center>
			<div style="clear: both;"></div>
			</div>
			</div>
			</div>
			<img class="widescroll-bottom" src="img/scroll/backdrop_765_bottom.gif" alt="" width="765" height="50" />
			<div class="tandc"><?php echo $data['wb_foot']; ?></div>
	</div>
	</body>
</html>