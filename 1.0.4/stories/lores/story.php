<?php
require('../../includes/config.php');
require('../../structure/database.php');
require('../../structure/base.php');
require('../../structure/user.php');

$database = new database($db_host, $db_name, $db_user, $db_password);
$base = new base();
$user = new user($database);
$user->updateLastActive();

$rank = $user->getRank($user->getUsername($_COOKIE['user'], 2));

//id of the story
$id = $_GET['id'];

//make sure the story exists then extract its content
$story = $database->processQuery("SELECT `id`,`title`,`content` FROM `stories` WHERE `id` = ?", array($id), true);

//doesn't exist, send them to stories index
if($database->getRowCount() == 0) $base->redirect('index.php');
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
                    <a href="../index.php" class=c>Main Menu</a> - <a href="index.php">Back</a>
                </div>
                </div>

                <?php
                    if($rank > 3)
                    {
                        ?> 
                            <br/>
                            <br/>
                            
                            <div class="titleframe e">
                                <a href="../../admin/editstory.php?id=<?php echo $id; ?>">Edit this Article</a>
                            </div>
                                
                        <?php
                    }
                ?>
            
		<img class="widescroll-top" src="../../img/scroll/backdrop_765_top.gif" alt="" width="765" height="50" />
		<div class="widescroll">
                    
			<div class="widescroll-bgimg">
				<div class="widescroll-content">
                                    <div style="text-align: justify;color: #402706">
                                        <h1><?php echo $story[0]['title']; ?></h1>
                                        <?php 
                                            echo $base->addSpecials(stripslashes($story[0]['content']), '../../img/varrock/lores/'); 
                                        ?>
                                    </div>
				</div>
			</div>
		</div>
		<img class="widescroll-bottom" src="../../img/scroll/backdrop_765_bottom.gif" alt="" width="765" height="50" />	

		<div class="tandc"><?php echo $data['wb_foot']; ?></div>
</body>
</html>
