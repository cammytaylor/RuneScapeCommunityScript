<?php 
require('includes/config.php');
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
    <b>Maintenance</b><br>
    <a href="index.php" class=c>Main Menu</a>
    </div>
    </div>
<br>
<br>
<div class="frame wide_e">
<div style="text-align: justify">
<center>
<?php echo $data['wb_abbr']; ?> is currently undergoing maintenance. We'll be back shortly!
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