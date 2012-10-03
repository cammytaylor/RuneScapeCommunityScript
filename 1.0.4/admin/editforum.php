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

if(!$_REQUEST['id'])
{
	$content = '
        <div id="blackfields">
	<form action="editforum.php" method="GET">
	<select name="id" class="button">';
	
	foreach($forum_index->retrieveCategories($rank) as $cat){
		foreach($forum_index->retrieveSubForums($cat['id']) as $forum){
			$content .= '<option value="'. $forum['id'] .'">'. $forum['title'] .'</option>';
		}
	}
	
	$content .= '</select>
        <input type="submit" value="Select" class="button">
	</form>
        </div>
	';
}
else
{
    if(isset($_GET['delete']) || ($forum->forumExists($_GET['id']) || $forum->forumExists($_POST['id']))){
        if(isset($_GET['delete'])){
            if(!isset($_GET['confirm'])){
                $content = 'Are you sure you wish to delete this forum? All posts and threads within it will be lost. <a href="?id='. $_GET['id'] .'&delete=1&confirm=1">Yes</a> | <a href="editforum.php">No</a>';
            }else{
                //delete the forum, threads, and posts
                $forum->deleteForum($_GET['id']);
                $content = 'The forum was successfully deleted. <a href="index.php">Home</a> | <a href="editforum.php">Edit another forum</a>';
            }
        }
        elseif(!isset($_POST['id'])){
            //forum details
            $f = $database->processQuery("SELECT `icon`,`title`,`type`,`description`,`parent`,`double_posting`,`pos` FROM `forums` WHERE `id` = ? LIMIT 1", array($_GET['id']), true);
            
            //save time
            $type = $f[0]['type'];
            $icon = $f[0]['icon'];
            
            $content = '
            <form action="editforum.php" method="POST">
                <table>
                <input type="hidden" name="id" value="'. $_GET['id'] .'">
                <tr>
                    <td>Delete</td><td><a href="?id='. $_GET['id'] .'?&delete=1">Delete this forum.</a></td>
                </tr>
                <tr>
                <td>Icon</td><td>';
            
                    //show all icons and automatically select the existing selecte done
                    for($i = 1; $i <= 31; $i++){
                        $content .= '<input type="radio" name="icon" value="'. $i .'" '; if($i == $icon) { $content .= 'checked="checked"'; }  $content .= ' /> '. $forum->getIcon($i);
                    }
            
                $content .='
                </td>
                </tr>
                <tr>
                    <td>Type</td>
                    <td>
                        <select name="type" class="button">
                            <option value="1"'; if($type == 1) { $content .= 'selected="selected"'; } $content .= '>Normal</option>
                            <option value="2"'; if($type == 2) { $content .= 'selected="selected"'; } $content .= '>Staff-only threads/topics</option>
                            <option value="3"'; if($type == 3) { $content .= 'selected="selected"'; } $content .= '>New threads hidden</option>
                            <option value="4"'; if($type == 4) { $content .= 'selected="selected"'; } $content .= '>Mod Forum</option>
                            <option value="5"'; if($type == 5) { $content .= 'selected="selected"'; } $content .= '>Administrator Forum</option>
                            <option value="6"'; if($type == 6) { $content .= 'selected="selected"'; } $content .= '>Member Only (guess can\'t see)</option>
                        </select>
                    </td>
                </tr>
                <tr><td>Forum Name</td><td><input type="text" name="forum" class="button" maxlength="30" value="'. stripslashes($f[0]['title']) .'"></td></tr>
                <tr>
                    <td>Description</td>
                    <td><textarea name="description" rows="7" cols="45" class="button">'. stripslashes($f[0]['description']) .'</textarea></td>
                </tr>
                <tr>
                    <td>Category</td>
                    <td>
                    <select name="category" class="button" id="pos_selector">';

                    //add all the available categories as an option
                    foreach($forum_index->retrieveCategories($rank) as $category)
                    {
                        $content .= '<option value="'. $category['id'] .'"';
                        
                        //if it's the forum's category, make it automatically selected
                        if($f[0]['parent'] == $category['id']) $content .= 'selected="selected"';
                        
                        $content .= '>'. $category['title'] .'</option>';
                    }

            $content .= '
                        </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Position</td>
                        <td><input type="text" name="pos" size="7" class="button" value="'. $f[0]['pos'] .'"></td>
                    </tr>
                    <tr>
                        <td>Double Posting</td>
                        <td><input type="checkbox" value="1" name="double_posting" ';
                            //if double posting is enabled, automatically check it
                            if($f[0]['double_posting'] == 1) $content .= 'checked="checked"'; 
                        $content .= '></td>
                    </tr>
                    <tr>
                        <td><input type="submit" value="Update Forum" class="button"></td>
                    </tr>
                    </table>
                </form>
            ';
        }else{
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
                //update the selected forum!
                $database->processQuery("UPDATE `forums` SET `icon` = ?, `title` = ?, `description` = ?, `type` = ?, `parent` = ?, `pos` = ? WHERE `id` = ? LIMIT 1", array($_POST['icon'], $_POST['forum'], $_POST['description'], $_POST['type'], $_POST['category'], $_POST['pos'], $_POST['id']), false);

                //forum addition successful
                $content = 'You have successfully updated the forum! <a href="index.php">Back</a> | <a href="editforum.php">Update another</a>';
            }
        }
    }else{
        $content = 'You chose a non-existing forum.';
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
                    <b>Edit a forum</b><br>
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
