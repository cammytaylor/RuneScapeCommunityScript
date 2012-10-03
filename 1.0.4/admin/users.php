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
                    <b>User List</b><br>
                    <a href="../index.php" class=c>Main Menu</a> - <a href="index.php">Back</a>
                </div>
                </div>

		<img class="widescroll-top" src="../img/scroll/backdrop_765_top.gif" alt="" width="765" height="50" />
		<div class="widescroll">
			<div class="widescroll-bgimg">

				<div class="widescroll-content">
                                    <div id="black_fields">
                                        There is a total of <?php echo $base->userCount(); ?> registered users.
                                        <?php
                                            //get the # of users
                                            $database->processQuery("SELECT * FROM `users`", array(), false);
                                        
                                            //pagination
                                            $per_page = 25;
                                            $pages = ceil($database->getRowCount() / $per_page);
                                            
                                            //current page
                                            $page = ($_GET['page'] < 1 || $_GET['page'] > $pages || !ctype_digit($_GET['page'])) ? 1 : $_GET['page'];
                                            
                                            //where to start at when extracting
                                            $start = ($page-1)*$per_page;
                                            
                                            //query to draw user list
                                            $users = $database->processQuery("SELECT `username` FROM `users` ORDER BY `username` ASC LIMIT $start,$per_page", array(), true);
                                            
                                            ?>
                                        
                                                <table cellspacing="4" cellpadding="3">
                                                    <?php
                                                        //place holder
                                                        $ph = 0;
                                                        foreach($users as $user){
                                                            $ph++;
                                                            echo '<tr><td><font size="3"><b>#'. $ph .':</b> '.$user['username'].'</font></td></tr>';
                                                        }
                                                    ?>
                                                </table>
                                        
                                            <?php
                                            
                                            if($page > $pages) echo '<a href="?page='. ($page-1) .'">Prev</a>';
                                            
                                            for($i = 1; $i <= $pages; $i++){
                                                echo ($i == $page) ? '<a href="?page='. $i .'">['. $i .']</a>&nbsp;' : '<a href="?page='. $i .'">'. $i .'</a>&nbsp;';
                                            }
                                            
                                            if($page < $pages) echo '<a href="?page='. ($page+1) .'">Next</a>';
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
