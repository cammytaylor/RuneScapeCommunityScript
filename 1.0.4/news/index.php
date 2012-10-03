<?php
require('../includes/config.php');
require('../structure/database.php');
require('../structure/base.php');
require('../structure/user.php');

$database = new database($db_host, $db_name, $db_user, $db_password);
$user = new user($database);
$user->updateLastActive();

//set # of articles to show per page
$per_page = 10;                                                           
                                                        
//validate category if set
$category = (isset($_GET['cat']) && in_array($_GET['cat'], array(1,2,3,4,5,6))) ? $category = $_GET['cat'] : $_GET['category'] = 0;

//if the category is set, we want to select get the # of pages for THAT category
($category != 0) ? $database->processQuery("SELECT * FROM `news` WHERE `category` = ?", array($category), false) : $database->processQuery("SELECT * FROM `news`", array(), false);

//calculate number of pages
$pages = ceil($database->getRowCount() / $per_page);

//set some variables
$page = ($_GET['page'] > $pages || !ctype_digit($_GET['page'])) ? $page = 1 : $page = $_GET['page'];
$start = ($page-1)*$per_page;

if($database->getRowCount() >= 1)
{
    
    
    //extract all the articles
   if($category != 0)
        $articles = $database->processQuery("SELECT `id`,`title`,`date`,`category` FROM `news` WHERE `category` = ? ORDER BY `id` DESC LIMIT $start,$per_page", array($category), true);
    else
        $articles = $database->processQuery("SELECT `id`,`title`,`date`,`category` FROM `news` ORDER BY `id` DESC LIMIT $start,$per_page", array(), true);
    
    foreach($articles as $article)
    {
        //get the correct icon
        //what picture to use
        switch($article['category'])
        {
                case 1:
                $title = 'Website Update';
                $img = '../img/news/mini_website.gif';
                break;

                case 2:
                $title = 'Game Update';
                $img = '../img/news/mini_game_updates.gif';
                break;

                case 3:
                $title = 'Shop Update';
                $img = '../img/news/mini_shop.gif';
                break;

                case 4:
                $title = 'Customer Support';
                $img = '../img/news/mini_customer_support.gif';
                break;

                case 5:
                $title = 'Technical';
                $img = '../img/news/mini_technical.gif';
                break;

                case 6:
                $title = 'Behind the Scenes';
                $img =  '../img/news/mini_behind_the_scenes.gif';
                break;
        }
        
        $content .= '
        <tr><td style="color: black;"><img src="'. $img .'" />&nbsp;'. htmlentities($title, ENT_NOQUOTES) .'</td>
        <td><a href="viewarticle.php?id='. $article['id'] .'">'. $article['title'] .'</a></td>
        <td align="right" style="color: black;">'. $article['date'] .'</td>
        </tr>';
    }
        
}
else
{
    $content = '<tr><td>No news to display.</td></tr>';
}

if($page > 1) {
    $navbar .= '
    <a href="index.php?cat='. $category .'&page='. ($page-1) .'"><img width="30" height="15" alt="Next" title="Next" src="../img/news/arrow_back_first.gif" /></a>&nbsp;
    <a href="index.php?cat='. $category .'&page=1"><img width="30" height="15" alt="Last" title="Last" src="../img/news/arrow_back.gif" /></a>';
}

$navbar .= '<form action="index.php" method="post" style="display: inline">
            <input type="hidden" name="cat" value="'. $category .'" />
            page <input type="text" name="page" size="2" maxlength="4" value="'. $page .'" style="color:#FFFFFF;font-size: 10px; background-color:black; text-align:center;" />&nbsp;of '. $pages .'
            </form>';

if($page < $pages) {
$navbar .= '
<a href="index.php?cat='. $category .'&page='. ($page+1) .'"><img width="30" height="15" alt="Next" title="Next" src="../img/news/arrow_forward.gif" /></a>&nbsp;
<a href="index.php?cat='. $category .'&page='. $pages .'"><img width="30" height="15" alt="Last" title="Last" src="../img/news/arrow_forward_last.gif" /></a>';
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
<link href="../css/main/title-5.css" rel="stylesheet" type="text/css" media="all">
<link rel="shortcut icon" href="../img/favicon.ico" />
<style>
div.topnav {
	text-align: center;
	margin-bottom: 5px;
}

div.topnav img {
	vertical-align: bottom;
}

div.bottomnav {
	text-align: center;
	margin-top: 10px;
}

div.bottomnav img {
	vertical-align: bottom;
}

.newstitlebground {
	margin-left: auto;
	margin-right: auto;
	padding: 4px;
	width: 759px;
	height: 130px;
	background: url('../img/news/header.gif') no-repeat;
}

.newstitleframe {
	width: 165px;
	margin-left: auto;
	margin-right: auto;
	padding: 4px;
	border: 2px solid #382418;
	background-color: black;
	margin-top: 50px;
}
</style>
<?php include('../includes/google_analytics.html'); ?>
</head>
		<div id="body">
		<div style="text-align:center;">
			<div class="newstitlebground">
				<div class="newstitleframe">
					<b>Latest News</b><br />
					
					<a href="../index.php">Main Menu</a> - <a href="index.php">News List</a>
				</div>
			</div>
		</div>
			
			<img class="widescroll-top" src="../img/scroll/backdrop_765_top.gif" alt="" width="765" height="50" />
			<div class="widescroll">
				<div class="widescroll-bgimg">
									<div class="widescroll-content">
					<div style="text-align: center">
						<img src="../img/news/mini_all_categories.gif" />&nbsp;<a href="index.php?cat=0" class="white">All Categories</a> -
						<img src="../img/news/mini_game_updates.gif" />&nbsp;<a href="index.php?cat=2" class="white">Game Updates</a> - 
						<img src="../img/news/mini_website.gif" />&nbsp;<a href="index.php?cat=1" class="white">Website</a> -
						<img src="../img/news/mini_shop.gif" />&nbsp;<a href="index.php?cat=3" class="white">Shop</a> -
						<img src="../img/news/mini_customer_support.gif" />&nbsp;<a href="index.php?cat=4" class="white">Customer Support</a><br />
						<img src="../img/news/mini_technical.gif" />&nbsp;<a href="index.php?cat=5" class="white">Technical</a> -
						<img src="../img/news/mini_behind_the_scenes.gif" />&nbsp;<a href="index.php?cat=6" class="white">Behind the Scenes</a>
					</div>
					<br />
					<div class="topnav">
                                            <?php echo $navbar; ?>
					</div>
                                        
					<div style="width:100%;">
						<table border="0" style="width: 100%;">
							<tr>
								<td width="30%" style="color: black;"><b>Category</b></td>
								<td width="50%" style="color: black;"><b>News Item</b></td>
								<td align="right" width="20%" style="color: black;"><b>Date</b>
								</td>
							</tr>
                                                        <?php
                                                            echo $content;
                                                        ?>
						</table>
					</div>
                                        
					<div class="bottomnav">
                                            <?php echo $navbar; ?>
					</div>
				</div>
				</div>
			</div>
			<img class="widescroll-bottom" src="../img/scroll/backdrop_765_bottom.gif" alt="" width="765" height="50" />


		<div class="tandc"><?php echo $data['wb_title']; ?></div>

	</div>
	</body>
</html>