<?php
require('../includes/config.php');
require('../structure/database.php');
require('../structure/base.php');
require('../structure/user.php');
require('../structure/msgcenter.php');

$database = new database($db_host, $db_name, $db_user, $db_password);
$base = new base($database);
$msgcenter = new msgcenter($database);
$user = new user($database);
$user->updateLastActive();

$username = $user->getUsername($_COOKIE['user'], 2);
$rank = $user->getRank($username);

if(!$user->isLoggedIn()) $base->redirect('../index.php');
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
<link href="../css/msgcenter.css" rel="stylesheet" type="text/css" media="all">
<link rel="shortcut icon" href="../img/favicon.ico" />
<style>

</style>
<?php include('../includes/google_analytics.html'); ?>
</head>
	<div id="body">

                <div style="text-align: center; background: none;">
                    <div class="header">
                    <div class="msg_titleframe">
                        <b>Your Message Centre</b><br>(last 3 months)<br/>
                        <a href="../index.php">home</a>
                    </div>
                    </div>
                </div>
            
                <br/>
                <div class="frame wide_e">
                    <a href="create.php"><img src="../img/forum/new_thread.gif">Create new Conversation</a>
                </div>
                <br/>
                <div id="msg_c_container">
                    <div id="t_container">
                        <img src="../img/msgcenter/received.gif">
                        <div id="t_container_bottom">
                            
                                <?php
                                    //get new replies
                                    //administrators should receive ALL newly created conversations that aren't closed/solved, so they can reply to them
                                    if($rank < 4)
                                        $new = $database->processQuery("SELECT `id`,`title`,`date` FROM `messages` WHERE (`opened` = '0' AND `receiver` = ?) OR (`opened` = 0 AND `receiver` = '*') AND ". time() ." - `timestamp` < '7889231' ORDER BY `date` DESC", array($username), true);
                                    else
                                        $new = $database->processQuery("SELECT `id`,`title`,`date` FROM `messages` WHERE (`opened` = 0 AND `receiver` = ?) OR (`lastreply` <> ? AND `receiver` = '!' AND `status` = 0) AND ". time() ." - `timestamp` < '7889231' ORDER BY `date` DESC", array($username, $username), true);
                                        
                                    if($database->getRowCount())
                                    {
                                        foreach($new as $message)
                                        {
                                            ?>
                                                <table>
                                                <tr>
                                                    <td width="15%"><a href="viewmessage.php?id=<?php echo $message['id']; ?>"><?php echo stripslashes($message['title']); ?></a></td>
                                                    <td width="5%"><?php echo $msgcenter->getReplies($message['id']); ?></td>
                                                    <td width="10%"><?php echo $message['date']; ?></td>
                                                    <td width="10%" style="border:0px"><a href="reply.php?id=<?php echo $message['id']; ?>"><img src="../img/msgcenter/button1.png"></a></td>
                                                </tr>
                                                </table>
                                            <?php
                                        }
                                    }
                                    else
                                    {
                                        echo '<tr><td>No conversations in this section.</td></tr>';
                                    }
                                ?>
                        </div>
                    </div>
                    
                    <br/>
                    
                    <div id="t_container">
                        <img src="../img/msgcenter/sent.gif">
                        <div id="t_container_bottom">
                                <?php
                                    //get new replies
                                    $sent = $database->processQuery("SELECT `id`,`title`,`date` FROM `messages` WHERE `creator` = ? AND ". time() ." - `timestamp` < '7889231' ORDER BY `date` DESC", array($username), true);
                                
                                    if($database->getRowCount())
                                    {
                                        foreach($sent as $message)
                                        {
                                            ?>
                                                <table>
                                                <tr>
                                                    <td width="15%"><a href="viewmessage.php?id=<?php echo $message['id']; ?>"><?php echo stripslashes($message['title']); ?></a></td>
                                                    <td width="5%"><?php echo $msgcenter->getReplies($message['id']); ?></td>
                                                    <td width="10%"><?php echo $message['date']; ?></td>
                                                    <td width="10%" style="border:0px"><a href="reply.php?id=<?php echo $message['id']; ?>"><img src="../img/msgcenter/button1.png"></a></td>
                                                </tr>
                                                </table>
                                            <?php
                                        }
                                    }
                                    else
                                    {
                                        echo 'No conversations in this section.';
                                    }
                                    
                                ?>
                        </div>
                    </div>
                    
                    <br/>
                    
                    <div id="t_container">
                        <img src="../img/msgcenter/read.gif">
                        <div id="t_container_bottom">
                                <?php
                                    if($rank < 4)
                                        $read = $database->processQuery("SELECT `id`,`title`,`date` FROM `messages` WHERE `opened` = '1' AND `receiver` = ? AND ". time() ." - `timestamp` < '7889231' ORDER BY `date` DESC", array($username), true);
                                    else
                                        $read = $database->processQuery("SELECT `id`,`title`,`date` FROM `messages` WHERE (`opened` = 1 AND `receiver` = ?) OR (`lastreply` = ? AND `receiver` = '!') OR `status` = 1 AND ". time() ." - `timestamp` < '7889231' ORDER BY `date` DESC", array($username, $username), true);    
                                
                                    if($database->getRowCount())
                                    {
                                        foreach($read as $message)
                                        {
                                            ?>
                                                <table>
                                                <tr>
                                                    <td width="15%"><a href="viewmessage.php?id=<?php echo $message['id']; ?>"><?php echo stripslashes($message['title']); ?></a></td>
                                                    <td width="5%"><?php echo $msgcenter->getReplies($message['id']); ?></td>
                                                    <td width="10%"><?php echo $message['date']; ?></td>
                                                    <td width="10%" style="border:0px"><a href="reply.php?id=<?php echo $message['id']; ?>"><img src="../img/msgcenter/button1.png"></a></td>
                                                </tr>
                                                </table>
                                            <?php
                                        }
                                    }
                                    else
                                    {
                                        echo 'No conversations in this section.';
                                    }
                                ?>
                        </div>
                    </div>
                    
                </div>
                <br/>
        <div class="tandc"><?php echo $data['wb_foot']; ?></div>
</body>
</html>
