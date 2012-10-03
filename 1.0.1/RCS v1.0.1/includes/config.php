<?php
$domain = $_SERVER['HTTP_HOST'];

//check for installation file
if(is_dir('install')) die('<a href="http://'. $domain .'/install/">Install RCS</a><br/><br/><a href="http://rcscript.comlu.com/" target="_blank">Official Support Forums for RCS</a>');

//basic site configuration
$data['wb_name'] = '{wb_name}';
$data['wb_abbr'] = '{abbr}';
$data['wb_title'] = '{title}';
$data['wb_foot'] = 'This website and its contents are copyright &copy; 1999 - 2007 Jagex Ltd.<br/>
Use of this website is subject to our Terms+Conditions and Privacy policy<br/>Powered by RuneScape Community Script (RCS)';
$data['login_time'] = 50000; //SECONDS

//if you change $data['use_recaptcha'] to true, you must specify a private and public keycode
//^^^^^^^^^^^^
$data['use_recaptcha'] = false; //true = use google's recaptcha, false = don't
$data['public_key'] = ''; //public key given to you by google
$data['private_key'] = ''; //private key given to you by google

$maintenance_url = $domain.'/maintenance.php'; //url for maintenance page

//database connection settings
$db_host = '{host}';
$db_user = '{user}';
$db_name = '{name}';
$db_password = '{pass}';
?>
