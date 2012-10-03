<?php
require('../includes/config.php');
require('../structure/database.php');

$database = new database($db_host, $db_name, $db_user, $db_password);

/*
 * this file is for addforum.php's and editforum.php's AJAX requests
 */

if(isset($_GET['cat']))
{
    //id of the category
    $cat = $_GET['cat'];
    
    $database->processQuery("SELECT * FROM `cats` WHERE `id` = ?", array($cat), false);
    
    if($database->getRowCount() > 0)
    {
        $query = $database->processQuery("SELECT `pos` FROM `forums` WHERE `parent` = ? ORDER BY `pos` DESC LIMIT 0,1", array($cat), true);
        
        echo $query[0]['pos']+1;
    }
}

?>
