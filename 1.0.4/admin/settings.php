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

if($rank < 4) $base->redirect('../index.php');


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
                    <b>Site Settings</b><br>
                    <a href="../index.php" class=c>Main Menu</a> - <a href="index.php">Back</a>
                </div>
                </div>

		<img class="widescroll-top" src="../img/scroll/backdrop_765_top.gif" alt="" width="765" height="50" />
		<div class="widescroll">
			<div class="widescroll-bgimg">

				<div class="widescroll-content">
                                    <div id="black_fields">
                                        Here you can change the basic settings for your site.
                                        <hr>
                                        <?php
                                            if(!isset($_POST['update']))
                                            {
                                                //get the current settings
                                                $settings = $base->loadConfig();
                                                
                                                ?>
                                        
                                                    <table>
                                                    <form action="settings.php" method="POST">
                                                        <tr><td><input type="checkbox" name="bbcode_members" <?php echo ($settings['bbcode_members'] == 1) ? 'checked="checked"' : ''; ?>>Allow members to use the YouTube BBCode</td></tr>
                                                        <tr><td><input type="checkbox" name="postcount" <?php echo ($settings['postcount'] == 1) ? 'checked="checked"' : ''; ?>>Show post count under posts</td></tr>
                                                        <tr><td><input type="text" name="floodlimit" value="<?php echo $settings['floodlimit']; ?>" size="7"> Floodlimit (seconds)</td></tr>
                                                        <tr><td><input type="text" name="reportforum" value="<?php echo $settings['reportforum']; ?>" size="7"> ID of the forum where reports are stored</td></tr>
                                                        <tr><td><input type="submit" name="update" value="Update Settings"></td></tr>
                                                    </form>
                                                    </table>
                                                        
                                                <?php
                                            }
                                            elseif(!ctype_digit($_POST['floodlimit']) || !ctype_digit($_POST['reportforum']))
                                            {
                                                echo 'Floodlimit and Report Forum fields must be numbers. <input type="button" class="button" value="Back" onclick="goBack()" />';
                                            }
                                            else
                                            {
                                                //array for query
                                                $array = array((isset($_POST['bbcode_members'])) ? 1 : 0, (isset($_POST['postcount'])) ? 1 : 0, (int)$_POST['floodlimit'], (int)$_POST['reportforum']);
                                                
                                                //update the config table
                                                $database->processQuery("UPDATE `config` SET `bbcode_members` = ?, `postcount` = ?, `floodlimit` = ?, `reportforum` = ?", $array, false);
                                            
                                                echo 'Settings updated! <a href="settings.php">Back</a>';
                                            }
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
