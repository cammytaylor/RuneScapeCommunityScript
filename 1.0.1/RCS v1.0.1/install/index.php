<?php
session_start();

if(isset($_SESSION['step1'])){
    header('location: step2.php');
}else{
    if(isset($_POST['host']) && isset($_POST['user']) && isset($_POST['pass']) && isset($_POST['db'])){
        $db = verifyConnection($_POST['host'], $_POST['user'], $_POST['pass'], $_POST['db']);
        if($db){
            $file = '../includes/config.php';
            $data = file_get_contents($file);
            
            //data conversion
            $old = array('{host}', '{user}', '{name}', '{pass}');
            $new = array($_POST['host'], $_POST['user'], $_POST['db'], $_POST['pass']);
            $written = str_replace($old, $new, $data);
            
            $f = fopen($file, 'w');
            fwrite($f, $written);
            fclose($f);
            
            //IMPLEMENT SQL TABLES & ROWS & DATA
            //banned_ips
            $db->query("CREATE TABLE `banned_ips` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `ip` varchar(20) COLLATE latin1_german2_ci NOT NULL,
            PRIMARY KEY (`id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci AUTO_INCREMENT=1 ;");
            
            //cats
            $db->query("CREATE TABLE `cats` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `title` varchar(40) COLLATE latin1_german2_ci NOT NULL,
            `type` int(11) NOT NULL,
            `pos` int(11) NOT NULL,
            PRIMARY KEY (`id`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci AUTO_INCREMENT=2 ;");
            $db->query("INSERT INTO `cats` VALUES(1, 'Staff', 1, 1)");
            $db->query("INSERT INTO `cats` VALUES(2, 'General', 0, 2)");
            
            //config
            $db->query("CREATE TABLE `config` (
            `maintenance` int(11) NOT NULL DEFAULT '0',
            `floodlimit` int(11) NOT NULL DEFAULT '0',
            `lastset` int(11) NOT NULL,
            `postcount` int(11) NOT NULL,
            `reportforum` int(11) NOT NULL
            ) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci;");
            $db->query("INSERT INTO `config` VALUES(0, 15, 1340316455, 0, 2);");
            
            //dailyscreenshots
            $db->query("CREATE TABLE `dailyscreenshots` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `filename` varchar(54) NOT NULL,
            `caption` text NOT NULL,
            PRIMARY KEY (`id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;");
            
            //forums
            $db->query("CREATE TABLE `forums` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `parent` int(11) NOT NULL,
            `pos` int(11) NOT NULL,
            `title` varchar(30) COLLATE latin1_german2_ci NOT NULL,
            `description` varchar(100) COLLATE latin1_german2_ci NOT NULL,
            `icon` int(2) NOT NULL,
            `type` int(11) NOT NULL,
            `double_posting` int(11) NOT NULL,
            PRIMARY KEY (`id`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci AUTO_INCREMENT=3 ;");
            $db->query("INSERT INTO `forums` VALUES(1, 1, 1, 'Staff Center', 'All the staff-related or very delicate issues can be resolved here.', 0, 4, 1)");
            $db->query("INSERT INTO `forums` VALUES(2, 1, 2, 'Reports', 'All automated reports go here.', 0, 4, 0)");
            $db->query("INSERT INTO `forums` VALUES(3, 2, 1, 'Success', 'You have successfully installed RCS. Wewt!', 9, 1, 1)");
            
            //login_attempts
            $db->query("CREATE TABLE `login_attempts` (
            `failed` int(11) NOT NULL,
            `ip` varchar(15) COLLATE latin1_german2_ci NOT NULL,
            `time` int(11) NOT NULL
            ) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci;");
            
            //messages
            $db->query("CREATE TABLE `messages` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `creator` varchar(12) NOT NULL,
            `receiver` varchar(12) NOT NULL,
            `title` varchar(50) NOT NULL,
            `message` text NOT NULL,
            `ip` text NOT NULL,
            `date` datetime NOT NULL,
            `status` int(11) NOT NULL,
            `opened` int(11) NOT NULL,
            `lastreply` varchar(12) NOT NULL,
            `timestamp` int(11) NOT NULL,
            PRIMARY KEY (`id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;");
            
            //news
            $db->query("CREATE TABLE `news` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `title` varchar(35) NOT NULL,
            `category` int(1) NOT NULL,
            `content` text NOT NULL,
            `username` varchar(12) NOT NULL,
            `date` varchar(20) NOT NULL,
            `ip` varchar(20) NOT NULL,
            `icon` int(11) NOT NULL,
            PRIMARY KEY (`id`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;");
            
            //online_users
            $db->query("CREATE TABLE `online_users` (
            `cookie` varchar(35) COLLATE latin1_german2_ci NOT NULL,
            `time` int(11) NOT NULL
            ) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci;");
            
            //polls
            $db->query("CREATE TABLE `polls` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `poll_title` varchar(50) COLLATE latin1_german2_ci NOT NULL,
            `poll_question` text COLLATE latin1_german2_ci NOT NULL,
            `date` varchar(13) COLLATE latin1_german2_ci NOT NULL,
            `closed` int(1) NOT NULL,
            PRIMARY KEY (`id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci AUTO_INCREMENT=1 ;");
            
            //poll_options
            $db->query("CREATE TABLE `poll_options` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `belongs` int(11) NOT NULL,
            `option` varchar(100) COLLATE latin1_german2_ci NOT NULL,
            PRIMARY KEY (`id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci AUTO_INCREMENT=1 ;");
            
            //posts
            $db->query("CREATE TABLE `posts` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `username` varchar(12) COLLATE latin1_german2_ci NOT NULL,
            `content` text COLLATE latin1_german2_ci NOT NULL,
            `thread` int(11) NOT NULL,
            `date` datetime NOT NULL,
            `status` int(11) NOT NULL,
            `lastedit` varchar(55) COLLATE latin1_german2_ci NOT NULL,
            `ip` varchar(20) COLLATE latin1_german2_ci NOT NULL,
            `timestamp` int(11) NOT NULL,
            PRIMARY KEY (`id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci AUTO_INCREMENT=1 ;");
            
            //recoveries
            $db->query("CREATE TABLE `recoveries` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `userid` int(11) NOT NULL,
            `cancel` int(11) NOT NULL,
            `a1` varchar(35) NOT NULL,
            `a2` varchar(35) NOT NULL,
            `a3` varchar(35) NOT NULL,
            `a4` varchar(35) NOT NULL,
            `a5` varchar(35) NOT NULL,
            `a6` varchar(35) NOT NULL,
            PRIMARY KEY (`id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;");
            
            //replies
            $db->query("CREATE TABLE `replies` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `username` varchar(12) NOT NULL,
            `conversation` int(11) NOT NULL,
            `content` varchar(2000) NOT NULL,
            `ip` varchar(20) NOT NULL,
            `date` datetime NOT NULL,
            PRIMARY KEY (`id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;");
            
            //reports
            $db->query("CREATE TABLE `reports` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `reported` varchar(15) COLLATE latin1_german2_ci NOT NULL,
            PRIMARY KEY (`id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci AUTO_INCREMENT=1 ;");
            
            //stories
            $db->query("CREATE TABLE `stories` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `title` varchar(50) COLLATE latin1_german2_ci NOT NULL,
            `content` text COLLATE latin1_german2_ci NOT NULL,
            PRIMARY KEY (`id`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci AUTO_INCREMENT=2 ;");
            
            //threads
            $db->query("CREATE TABLE `threads` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `parent` int(11) NOT NULL,
            `title` varchar(50) COLLATE latin1_german2_ci NOT NULL,
            `content` text COLLATE latin1_german2_ci NOT NULL,
            `username` varchar(12) COLLATE latin1_german2_ci NOT NULL,
            `date` datetime NOT NULL,
            `qfc` varchar(14) COLLATE latin1_german2_ci NOT NULL,
            `lastpost` datetime NOT NULL,
            `lastposter` varchar(12) COLLATE latin1_german2_ci NOT NULL,
            `lastedit` varchar(55) COLLATE latin1_german2_ci NOT NULL,
            `lastbump` int(11) NOT NULL,
            `ip` varchar(15) COLLATE latin1_german2_ci NOT NULL,
            `sticky` int(11) NOT NULL,
            `lock` int(11) NOT NULL,
            `status` int(11) NOT NULL,
            `moved` tinytext COLLATE latin1_german2_ci NOT NULL,
            `hidden` int(11) NOT NULL,
            `timestamp` int(11) NOT NULL,
            `autohiding` int(11) NOT NULL,
            PRIMARY KEY (`id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci AUTO_INCREMENT=1 ;");
            
            //tracking
            $db->query("CREATE TABLE `tracking` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `account` varchar(12) NOT NULL,
            `ip` varchar(20) NOT NULL,
            `date` date NOT NULL,
            `time` int(11) NOT NULL,
            `tracking_id` varchar(12) NOT NULL,
            `status` int(11) NOT NULL,
            `a1` varchar(35) NOT NULL,
            `a2` varchar(35) NOT NULL,
            `a3` varchar(35) NOT NULL,
            `a4` varchar(35) NOT NULL,
            `a5` varchar(35) NOT NULL,
            `a6` varchar(35) NOT NULL,
            PRIMARY KEY (`id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;");
            
            //users
            $db->query("CREATE TABLE `users` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `username` varchar(12) NOT NULL,
            `password` tinytext NOT NULL,
            `age` varchar(8) NOT NULL,
            `country` varchar(20) NOT NULL,
            `email` varchar(35) NOT NULL,
            `reg_date` varchar(20) NOT NULL,
            `acc_status` int(11) NOT NULL,
            `ip` varchar(20) NOT NULL,
            `lastpost` int(11) NOT NULL,
            `lastlogin` int(11) NOT NULL,
            `cookie` varchar(35) NOT NULL,
            `forum_mute` int(11) NOT NULL,
            `mute_time` int(11) NOT NULL,
            `updated` int(11) NOT NULL,
            `invited` int(11) NOT NULL,
            `lastbump` int(11) NOT NULL,
            `messages` int(11) NOT NULL,
            PRIMARY KEY (`id`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;");
            $db->query("INSERT INTO `users` VALUES(1, 'Report', '43e55dc74d83bad897cb49b784c49e418304f8544b81ed015f77617dbe80c3edf5f6e702b25f721d82a962a10f6764897dd7b24b5e3cf9bd1e2a6b43e', '13-18', '225', 'fdsfdsf@gmail.com', 'Jun/22/2012', 4, '209.82.161.101', 1348344761, 1340362924, '". hash(sha512, time()) ."', 0, 0, 1, 0, 0, 0);");
            
            //votes
            $db->query("CREATE TABLE `votes` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `option_id` int(11) NOT NULL,
            `poll` int(11) NOT NULL,
            `username` varchar(12) COLLATE latin1_german2_ci NOT NULL,
            PRIMARY KEY (`id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci AUTO_INCREMENT=1 ;");
            
            $_SESSION['step1'] = true;
            $_SESSION['mysql_host'] = $_POST['host'];
            $_SESSION['mysql_user'] = $_POST['user'];
            $_SESSION['mysql_pass'] = $_POST['pass'];
            $_SESSION['mysql_name'] = $_POST['db'];
            header('location: step2.php');
        }else{
            $err = 1;
        }
    }
}

function verifyConnection($db_host, $db_user, $db_password, $db_name){
    try{
        $db = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_password);
    }
    catch(PDOException $e){
        $fail = true;
    }
    
    return ($fail) ? false : $db;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns:IE>

<head>
<meta http-equiv="Expires" content="0">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache">
<meta name="MSSmartTagsPreventParsing" content="TRUE">
<title>Install RCS</title>
<link href="../css/basic-3.css" rel="stylesheet" type="text/css" media="all">
<link href="../css/main/title-5.css" rel="stylesheet" type="text/css" media="all">
<link href="../css/kbase-2.css" rel="stylesheet" type="text/css" media="all" />
<link rel="shortcut icon" href="img/favicon.ico" />
</head>

		<div id="body">
		<div style="text-align: center; background: none;">
				<div class="titleframe e">
					<b>Install RuneScape Community Script</b>
				</div>
			</div>

			
			<img class="widescroll-top" src="../img/scroll/backdrop_765_top.gif" alt="" width="765" height="50" />
			<div class="widescroll">
			<div class="widescroll-bgimg">
			<div class="widescroll-content">
                            <div style="margin-left:auto;margin-right:auto;text-align:left;width:325px;">
                            <h2>MySQL Connection Info</h2>
                            <form action="index.php" method="POST">
                                <table>
                                   <?php
                                    if($err==1){
                                        ?>
                                            <tr style="color:red;">
                                                <td>Error</td>
                                                <td>Couldn't connect with specified settings.</td>
                                            </tr>
                                        <?php
                                    }
                                   ?>
                                   <tr>
                                       <td>MySQL Host</td>
                                       <td><input type="text" name="host"></td>
                                   </tr>
                                   <tr>
                                       <td>MySQL Username</td>
                                       <td><input type="text" name="user"></td>
                                   </tr>
                                   <tr>
                                       <td>MySQL Password</td>
                                       <td><input type="password" name="pass"></td>
                                   </tr>
                                   <tr>
                                       <td>Database Name</td>
                                       <td><input type="text" name="db"></td>
                                   </tr>
                                   <tr>
                                       <td>Continu</td>
                                       <td><input type="submit" value="Step 2 ->"></td>
                                   </tr>
                                </table>
                            </form>
                            </div>
			<div style="clear: both;"></div>
			</div>
			</div>
			</div>
			<img class="widescroll-bottom" src="../img/scroll/backdrop_765_bottom.gif" alt="" width="765" height="50" />
		<div class="tandc">This website and its contents are copyright &copy; 1999 - 2007 Jagex Ltd.<br/>
Use of this website is subject to our Terms+Conditions and Privacy policy<br/>Powered by RuneScape Community Script (RCS)</div>
	</div>
	</body>
</html>
