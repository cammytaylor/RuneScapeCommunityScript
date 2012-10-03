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
$id = $_GET['id'];

if(!$user->isLoggedIn()) $base->redirect('../index.php');
if(!$msgcenter->canView($_GET['id'], $username, $rank)) $base->redirect('index.php');

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
                                    <b>Viewing Message Exchange</b><br/><a href="index.php">Back to Message Centre</a>
                                </div>
                            </div>
                </div>
                <br/>
                <br/>
                
                <?php
                    //extract the messagent
                    $message = $database->processQuery("SELECT `message`,`creator`,`date`,`receiver` FROM `messages` WHERE `id` = ? LIMIT 1", array($id), true);
                    
                    //mark the message as read if the user is the receiver
                    if($message[0]['receiver'] == $username) $database->processQuery("UPDATE `messages` SET `opened` = 1 WHERE `id` = ?", array($_GET['id']), false);
                    
                    //get appropriate icons
                    if($user->getRank($message[0]['creator']) > 3)
                    {
                        $avatar = '<img src="../img/msgcenter/admin.png">';
                        $icon = '<img src="../img/msgcenter/staff.png">';
                    }
                    else
                    {
                        $avatar = '<img src="../img/msgcenter/sword.png">';
                        $icon = '<img src="../img/msgcenter/player.png">';
                    }
                ?>
                
                    <div class="frame wide_e">
                        <a href="reply.php?id=<?php echo $id; ?>"><img src="../img/forum/new_thread.gif">Reply to Conversation</a>
                        <?php if($rank > 3) echo '<a href="solve.php?id='. $id .'"><img src="../img/forum/locked.gif"> '. ($msgcenter->isSolved($id) ? 'Mark as Unsolved' : 'Mark as Solved') .'</a>&nbsp;
                                                  <a href="deletemessage.php?id='. $id .'"><img src="../img/msgcenter/delete.png"> Delete Conversation</a>'; ?>
                    </div>
                
                    <br/>
                    
                    <table style="width:765px; border:2px solid #54472B;" cellspacing="1">
                    <tr>
                        <td style="width:125px; background-color:#8A754A; padding:3px; text-align:center; vertical-align:text-top; color:#45372A;">
                            <?php echo $message[0]['creator']; ?><br/><br/><?php echo $avatar; if($rank > 3) echo '<br/><a href="editmessage.php?id='. $_GET['id'] .'">Edit</a>'; ?>
                        </td>
                        
                        <td style="background-color:#45372A; width:450px; padding:6px;">
                                <?php echo ($user->getRank($message[0]['creator']) < 4) ? $base->br2nl(stripslashes(htmlentities($message[0]['message']))) : stripslashes($message[0]['message']); ?>
                        </td>
                        
                        <td style="width:125px; background-color:#8A754A; padding:3px; text-align:right; vertical-align:text-top; color:#45372A;">
                            <?php
                                $exploded = explode(' ', $message[0]['date']);
                                echo $exploded[0].'<br/>'.$exploded[1].'<br/><center>'. $icon.'</center>';
                            ?>
                        </td>
                    </tr>
                    </table>
                    <br/>
                    
                    
                <?php
                    //get all the replies
                    $replies = $database->processQuery("SELECT `id`,`username`,`content`,`date` FROM `replies` WHERE `conversation` = ?", array($id), true);
                    
                    foreach($replies as $reply)
                    {
                        //get appropriate icons
                        if($user->getRank($reply['username']) > 3)
                        {
                            $avatar = '<img src="../img/msgcenter/admin.png">';
                            $icon = '<img src="../img/msgcenter/staff.png">';
                        }
                        else
                        {
                            $avatar = '<img src="../img/msgcenter/sword.png">';
                            $icon = '<img src="../img/msgcenter/player.png">';
                        }
                        
                        ?>
                    
                        <table style="width:765px; border:2px solid #54472B;" cellspacing="1">
                        <tr>
                            <td style="width:125px; background-color:#8A754A; padding:3px; text-align:center; vertical-align:text-top; color:#45372A;">
                                <?php echo $reply['username']; ?><br/><br/><?php echo $avatar; if($rank > 3) echo '<br/><a href="deletereply.php?id='. $reply['id'] .'&convo='. $_GET['id'] .'">Delete</a> 
                                                                                                                   | <a href="editreply.php?id='. $reply['id'] .'&convo='. $_GET['id'] .'">Edit</a>'; ?>
                            </td>

                            <td style="background-color:#45372A; width:450px; padding:6px;">
                                    <?php echo ($user->getRank($reply['username']) < 4) ? $base->br2nl(stripslashes(htmlentities($reply['content']))) : stripslashes($reply['content']); ?>
                            </td>

                            <td style="width:125px; background-color:#8A754A; padding:3px; text-align:right; vertical-align:text-top; color:#45372A;">
                                <?php
                                    $exploded = explode(' ', $reply['date']);
                                    echo $exploded[0].'<br/>'.$exploded[1].'<br/><center>'. $icon.'</center>';
                                ?>
                            </td>
                        </tr>
                        </table>
                        <br/>
                    
                        <?php
                    }
                ?>
                
        <div class="tandc"><?php echo $data['wb_foot']; ?></div>
</body>
</html>
