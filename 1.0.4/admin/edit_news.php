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
    //extract content from selected news post
    $data = $database->processQuery("SELECT `title`,`content` FROM `news` WHERE `id` = ?", array($_GET['id']), true);
    
    if($database->getRowCount() == 0)
    {
        $content = 'No news post exists with the chosen ID. <input type="button" class="button" value="Back" onclick="goBack()" />';
    }
    elseif(!isset($_POST['title']) || !isset($_POST['category']) || !isset($_POST['content']))
    {
        $content = '
            <form action="edit_news.php?id='. $_GET['id'] .'" method="POST">
            <table>
            <tr><td>Icon</td><td>
			<input type="radio" name="icon" value="1" /> <img src="../img/news/behind_the-scenes_2.gif" width="20" height="20">
			<input type="radio" name="icon" value="2" /> <img src="../img/news/fris_kingly_helm.gif" width="20" height="20">
			<input type="radio" name="icon" value="3" /> <img src="../img/news/shop_2.gif" width="20" height="20">
			<input type="radio" name="icon" value="4" /> <img src="../img/news/technical_3.gif" width="20" height="20">
			<input type="radio" name="icon" value="5" /> <img src="../img/news/world.gif" width="20" height="20">
			<input type="radio" name="icon" value="6" /> <img src="../img/news/green_cauldron.gif" width="20" height="20">
			<input type="radio" name="icon" value="7" /> <img src="../img/news/goblin.gif" width="20" height="20">
			<input type="radio" name="icon" value="8" /> <img src="../img/news/scroll.gif" width="20" height="20">
			<input type="radio" name="icon" value="9" /> <img src="../img/news/mail.gif" width="20" height="20">
			<input type="radio" name="icon" value="10" /> <img src="../img/news/bug_tracking_3.gif" width="20" height="20">
			<input type="radio" name="icon" value="11" /> <img src="../img/news/cabbage.gif" width="20" height="20">
			<input type="radio" name="icon" value="12" /> <img src="../img/news/coal.gif" width="20" height="20"><br/>
			<input type="radio" name="icon" value="13" /> <img src="../img/news/demon.gif" width="20" height="20">
			<input type="radio" name="icon" value="14" /> <img src="../img/news/macaroni_penguin.gif" width="20" height="20">
			<input type="radio" name="icon" value="15" /> <img src="../img/news/gold.gif" width="20" height="20">
			<input type="radio" name="icon" value="16" /> <img src="../img/news/high_priority.gif" width="20" height="20">
			<input type="radio" name="icon" value="17" /> <img src="../img/news/santa.gif" width="20" height="20">
			<input type="radio" name="icon" value="18" /> <img src="../img/news/scroll.gif" width="20" height="20">
			<input type="radio" name="icon" value="19" /> <img src="../img/news/shifter.gif" width="20" height="20">
                        <input type="radio" name="icon" value="20" /> <img src="../img/news/squirrel.gif" width="20" height="20">
                        <input type="radio" name="icon" value="21" /> <img src="../img/news/pohousing.gif" width="20" height="20">
                        <input type="radio" name="icon" value="22" /> <img src="../img/news/presents.gif" width="20" height="20">
                        <input type="radio" name="icon" value="23" /> <img src="../img/news/clock.gif" width="20" height="20">
            </td></tr>
            <tr><td>Title</td><td><input type="text" name="title" class="button" maxlength="50" value="'. stripslashes($data[0]['title']) .'"></td></tr>
            <tr>
                <td>Category</td>
                <td>
                <select name="category" class="button">
                    <option value="1">Website</option>
                    <option value="2">Game</option>
                    <option value="3">Shop</option>
                    <option value="4">Customer Support</option>
                    <option value="5">Technial</option>
                    <option value="6">Behind the Scenes</option>
                </select>
                </td>
            </tr>
            <tr><td>Announcement</td><td><textarea name="content" class="button" rows="20" cols="50" maxlength="100000">'. $base->remBr(stripslashes($data[0]['content'])) .'</textarea></td></tr>
            <tr><td>Done?</td><td><input type="submit" class="button" value="Update"> <input type="submit" name="preview" class="button" value="Preview"> <input type="submit" name="delete" class="button" value="Delete"></td></tr>
            </table>
            </form>';
    }
    elseif(isset($_POST['delete']))
    {
        $database->processQuery("DELETE FROM `news` WHERE `id` = ?", array($_GET['id']), false);
        
        $content = 'The news post has been deleted.';
    }
    elseif(isset($_POST['preview']))
    {
        $content = '<center><input type="button" class="button" value="Back" onclick="goBack()" /></center><br/>'.stripslashes(nl2br($_POST['content']));
    }
    elseif(!in_array($_POST['category'], array(1,2,3,4,5,6)))
    {
        $content = 'Incorrect news category selected.';
    }
    else
    {
        //insert the news post
        $database->processQuery("UPDATE `news` SET `title` = ?, `content` = ?, `category` = ?, `icon` = ? WHERE `id` = ? LIMIT 1", array($_POST['title'], nl2br($_POST['content']), $_POST['category'], $_POST['icon'], $_GET['id']), false);
        $base->appendToFile('../forums/logs.txt', array($username. ' edited a news post'));
        
        $base->redirect('../news/viewarticle.php?id='. $_GET['id']);
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
                            <b>Editing news post</b><br>
                            <a href="index.php" class=c>Main Menu</a> - <a href="../news/index.php">News Listing</a>
                        </div>
                </div>

		<img class="widescroll-top" src="../img/scroll/backdrop_765_top.gif" alt="" width="765" height="50" />
		<div class="widescroll">
			<div class="widescroll-bgimg">

				<div class="widescroll-content">
                                    <div id="black_fields">
                                        <?php
                                            echo $content;
                                        ?>
                                    </div>
				</div>
			</div>
		</div>
		<img class="widescroll-bottom" src="../img/scroll/backdrop_765_bottom.gif" alt="" width="765" height="50" />	

		<div class="tandc"><?php echo $data['wb_foot']; ?></div>
</body>
</html>
