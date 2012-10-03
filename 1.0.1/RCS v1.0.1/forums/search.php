<?php
require('../includes/config.php');
require('../structure/database.php');
require('../structure/forum.php');
require('../structure/forum.index.php');
require('../structure/user.php');

$database = new database($db_host, $db_name, $db_user, $db_password);
$user = new user($database);
$forum = new forum($database);
$forum_index = new forum_index($database);

$user->updateLastActive();

$username = $user->getUsername($_COOKIE['user'], 2);
$rank = $user->getRank($username);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns:IE>
<head>
<meta http-equiv="Expires" content="0">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache">
<meta name="MSSmartTagsPreventParsing" content="TRUE">
<title><?php echo $data['wb_title']; ?></title>
<link href="../css/basic-3.css" rel="stylesheet" type="text/css" media="all" />
<link href="../css/forum-3.css" rel="stylesheet" type="text/css" media="all" />
<link href="../css/forummsg-1.css" rel="stylesheet" type="text/css" media="all" />
<link rel="shortcut icon" href="../img/favicon.ico" />
<!--[if IE 8]>
<link rel="stylesheet" type="text/css" href="../css/forummsg-ie-1.css" />
<![endif]-->
<style>
    #compact{
        text-align:center;
        width:50%;
    }
    
    #pack_results {
       width:80%; 
    }
</style>
<?php include('../includes/google_analytics.html'); ?>
</head>
<body>
	<div id="body">
		<?php $forum->getNavBar($username, $rank); ?>
                <br /><br />
                
                <div style="text-align: center; background: none;">
                <div class="titleframe e">
                <b>Search the Forums</b><br>
                <a href="../index.php" class=c>Main Menu</a> - <a href="index.php">Back to Forums</a>
                </div>
                </div>
                <br/>
                <br/>
                <div class="frame wide_e">
                <div style="text-align: justify">
                <center>
                <div id="compact">
                <form action="search.php" method="POST">
                <table cellpadding="3">
                    <tr>
                        <td align="left">
                            Select Location
                        </td>
                        <td align="left">
                            <select name="forum">
                            <option value="all" selected="selected">All</option>
                            <?php
                                $categories = $forum_index->retrieveCategories($rank);
                                
                                foreach($categories as $category)
                                {
                                    $forums = $forum_index->retrieveSubForums($category['id']);
                                    
                                    echo '<option disabled="disabled">'. $category['title'] .'</option>';
                                    
                                    foreach($forums as $forum)
                                    {
                                        echo '<option value="'. $forum['id'] .'">'. $forum['title'] .'</option>';
                                    }
                                }
                            ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td align="left">
                            Search Terms
                        </td>
                        <td align="left">
                            <input type="text" name="keywords" size="42" maxlength="30"> <input type="submit" value="Search">
                        </td>
                    </tr>
                </table>
                </form>
                </div>
                </center>
                    
                <?php
                
                    if(isset($_POST['keywords']) && isset($_POST['forum']))
                    {
                        if(strlen($_POST['keywords']) < 3)
                        {
                            echo '<b>Your search term must be at least 3 characters.</b>';
                        }
                        else
                        {
                            if($_POST['forum'] == 'all')
                            $query = $database->processQuery("SELECT `id`,`title`,`parent`,`date` FROM `threads` WHERE `title` LIKE ? ORDER BY `date` DESC", array('%'. $_POST['keywords'] .'%'), true);
                            else
                                $query = $database->processQuery("SELECT `id`,`title`,`parent`,`date` FROM `threads` WHERE `title` LIKE ? AND parent = ? ORDER BY `date` DESC", array('%'. $_POST['keywords'] .'%', $_POST['forum']), true);

                            ?>
                            
                            <div id="pack_results">
                            <b>Results for "<?php echo htmlentities($_POST['keywords'], ENT_NOQUOTES); ?>" (<?php echo $database->getRowCount(); ?>)</b><br/><table cellpadding="3" cellspacing="0">

                            <?php    
                                
                            foreach($query as $result)
                            {
                                $forum = $database->processQuery("SELECT `title` FROM `forums` WHERE `id` = ?", array($result['parent']), true);

                                //put on a separate line as having it in the echo would be too long
                                $title = '<a href="viewthread.php?forum='. $result['parent'] .'&id='. $result['id'] .'">'. $result['title'] .'</a>';

                                echo '<tr><td align="left"><img src="../img/forum/sword_five.png"></td><td align="left">'. $result['date'] .'</td><td align="left">'. $title .' in forum <a href="viewforum.php?forum='. $result['parent'] .'">'. $forum[0]['title'] .'</a></td></tr>';
                            }

                            ?>
                            </table>
                            </div>
                            <?php
                        }
                    }
                
                ?>
                </div>
                </div>
                
				<div class="tandc"><?php echo $data['wb_foot']; ?></div>
		</div>

	</div>
</body>