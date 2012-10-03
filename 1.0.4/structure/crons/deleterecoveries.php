<?php
require('../../includes/config.php');
require('../../structure/database.php');

$database = new database($db_host, $db_name, $db_user, $db_password);

//delete recoveries that have expired
$database->processQuery("DELETE FROM `recoveries` WHERE `cancel` = 1 AND  ". time() ." - `canceltime` > 1209600 ORDER BY `canceltime` ASC", array(), false);
?>
