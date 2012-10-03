<?php
include('includes/config.php');
if(isset($_COOKIE['user'])) setcookie('user', null, time()-$data['login_time'], '/', '.'.$domain);
header('location: index.php');
?>
