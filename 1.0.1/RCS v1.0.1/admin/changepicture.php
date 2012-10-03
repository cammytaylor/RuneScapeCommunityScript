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
                            <b>Administration</b><br> <a href="../index.php" class=c>Main Menu</a> - <a href="index.php">Home</a>
                        </div>
                        </div>

		<img class="widescroll-top" src="../img/scroll/backdrop_765_top.gif" alt="" width="765" height="50" />
		<div class="widescroll">
			<div class="widescroll-bgimg">
				<div class="widescroll-content">
                                        <?php
                                            $details = explode('.', strtolower($_FILES['file']['name']));
                                        
                                            if(!isset($_FILES['file']))
                                            {
                                                ?>
                                    
                                                    <form action="changepicture.php" method="POST" enctype="multipart/form-data">
                                                        <table>
                                                            <tr><td>NOTICE</td><td>Uploading this picture will automatically delete the old one uploaded.</td></tr>
                                                            <tr><td>Caption</td><td><textarea name="caption" rows="12" cols="35" maxlength="750"></textarea></td></tr>
                                                            <tr><td>Choose File</td><td><input type="file" name="file"></td></tr>
                                                            <tr><td>Upload</td><td><input type="submit" value="Upload Picture"></td></tr>
                                                        </table>
                                                    </form>
                                    
                                                <?php
                                            }
                                            elseif(file_exists('../img/dailyscreenshots/'. $_FILES['file']['name']))
                                            {
                                                echo 'This screenshot already exists! <input type="button" value="Back" onclick="goBack()" />';
                                            }
                                            elseif(strlen($details[0]) > 50)
                                            {
                                                echo 'The file cannot have a name that is greater than fifty characters.';
                                            }
                                            else
                                            {
                                                
                                                if(!in_array($details[1], array('png')))
                                                {
                                                    echo 'You can\'t upload that file type. Only PNG allowed. <input type="button" value="Back" onclick="goBack()" />';
                                                }
                                                else
                                                {
                                                    //move new file
                                                    move_uploaded_file($_FILES['file']['tmp_name'], '../img/dailyscreenshots/'. $_FILES['file']['name']);
                                                    
                                                    //replace old caption with the new
                                                    $database->processQuery("INSERT INTO `dailyscreenshots` VALUES (null, ?, ?)", array($_FILES['file']['name'], nl2br($_POST['caption'])), false);
                                                    
                                                    echo 'The picture has been uploaded along with the caption! <a href="../screenshots.php">View here</a>';
                                                }
                                            }
                                        ?>
				</div>
			</div>
		</div>
		<img class="widescroll-bottom" src="../img/scroll/backdrop_765_bottom.gif" alt="" width="765" height="50" />	

		<div class="tandc"><?php echo $data['wb_foot']; ?></div>
</body>
</html>
