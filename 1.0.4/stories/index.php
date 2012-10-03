<?php
require('../includes/config.php');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns:IE>
<head>
<meta http-equiv="Expires" content="0">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache">
<meta name="MSSmartTagsPreventParsing" content="TRUE">
<title><?php echo $data['wb_title']; ?></title>
<link href="../css/basic-3.css" rel="stylesheet" type="text/css" media="all">
<link rel="shortcut icon" href="../img/favicon.ico" />
<?php include('../includes/google_analytics.html'); ?>
</head>
	<div id="body">

			<div style="text-align: center;">
			<div style="text-align: center; background: none;">
                        <div class="titleframe e">
                            <b>Stories and Letters</b><br>
                            <a href="../index.php" class=c>Main Menu</a>
                        </div>
                        </div>
		</div>

		<img class="widescroll-top" src="../img/scroll/backdrop_765_top.gif" alt="" width="765" height="50" />
		<div class="widescroll">
			<div class="widescroll-bgimg">
				<div class="widescroll-content">
                                    <table align="center" style="border-spacing: 5px;">
                                        <tr>
                                        <td style="padding: 2px; border: #382418 2px solid; width: 355px; text-align: left;">
                                        <a href="lores/index.php"><img src="../img/varrock/lores/lores.jpg" style="float: left; padding-right: 3px;">
                                        <b><span style="font-size: 16;">Lores and Histories</span></b></a><br>
                                        Stories and histories from the lands of <?php echo $data['wb_abbr']; ?>.
                                        </td>
                                        <td style="padding: 2px; border: #382418 2px solid; width: 355px; text-align: left;">
                                        <a href="#"><img src="../img/varrock/letters/letters.jpg" style="float: left; padding-right: 3px;">
                                        <b><span style="font-size: 16;">Postbag from the Hedge</span></b></a><br>
                                        Read letters from your favourite NPCs.
                                        </td>
                                        </tr>
                                        <tr>
                                        <td style="padding: 2px; border: #382418 2px solid; width: 355px; text-align: left;">
                                        <a href="#"><img src="../img/varrock/gallery/gallery.jpg" style="float: left; padding-right: 3px;">
                                        <b><span style="font-size: 16;">Players' Gallery</span></b></a><br>
                                        View some great art made by <?php echo $data['wb_abbr']; ?>'s players.
                                        </td>
                                        <td style="padding: 2px; border: #382418 2px solid; width: 355px; text-align: left;">
                                        <a href="#"><img src="../img/varrock/devdiary/devdiary.jpg" style="float: left; padding-right: 3px;">
                                        <b><span style="font-size: 16;">Development Diaries</span></b></a><br>
                                        Explore behind the scenes at <?php echo $data['wb_abbr']; ?> HQ.
                                        </td>
                                        </tr>
                                    </table>
				</div>
			</div>
		</div>
		<img class="widescroll-bottom" src="../img/scroll/backdrop_765_bottom.gif" alt="" width="765" height="50" />	

		<div class="tandc"><?php echo $data['wb_foot']; ?></div>
</body>
</html>
