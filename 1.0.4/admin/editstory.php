<?php
require('../includes/config.php');
require('../structure/database.php');
require('../structure/base.php');
require('../structure/user.php');

$database = new database($db_host, $db_name, $db_user, $db_password);
$base = new base($database);
$user = new user($database);
$user->updateLastActive();

$username = $user->getUsername($_COOKIE['user'], 2);
$rank = $user->getRank($username);

if($rank < 4)
{
    $base->redirect('../index.php');
}
elseif(!isset($_GET['id']))
{
    $base->redirect('index.php');
}
else
{
    //id of the story we're editing
    $id = $_GET['id'];
    
    //extract content from selected news post
    $data = $database->processQuery("SELECT `title`,`content` FROM `stories` WHERE `id` = ?", array($id), true);
    
    if($database->getRowCount() == 0)
    {
        $content = 'No story exists with the chosen ID. <input type="button" class="button" value="Back" onclick="goBack()" />';
    }
    elseif(!isset($_POST['title']) || !isset($_POST['content']))
    {
        $content = '
            <form action="editstory.php?id='. $id .'" method="POST">
            <table>
            <tr><td>Title</td><td><input type="text" class="button" name="title" maxlength="50" value="'. stripslashes($data[0]['title']) .'"></td></tr>
            <tr><td>Content</td><td><textarea name="content" class="button" cols="60" rows="35">'. $base->remBr(stripslashes($data[0]['content'])) .'</textarea></td></tr>
            <tr><td>Done?</td><td><input type="submit" class="button" value="Update"> <input type="submit" name="preview" class="button" value="Preview"></td></tr>
            </table>
            </form>';
    }
    elseif(isset($_POST['delete']))
    {
        $database->processQuery("DELETE FROM `stories` WHERE `id` = ?", array($id), false);
        
        $content = 'The story has been deleted.';
    }
    elseif(isset($_POST['preview']))
    {
        $content = '<center><input type="button" class="button" value="Back" onclick="goBack()" /></center><br/>'. $base->addSpecials(stripslashes(nl2br($_POST['content'])), '../img/varrock/lores/');
    }
    else
    {
        //update the story to the new content/title
        $database->processQuery("UPDATE `stories` SET `title` = ?, `content` = ? WHERE `id` = ? LIMIT 1", array($_POST['title'], nl2br($_POST['content']), $id), false);
        
        //log it
        $base->appendToFile('../forums/logs.txt', array($username. ' edited a story'));
        
        $base->redirect('../stories/lores/story.php?id='. $id);
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
<title><?php echo $data['wb_title']; ?></title>
<link href="../css/basic-3.css" rel="stylesheet" type="text/css" media="all">
<link rel="shortcut icon" href="../img/favicon.ico" />
<?php include('../includes/google_analytics.html'); ?>
<script type="text/javascript">
function goBack()
{
	window.history.back();
}	
</script>
</head>
	<div id="body">

                <div style="text-align: center; background: none;">
                    <div class="titleframe e">
                        <b>Editing Story</b><br>
                        <a href="../index.php" class=c>Main Menu</a> - <a href="../stories/lores/index.php">Story Index</a>
                    </div>
                </div>

		<img class="widescroll-top" src="../img/scroll/backdrop_765_top.gif" alt="" width="765" height="50" />
		<div class="widescroll">
			<div class="widescroll-bgimg">

				<div class="widescroll-content">
                                    <div style="text-align: justify;color: #402706">
                                        <div id="black_fields">
                                            <?php
                                                echo $content;
                                            ?>
                                        </div>
                                    </div>
				</div>
			</div>
		</div>
		<img class="widescroll-bottom" src="../img/scroll/backdrop_765_bottom.gif" alt="" width="765" height="50" />	

		<div class="tandc"><?php echo $data['wb_foot']; ?></div>
</body>
</html>
