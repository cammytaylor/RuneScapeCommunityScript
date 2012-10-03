<?php
require('../../includes/config.php');
require('../../structure/database.php');
require('../../structure/user.php');

$database = new database($db_host, $db_name, $db_user, $db_password);
$user = new user($database);
$user->updateLastActive();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns:IE>
<head>
<meta http-equiv="Expires" content="0">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache">
<meta name="MSSmartTagsPreventParsing" content="TRUE">
<title><?php echo $data['wb_title']; ?></title>
<link href="../../css/basic-3.css" rel="stylesheet" type="text/css" media="all">
<link rel="shortcut icon" href="../../img/favicon.ico" />
<?php include('../../includes/google_analytics.html'); ?>
</head>
	<div id="body">

                <div style="text-align: center; background: none;">
                <div class="titleframe e">
                    <b>Stories and Lores</b><br>
                    <a href="../../index.php" class=c>Main Menu</a>
                </div>
                </div>

		<img class="widescroll-top" src="../../img/scroll/backdrop_765_top.gif" alt="" width="765" height="50" />
		<div class="widescroll">
			<div class="widescroll-bgimg">
				<div class="widescroll-content">
                                    <div style="text-align: justify;color: #402706">
                                        <p><img src="../../img/varrock/lores/decor_w.gif" style="float: left;">elcome to the Royal Library of Varrock, home of knowledge and wisdom, repository of all true learning in the human kingdoms (and even some from beyond). My name is Reldo, and as curator, historian and scholar to the histories of <?php echo $data['wb_name']; ?>, I shall be your guide here.</p>
                                        <p>In the Library I keep records of the histories of the great nations of <?php echo $data['wb_name']; ?>, the diaries of bold explorers, and fantastical tales spun in ages past. Please, take some time to peruse the catalogue and I'm sure you will find some tome of interest to you.</p>
                                        <p>Below you will find a list of those books restored or in suitable condition to be read by any passing reader. Some are verifiably accurate histories of <?php echo $data['wb_name']; ?>, others are mythologies relating to true people and events whose accuracy cannot be guaranteed, but in the absence of other source material I have included for historical interest.</p>
                                        <p>I hope you enjoy the following documents as much as I have enjoyed compiling them.</p>
                                        <p align="right">-Reldo</p>
                                        </i>
                                        </b>
                                        <p style="text-align: center; margin-top: 20px;"><img src="../../img/kbase/scroll_spacer.gif"></p><br>
                                        <center>
                                            <?php
                                                //show stories
                                                $stories = $database->processQuery("SELECT `id`,`title` FROM `stories` ORDER BY `id` DESC", array(), true);

                                                if($database->getRowCount() >= 1)
                                                {
                                                    foreach($stories as $story)
                                                    {
                                                        echo '<a href="story.php?id='. $story['id'] .'">'. $story['title'] .'</a><br/><br/>';
                                                    }
                                                }
                                                else
                                                {
                                                    echo '<b>There are currently no stories.</b>';
                                                }
                                            ?>
                                        </center>
                                    </div>
				</div>
			</div>
		</div>
		<img class="widescroll-bottom" src="../../img/scroll/backdrop_765_bottom.gif" alt="" width="765" height="50" />	

		<div class="tandc"><?php echo $data['wb_foot']; ?></div>
</body>
</html>
