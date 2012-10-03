<?php 
require('includes/config.php');
require('structure/database.php');
require('structure/base.php');
require('structure/user.php');

$database = new database($db_host, $db_name, $db_user, $db_password);
$base = new base($database);
$user = new user($database);

$rank = $user->getRank($user->getUsername($_COOKIE['user'], 2));
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
<link rel="shortcut icon" href="img/favicon.ico" />
<?php include('includes/google_analytics.html'); ?>
</head>

		<div id="body">
		<div style="text-align: center; background: none;">
				<div class="titleframe e">
					<b>Daily Screenshot</b><br />
					<a href="index.php">Main Menu</a>
				</div>
			</div>
                        <br/>
                        <br/>
                        <div class="titleframe e" style="text-align:left; color:white; width:750px; margin-left:auto; margin-right:auto;">
                            <?php
                                //pagination for daily screenshots - newest to oldest
                                $database->processQuery("SELECT * FROM `dailyscreenshots`", array(), false);
                                $pages = $database->getRowCount();
                                
                                if($pages == 0)
                                {
                                    echo 'No screenshots to display.';
                                }
                                else
                                {
                                    //set basic variables
                                    $page = ($_GET['page'] > $pages || $_GET['page'] == 0 || !isset($_GET['page'])) ? 1 : $_GET['page'];
                                    $start = ($page-1)*1;
                                    
                                    if($page < $pages) { ?> <div style="float:right;"><a href="?page=<?php echo $page+1; ?>">Older Screenshot ></a> &nbsp;&nbsp; <a href="?page=<?php echo $pages; ?>">Oldest Screenshot >></a></div> <?php }
                                    if($page > 1) { ?> <div style="float:left;"><a href="?page=1"><< Newest Screenshot</a> &nbsp;&nbsp;<a href="?page=<?php echo $page-1; ?>">< Newer Screenshot</a></div> <?php }
                                    
                                    //query details
                                    $info = $database->processQuery("SELECT `caption`,`filename` FROM `dailyscreenshots` ORDER BY `id` DESC LIMIT $start,1", array($page), true);
                                    
                                    ?>
                                        <center>
                                            <br/><br/>
                                            <b><?php echo stripslashes($info[0]['caption']); ?></b>
                                            <br/>
                                            <img src="img/dailyscreenshots/<?php echo $info[0]['filename']; ?>" width="750px"></img>
                                        </center>
                                    <?php
                                }
                            ?>
                        </div>
                    
		<div class="tandc"><?php echo $data['wb_foot']; ?></div>
	</div>
	</body>
</html>
