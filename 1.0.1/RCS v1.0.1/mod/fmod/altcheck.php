<?php
require('../../includes/config.php');
require('../../structure/database.php');
require('../../structure/base.php');
require('../../structure/user.php');

$database = new database($db_host, $db_name, $db_user, $db_password);
$base = new base($database);
$user = new user($database);
$user->updateLastActive();

$username = $user->getUsername($_COOKIE['user'], 2);
$rank = $user->getRank($username);

if($rank < 3) $base->redirect('../../index.php');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns:IE>
<head>
<meta http-equiv="Expires" content="0">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache">
<meta name="MSSmartTagsPreventParsing" content="TRUE">
<title><?php echo $data['wb_title']; ?></title>
<link href="../../css/basic-3.css" rel="stylesheet" type="text/css" media="all">
<link rel="shortcut icon" href="../../img/favicon.ico" />
<?php include('../../includes/google_analytics.html'); ?>
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
                            <b>Moderation</b><br> <a href="../../index.php" class=c>Main Menu</a>  - <a href="index.php">Back</a>
                        </div>
                        </div>

		<img class="widescroll-top" src="../../img/scroll/backdrop_765_top.gif" alt="" width="765" height="50" />
		<div class="widescroll">
			<div class="widescroll-bgimg">

				<div class="widescroll-content">
                                        <?php
                                            if(!isset($_POST['username']) && !isset($_GET['username']))
                                            {
                                                ?>
                                    
                                                    <table>
                                                        <form action="altcheck.php" method="POST">
                                                            <tr><td>Username:</td><td><input type="text" name="username" maxlength="12"> <input type="submit" value="Lookup"></td></tr>
                                                        </form>
                                                    </table>
                                    
                                                <?php
                                            }
                                            elseif(isset($_GET['username']) && $user->doesExist($_GET['username']))
                                            {
                                                ?>
                                                    <center><input type="button" class="button" value="Back" onclick="goBack()" /></center>
                                                    Viewing alternative accounts for: <b><?php echo $_GET['username']; ?></b>
                                                    <table cellspacing="6" style="border:1x dotted white;">
                                                        <?php
                                                            foreach(queryAlts($_GET['username'], $user->getUserIp($_GET['username']), $database) as $user)
                                                            {
                                                                ?>
                                                                    <tr><td><?php echo $user['username']; ?></td><td><a href="userinformation.php?username=<?php echo $user['username']; ?>">View Info</a></td></tr>
                                                                <?php
                                                            }
                                                        ?>
                                                    </table>
                                    
                                                <?php
                                            }
                                            else
                                            {
                                                //search
                                                $search = $database->processQuery("SELECT `username`,`ip` FROM `users` WHERE `username` LIKE ? ORDER BY `username` ASC", array('%'.$_POST['username'].'%'), true);
                                            
                                                if($database->getRowCount() >= 1)
                                                {
                                                    ?> <center><input type="button" class="button" value="Back" onclick="goBack()" /></center><table cellspacing="6"> <?php
                                                    
                                                    foreach($search as $result)
                                                    {
                                                        //retrieve the number of alts they have
                                                        queryAlts($result['username'], $result['ip'], $database);
                                                        $alts = $database->getRowCount();
                                                        
                                                        if($alts >= 1)
                                                        {
                                                            ?>
                                                                <tr><td><a href="altcheck.php?username=<?php echo $result['username']; ?>"><?php echo $result['username']; ?></a></td><td>(<?php echo $alts; ?> alts)</td></tr>
                                                            <?php
                                                        }
                                                    }
                                                    
                                                    ?> </table> <?php
                                                }
                                                else
                                                {
                                                    echo 'Your search didn\'t return any results. <input type="button" class="button" value="Back" onclick="goBack()" />';
                                                }
                                            }
                                            
                                            function queryAlts($username, $ip, database $database)
                                            {
                                                return $database->processQuery("SELECT `username` FROM `users` WHERE `ip` = ? AND `username` <> ? ORDER BY `username` ASC", array($ip, $username), true);
                                            }
                                        ?>
				</div>
			</div>
		</div>
		<img class="widescroll-bottom" src="../../img/scroll/backdrop_765_bottom.gif" alt="" width="765" height="50" />	

		<div class="tandc"><?php echo $data['wb_foot']; ?></div>
</body>
</html>
