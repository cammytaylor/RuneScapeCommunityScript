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

if(!isset($_REQUEST['cat']))
{
	
	$content = '
	<form action="editcat.php" method="POST">
	<select name="cat" class="button">';
	
	foreach($forum_index->retrieveCategories($rank) as $category)
	{
		$content .= '<option value="'. $category['id'] .'">'. $category['title'] .'</option>';
	}
	
	$content .= '<input type="submit" value="Edit"></select></form>';
}
else
{
	//make sure it exists
	if(!$forum->catExists($_REQUEST['cat']))
	{
		$content = 'No category exists with the given ID.';
	}
	else
	{
		if(isset($_REQUEST['delete']))
		{
			if(!isset($_REQUEST['confirm']))
			{
				$content = 'Are you sure you wish to delete this category and all forums/threads/posts a long with it? <a href="?cat='. $_REQUEST['cat'] .'&delete=1&confirm=1">Yes!</a> | <a href="?cat='. $_REQUEST['cat'] .'">Back</a>';
			}
			else
			{
				//delete everything belonging to this category
				foreach($forum_index->retrieveSubForums($_REQUEST['cat']) as $a_forum)
				{
					//get all threads and posts in the forum, deleting starting at posts
					$threads = $database->processQuery("SELECT * FROM `threads` WHERE `parent` = ?", array($a_forum['id']), true);
					foreach($threads as $thread)
					{
						//delete all posts to this thread
						$database->processQuery("DELETE FROM `posts` WHERE `thread` = ?", array($thread['id']), false);
					}
					//delete all the threads
					$database->processQuery("DELETE FROM `threads` WHERE `parent` = ?", array($a_forum['id']), false);
					
					//delete the forum
					$database->processQuery("DELETE FROM `forums` WHERE `id` = ?", array($a_forum['id']), false);
				}
				
				//delete the category itself
				$database->processQuery("DELETE FROM `cats` WHERE `id` = ?", array($_REQUEST['cat']), false);
				
				$content = 'Delete successful!';
			}
		}
		else
		{
			$cat = $database->processQuery("SELECT `title`,`id`,`type`,`pos` FROM `cats` WHERE `id` = ?", array($_REQUEST['cat']), true);
		
			if(!isset($_POST['category']))
			{
				$content = '
				<form action="editcat.php?cat='. $cat[0]['id'] .'" method="POST">
					<table>
						<tr>
							<td>Category</td>
							<td><input type="text" name="category" class="button" value="'. $cat[0]['title'] .'"></td>
						</tr>
						<tr>
							<td>Staff-Only</td>
							<td><input type="checkbox" name="staff" value="1"';

							if($cat[0]['type'] == 1) $content .= 'checked="checked"';
				
				$content .=	'></td></tr>
						<tr>
							<td>Position</td>
							<td><input type="text" name="pos" value="'. $cat[0]['pos'] .'" class="button"></td>
						</tr>
						<tr>
							<td><input type="submit" value="Edit Category"></td>
							<td><input type="submit" value="Delete Category" name="delete"></td>
						</tr>
					</table>
				</form>
				';
			}
			elseif(strlen($_POST['category']) > 50)
			{
				$content = 'The name of the category cannot be greater than fifty characters.';
			}
			else
			{
				//update the category
				$database->processQuery("UPDATE `cats` SET `type` = ?, `title` = ? WHERE `id` = ?", array($_POST['staff'], $_POST['category'], $_REQUEST['cat']), false);
				
				$content = 'You have successfully updated the category! <a href="index.php">ACP Home</a>';
			}
		}
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
                    <b>Edit Category</b><br>
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
<script type="text/javascript" src="../js/addcat.js"></script>
</html>
