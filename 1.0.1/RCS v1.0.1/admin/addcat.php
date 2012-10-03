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

//get the new position
$pos = $database->processQuery("SELECT `pos` FROM `cats` ORDER BY `pos` DESC LIMIT 0,1", array(), true);

//make sure there is at least ONE category, else make the default POS 1
if($database->getRowCount() == 0)
	$pos = 1;
else
	$pos = $pos[0]['pos']+1;

if(!isset($_POST['category']) || !ctype_digit($_POST['pos']))
{
    $content = '
        <form action="addcat.php" method="POST">
            <table>
                <tr>
                    <td>Category</td>
                    <td><input type="text" name="category" class="button"></td>
                </tr>
                <tr>
                    <td>Staff-Only</td>
                    <td><input type="checkbox" name="staff" value="1"></td>
                </tr>
				<tr>
					<td>Position</td>
					<td><input type="text" name="pos" value="'. $pos .'" class="button"></td>
				</tr>
                <tr>
                    <td><input type="submit" value="Add Category" class="button"></td>
                </tr>
            </table>
        </form>
    ';
}
else
{
    //add forum
    if(strlen($_POST['category']) > 50)
    {
        $content = 'The category cannot have name larger than fifty characters.';
    }
    else
    {
        $database->processQuery("INSERT INTO `cats` VALUES (null, ?, ?, ?)", array($_POST['category'], (isset($_POST['staff'])) ? 1 : 0, $_POST['pos']), false);
        
        //forum addition successful
        $content = 'You have successfully added the category <b>'. $_POST['category'] .'</b>. <a href="addcat.php">Add another!</a>';
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
                    <b>Add a category</b><br>
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
