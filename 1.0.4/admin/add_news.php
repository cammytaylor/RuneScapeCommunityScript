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
else
{
    //extract a list of forums, other than the one it's currently in
    $query_forums = $database->processQuery("SELECT `id`,`title` FROM `forums` ORDER BY `id` ASC", array(), true);
    
    if(!isset($_POST['title']) || !isset($_POST['category']) || !isset($_POST['content']))
    {
        $content = '
            <form action="add_news.php" method="POST">
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
            <tr><td>Title</td><td><input type="text" name="title" class="button" maxlength="50"></td></tr>
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
            <tr><td>Announcement</td><td><textarea name="content" class="button" rows="20" cols="50" maxlength="100000"></textarea></td></tr>
            <tr>
                <td>Include Forum Post</td>
                <td>
                <select name="f_post" class="button">
                <option value="false" selected="selected">Do not post</option>';
        
                //show the list of forums
                foreach($query_forums as $forum)
                {
                        $content .= '<option value="'. $forum['id'] .'">'. $forum['title'] .'</option>';
                }
            
        $content .= '
                </select><br/><i>select designated forum</i>
                </td>
            </tr>
            <tr><td>Done?</td><td><input type="submit" class="button" value="Post"> <input type="submit" name="preview" class="button" value="Preview"></td></tr>
            </table>
            </form>';
    }
    elseif(isset($_POST['preview']))
    {
        $content = '<center><input type="button" class="button" value="Back" onclick="goBack()" /></center><br/>'.nl2br($_POST['content']);
    }
    elseif(!in_array($_POST['category'], array(1,2,3,4,5,6)))
    {
        $content = 'Incorrect news category selected.';
    }
    else
    {
        $content = nl2br($_POST['content']);
        
        $forums[] = array();
        foreach($query_forums as $forum) { $forums[] = $forum['id']; };
        
        //create new thread in selected section if the option is set
        if($_POST['f_post'] != 'false' && in_array($_POST['f_post'], $forums)) 
                $database->processQuery("INSERT INTO `threads` VALUES (null, ?, ?, ?, ?, NOW(), ?, NOW(), ?, '', ?, ?, '0', '0', '0', '', '0', ?, '0')", array($_POST['f_post'], $_POST['title'], $content, $username, qfc($database), $username, time(), $_SERVER['REMOTE_ADDR'], time()), false);
        
        //insert the news post
        $database->processQuery("INSERT INTO `news` VALUES (null, ?, ?, ?, ?, ?, ?, ?)", array($_POST['title'], $_POST['category'], $content, $username, date('M-d-Y'), $_SERVER['REMOTE_ADDR'], $_POST['icon']), false);
        
        $base->redirect('../news/viewarticle.php?id='. $database->getInsertId());
    }
}

function qfc(database $database)
{
    $qfc = rand(0,9).'-'.rand(0,9).'-'.rand(1000,9999).'-'.rand(10000,19999);

    //make sure this qfc doesn't already exist (even though it's not likely to happen)
    $database->processQuery("SELECT * FROM `threads` WHERE `qfc` = ?", array($qfc), false);

    if($database->getRowCount() >= 1)
        qfc();
    else
        return $qfc;
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
                    <b>Add a news post</b><br>
                    <a href="../index.php" class=c>Main Menu</a> - <a href="index.php">Back</a>
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
