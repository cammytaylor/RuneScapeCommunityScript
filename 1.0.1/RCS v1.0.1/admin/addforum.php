<?php
require('../includes/config.php');
require('../structure/database.php');
require('../structure/base.php');
require('../structure/forum.php');
require('../structure/forum.index.php');
require('../structure/user.php');

$database = new database($db_host, $db_name, $db_user, $db_password);
$base = new base($database);
$forum = new forum($database);
$forum_index = new forum_index($database);
$user = new user($database);
$user->updateLastActive();

$username = $user->getUsername($_COOKIE['user'], 2);
$rank = $user->getRank($username);

if($rank < 4) $base->redirect('../index.php');

if(!isset($_POST['forum']) || !isset($_POST['category']) || !isset($_POST['description']) || !isset($_POST['icon']) || !isset($_POST['pos']) || !isset($_POST['type']))
{
    $content = '
        <form action="addforum.php" method="POST">
            <table>
            <tr>
            <td>Icon</td><td>
			<input type="radio" name="icon" value="1" /> <img src="../img/forum/icons/bug.gif" width="20" height="20">
			<input type="radio" name="icon" value="2" /> <img src="../img/forum/icons/clan.gif" width="20" height="20">
			<input type="radio" name="icon" value="3" /> <img src="../img/forum/icons/clan_recruitment.gif" width="20" height="20">
			<input type="radio" name="icon" value="4" /> <img src="../img/forum/icons/compliments.gif" width="20" height="20">
			<input type="radio" name="icon" value="5" /> <img src="../img/forum/icons/events.gif" width="20" height="20">
			<input type="radio" name="icon" value="6" /> <img src="../img/forum/icons/forum_feedback.gif" width="20" height="20">
			<input type="radio" name="icon" value="7" /> <img src="../img/forum/icons/forum_games.gif" width="20" height="20">
			<input type="radio" name="icon" value="8" /> <img src="../img/forum/icons/future_updates.gif" width="20" height="20">
			<input type="radio" name="icon" value="9" /> <img src="../img/forum/icons/general.gif" width="20" height="20">
			<input type="radio" name="icon" value="10" /> <img src="../img/forum/icons/goalsandachievements.gif" width="20" height="20">
			<input type="radio" name="icon" value="11" /> <img src="../img/forum/icons/Guides.gif" width="20" height="20">
			<input type="radio" name="icon" value="12" /> <img src="../img/forum/icons/item_discussion.gif" width="20" height="20"><br/>
			<input type="radio" name="icon" value="13" /> <img src="../img/forum/icons/monsters.gif" width="20" height="20">
			<input type="radio" name="icon" value="14" /> <img src="../img/forum/icons/news_announcements.gif" width="20" height="20">
			<input type="radio" name="icon" value="15" /> <img src="../img/forum/icons/off_topic.gif" width="20" height="20">
			<input type="radio" name="icon" value="16" /> <img src="../img/forum/icons/fighting.gif" width="20" height="20">
			<input type="radio" name="icon" value="17" /> <img src="../img/forum/icons/quest.gif" width="20" height="20">
			<input type="radio" name="icon" value="18" /> <img src="../img/forum/icons/questions.gif" width="20" height="20">
			<input type="radio" name="icon" value="19" /> <img src="../img/forum/icons/rants.gif" width="20" height="20">
			<input type="radio" name="icon" value="20" /> <img src="../img/forum/icons/recent_updates.gif" width="20" height="20">
			<input type="radio" name="icon" value="21" /> <img src="../img/forum/icons/skills.gif" width="20" height="20">
			<input type="radio" name="icon" value="22" /> <img src="../img/forum/icons/stories.gif" width="20" height="20">
			<input type="radio" name="icon" value="23" /> <img src="../img/forum/icons/suggestions.gif" width="20" height="20">
			<input type="radio" name="icon" value="24" /> <img src="../img/forum/icons/tech_support.gif" width="20" height="20"><br/>
			<input type="radio" name="icon" value="25" /> <img src="../img/forum/icons/web_feedback.gif" width="20" height="20">
			<input type="radio" name="icon" value="26" /> <img src="../img/forum/icons/armour_2.gif" width="20" height="20">
			<input type="radio" name="icon" value="27" /> <img src="../img/forum/icons/crafting_2.gif" width="20" height="20">
			<input type="radio" name="icon" value="28" /> <img src="../img/forum/icons/fletching_2.gif" width="20" height="20">
			<input type="radio" name="icon" value="29" /> <img src="../img/forum/icons/ores_bars_2.gif" width="20" height="20">
			<input type="radio" name="icon" value="30" /> <img src="../img/forum/icons/runes_2.gif" width="20" height="20">
			<input type="radio" name="icon" value="31" /> <img src="../img/forum/icons/weapons_forum_2.gif" width="20" height="20">
			
            </td>
            </tr>
            <tr>
                <td>Type</td>
                <td>
                    <select name="type" class="button">
                        <option value="1" selected="selected">Normal</option>
                        <option value="2">Staff-only threads/topics</option>
                        <option value="3">New threads hidden</option>
                        <option value="4">Mod Forum</option>
                        <option value="5">Administrator Forum</option>
                    </select>
                </td>
            </tr>
            <tr><td>Forum Name</td><td><input type="text" name="forum" class="button" maxlength="30"></td></tr>
            <tr>
                <td>Description</td>
                <td><textarea name="description" rows="7" cols="45" class="button"></textarea></td>
            </tr>
            <tr>
                <td>Category</td>
                <td>
                <select name="category" class="button" id="pos_selector">';
    
                //add all the available categories as an option
                foreach($forum_index->retrieveCategories($rank) as $category)
                {
                    $content .= '<option value="'. $category['id'] .'">'. $category['title'] .'</option>';
                }
                    
    $content .= '
                </select>
                </td>
            </tr>
            <tr>
                <td>Position</td>
                <td><input type="text" name="pos" id="pos" size="7" class="button"> (automatically makes it last)</td>
            </tr>
            <tr>
                <td>Double Posting</td>
                <td><input type="checkbox" value="1" name="double_posting"></td>
            </tr>
            <tr>
                <td><input type="submit" value="Add Forum" class="button"></td>
            </tr>
            </table>
        </form>
    ';
}
else
{
    //add forum
    if(strlen($_POST['forum']) > 50)
    {
        $content = 'The forum cannot have name larger than fifty characters.';
    }
	elseif(strlen($_POST['description']) < 3)
	{
		$content = 'The description must be at least 3 characters.';
	}
    elseif(!$forum->catExists($_POST['category']))
    {
        $content = 'The chosen category doesn\'t exist.';
    }
    elseif(!ctype_digit($_POST['pos']))
    {
        $content = 'The position must be a number.';
    }
    else
    {
        $database->processQuery("INSERT INTO `forums` VALUES (null, ?, ?, ?, ?, ?, ?, ?)", array($_POST['category'], $_POST['pos'], $_POST['forum'], $_POST['description'], $_POST['icon'], $_POST['type'], (isset($_POST['double_posting'])) ? 1 : 0), false);
        
        //forum addition successful
        $content = 'You have successfully added the forum <b>'. $_POST['forum'] .'</b>. <a href="addforum.php">Add another!</a>';
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
                    <b>Add a forum</b><br>
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
<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript" src="../js/addforum.js"></script>
</html>
