<?php
require('../includes/config.php');
require('../structure/database.php');
require('../structure/base.php');
require('../structure/user.php');

$database = new database($db_host, $db_name, $db_user, $db_password);
$base = new base($database);
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
<link href="../css/basic-3.css" rel="stylesheet" type="text/css" media="all">
<!-- <link href="../css/register-1.css" rel="stylesheet" type="text/css" media="all"> -->
<link rel="shortcut icon" href="../img/favicon.ico" />
<style>
div.topnav {
	text-align: center;
	margin-bottom: 5px;
}

div.topnav img {
	vertical-align: bottom;
}

div.bottomnav {
	text-align: center;
	margin-top: 10px;
}

div.bottomnav img {
	vertical-align: bottom;
}

.newstitlebground {
	margin-left: auto;
	margin-right: auto;
	padding: 4px;
	width: 759px;
	height: 130px;
	background: url('../img/news/header.gif') no-repeat;
}

.newstitleframe {
	width: 165px;
	margin-left: auto;
	margin-right: auto;
	padding: 4px;
	border: 2px solid #382418;
	background-color: black;
	margin-top: 50px;
}
</style>
<?php include('../includes/google_analytics.html'); ?>
</head>
	<div id="body">

			<div style="text-align: center;">
			<div class="newstitlebground">
				<div class="newstitleframe">
					<b>Website News</b><br /> <a
						href="../index.php">Main Menu</a> - <a href="index.php">News List</a>
				</div>
			</div>
		</div>

		<img class="widescroll-top" src="../img/scroll/backdrop_765_top.gif" alt="" width="765" height="50" />
		<div class="widescroll">
			<div class="widescroll-bgimg">

				<div class="widescroll-content">
                                    <?php
                                        if(!ctype_digit($_GET['id']))
                                        {
                                            echo 'An news ID is required to access this page. <a href="index.php">Return</a>';
                                        }
                                        else
                                        {
                                            $article = $database->processQuery("SELECT `title`,`content`,`date` FROM `news` WHERE `id` = ?", array($_GET['id']), true);
                                            
                                            if($database->getRowCount() == 0)
                                            {
                                                echo 'No news articel exists with the specified ID.';
                                            }
                                            else
                                            {
                                                ?>
                                    
                                                <hr class="trails_top" />
                                                <b style="float: left;">Location:</b>
                                                    <div style="margin-left: 6em;">
                                                            <a href="../index.php">Home</a> > <a href="index.php">News List</a> >
                                                            <?php echo stripcslashes(htmlentities($article[0]['title'], ENT_NOQUOTES)); ?><br />

                                                    </div>
                                                
                                                <hr class="trails" />
                                                <div style="font-weight: bold; text-align: center; margin-top: 10px; margin-bottom: 10px;">
                                                    <?php echo $article[0]['date'].' - '.stripcslashes(htmlentities($article[0]['title'], ENT_NOQUOTES));  ?></div>
                                                <div style="text-align: justify;"></div>
                                                <div style="clear: both;">
                                                <?php echo stripcslashes($article[0]['content']); ?>
                                                </div>
                                                
                                                <?php
                                            }
                                        }
                                    ?>
				</div>
			</div>
		</div>
		<img class="widescroll-bottom" src="../img/scroll/backdrop_765_bottom.gif" alt="" width="765" height="50" />	

		<div class="tandc"><?php echo $data['wb_foot']; ?></div>
</body>
</html>
