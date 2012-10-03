<?php
/*
 * @FORUM:THREAD
 * ~~~~~~~~~~~~
 * @FILE DESCRIPTION: For threads
 * @LAST MODIFIED: June 5, 2012
 */

class thread extends forum
{ 
    /*
     * @METHOD  checkExistence
     * @DESC    check if the thread exists
     */
    
    public function checkExistence($id)
    {
        $this->database->processQuery("SELECT * FROM `threads` WHERE `id` = ? LIMIT 1", array($id), false);
        return ($this->database->getRowCount() == 1) ? $x = true : $x = false;
    }
    
    
    /*
     * @METHOD  canView
     * @DESC    checks if the user has permissions to see thread
     */
    
    public function canView($id, $username, $powerLevel)
    {
        //extract thread details
        $thread = $this->database->processQuery("SELECT `parent`,`hidden` FROM `threads` WHERE `id` = ? LIMIT 1", array($id), true);
        
        $canSee = true;

        //get the parent's type
        $parent = $this->database->processQuery("SELECT `type` FROM `forums` WHERE `id` = ? LIMIT 1", array($thread[0]['parent']), true);

        if($parent[0]['type'] > 2)
        {
            //if it's a protected forum, make sure they are the thread owner or staff in order to view
            if($parent[0]['type'] == 3 && ($username != $thread[0]['username'] && $powerLevel < 3)) $canSee = false;
			
            //forum moderator forum, let only moderators view it
            if($parent[0]['type'] == 4 && $powerLevel < 3) $canSee = false;

            //administrator forum, let only administrators see it
            if($parent[0]['type'] == 5 && $powerLevel < 4) $canSee = false;
            
            //member forum, let only members see it
            if($parent[0]['type'] == 6 && !isset($_COOKIE['user'])) $canSee = false;
        }

        //only let staff view hidden threads
        if($thread[0]['hidden'] == 1 && $powerLevel < 3) $canSee = false;
            
        return $canSee;
    }
    
    /*
     * @METHOD  coverThread
     * @DESC    hides the thread (users can still visit, though)
     */
    
    public function coverThread($id, $rank)
    {
        //extract current status of post
        $status = $this->database->processQuery("SELECT `status` FROM `threads` WHERE `id` = ? LIMIT 1", array($id), true);
        
        if($rank > 2) $this->database->processQuery("UPDATE `threads` SET `status` = ? WHERE `id` = ? LIMIT 1", array(($status[0]['status'] == 0) ? 1 : 0, $id), false);
    }
    
    /*
     * @METHOD  Thread
     * @DESC    hides the thread
     */
    
    public function hideThread($id, $rank)
    {
        //extract current status of post
        $status = $this->database->processQuery("SELECT `hidden` FROM `threads` WHERE `id` = ? LIMIT 1", array($id), true);
        
        if($rank > 2) $this->database->processQuery("UPDATE `threads` SET `hidden` = ? WHERE `id` = ? LIMIT 1", array(($status[0]['hidden'] == 0) ? 1 : 0, $id), false);
    }
    
    /*
     * @METHOD  lock
     * @DESC    locks a thread
     */
    
    public function lock($id, $rank = 0)
    {
        //make sure it exists
        $thread = $this->database->processQuery("SELECT `lock` FROM `threads` WHERE `id` = ? LIMIT 1", array($id), true);

		//take action if they are staff
        if($this->database->getRowCount() == 1 && $rank > 2) $this->database->processQuery("UPDATE `threads` SET `lock` = ? WHERE `id` = ? LIMIT 1", array(($thread[0]['lock'] == 1) ? $do = 0 : $do = 1,$id), false);
    }
    
    /*
     * @METHOD  canReply
     * @DESC    checks if the user is allowed to reply
     */
    
    public function canReply($id, $powerLevel)
    {
        //extract thread details
        $thread = $this->database->processQuery("SELECT `lock`,`moved` FROM `threads` WHERE `id` = ? LIMIT 1", array($id), true);
        
        //return if they can reply or not
        return (($thread[0]['lock'] == 1 && $powerLevel < 3) || !empty($thread[0]['moved']) || !isset($_COOKIE['user'])) ? $canReply = false : $canReply = true;
    }
    
    /*
     * @METHOD  preTitle
     * @DESC    get the lock/sticky icon before the thread's title 
     * @PARAM   $rank       rank of the user viewing
     * @PARAM   $location   where the user is at (viewforum.php/viewthread.php)
     */
    
    public function preTitle($id, $rank, $location = 1)
    {
        $data = $this->database->processQuery("SELECT `lock`,`sticky` FROM `threads` WHERE `id` = ?", array($id), true);
        $extra = '';
        
        if($rank > 2 && $location == 0) $extra .= '<input type="checkbox" name="selection[]" value="'. $id .'">&nbsp;<img src="../img/forum/modify.png" id="thread-'. $id .'">&nbsp;';
        if($data[0]['sticky'] == 1) $extra .= '<img src="../img/forum/sticky.gif"> ';
        if($data[0]['lock'] == 1) $extra .= ' <img src="../img/forum/locked.gif">';
        
        return $extra;
    }
    
    /*
     *@METHOD   hasReplies
     *@DESC     returns number of replies
     */
    
    public function getReplies($id)
    {
        $this->database->processQuery("SELECT * FROM `posts` WHERE `thread` = ?", array($id), false);
        return $this->database->getRowCount();
    }
    
    /*
     * @METHOD  getPostType
     * @DESC    gets the type of the post to display (like hidden, fmod, etc)
     * @PARAM   $id : id of the thread
     * @PARAM   $status : status (hidden/public) of the thread
     * @PARAM   $rank : the rank of the user that posted it
     */
    
    public function getPostType($status, $creator, $hide = false, $highlight = false)
    {
        $rank = $this->database->processQuery("SELECT `acc_status` FROM `users` WHERE `username` = ? LIMIT 1", array($creator), true);
        $rank = $rank[0]['acc_status'];
        
        if($status == 1)
        {
            $type = 'message hid';
        }
        elseif($hide)
        {
            $type = 'message moved';
        }
        else
        {
            $included = ($highlight) ? 'msghighlight' : null; 
            
            switch($rank)
            {
                case 3:
                    $type = 'message mod '. $included;
                    break;
                case 4:
                    $type = 'message jmod '. $included;
                    break;
                default:
                    $type = 'message '. $included;
                    break;
            }
        }
        
        return $type;
    }
    
    /*
     * @METHOD  getPageNum
     * @DESC    gets the page of the specifided post
     * @PARAM   $post       the id of the post (NOT THREAD) we'll be going to
     * @PARAM   $thread     the id of the thread
     * @PARAM   $per_page   derp
     */
    
    public function getPageNum($post, $thread, $per_page = 10)
    {
        //make sure the selected post exists
        $this->database->processQuery("SELECT * FROM `posts` WHERE `thread` = ? AND `id` = ? LIMIT 1", array($thread, $post), false);
        
        if($this->database->getRowCount() == 1)
        {
            //get number of results
            $thread = $this->database->processQuery("SELECT * FROM `posts` WHERE `thread` = ? ORDER BY `id` ASC", array($thread), true);

            //place holders
            $i = 0; //the position of post
            $x = 0; //0 = not found : 1 = found

            while($x == 0)
            {
                if($thread[$i]['id'] == $post) $x = 1;
                $i++;
            }
            
            return ceil($i/$per_page);
        }
        else
        {
             return false;
        }
    }
    
    /*
     * @METHOD  formatPost
     * @DESC    converts text to smileys, adds forum quotes, filters/cleans post, etc
     */
    
    public function formatPost($content, $username, base $base, forum $forum = null)
    {
        //config
        $config = $base->loadConfig();
        
        //get the rank of the user
        $user = $this->database->processQuery("SELECT `acc_status` FROM `users` WHERE `username` = ? LIMIT 1", array($username), true);
        $rank = $user[0]['acc_status'];
        
        //users & fmods
        if($rank < 4)
        {
            //apply filter for USERS if the forum variable is set
            if(!is_null($forum) && $rank < 3) $forum->filter($content);
            
            //remove HTML
            $content = htmlentities($content);
        }
        
        //youtube bbcode is seperate from other bbcodes, because only the youtube bbcodes can be toggled on/off for members
        if($rank > 2 || $config['bbcode_members']) $content = preg_replace('#\[youtube\](.+?)\[\/youtube\]#', '<iframe width="560" height="315" src="http://www.youtube.com/embed/$1?rel=0" frameborder="0" allowfullscreen></iframe>', $content);
            
        
        //now let's do BBCode for mods and admins
        if($rank > 2)
        {
            //bcode and some smileys
            $bbcode = array('#\[b\](.+?)\[\/b\]#i', '#\[i\](.+?)\[\/i\]#i', '#\[url=(.+?)](.+?)\[\/url\]#i', '#\[u\](.+?)\[\/u\]#i', '#:lolbert:#i', '#\[img\](.+?)\[\/img\]#i');
            $replace = array('<b>$1</b>', '<i>$1</i>', '<a href="$1">$2</a>', '<u>$1</u>', '<img src="../img/forum/smileys/lolbert.png">', '<img src="$1" border="0">');
            $content = preg_replace($bbcode, $replace, $content);
            
            if($rank == 4)
            {
                    //convert QUOTE BBcode to actual HTML format
                    $content = stripslashes(preg_replace('/\[quote\=(.+?)](.+?)\[\/quote\]/s', '<div style="border:1px solid #957C07;margin: 14px 0 0">
                    <p style="background:#645305;margin:0;padding:2px;font-style:normal">
                            <strong>Original Content</strong> (Posted by: $1)
                    </p>
                    <div style="position:relative;float:right;overflow:hidden;height:33px;top:-28px;left:10px">
                            <span style="color:#957C07;font-family:Engravers MT,Felix Titling,Perpetua Titling MT,Times New Roman;font-style:normal;font-size:120px;line-height:81px">"</span>
                    </div>
                    <div style="font-style:italic;margin:8px 6px">$2
                    </div>
                    </div>', $content)); 
            }
        }
        
        //add smileys
        $text = array(':)', ';)', ':P', ':(', ':|', 'O_o', ':D', '^^', ':O', ':@');
		$smileys = array('<img src="../img/forum/smileys/smile.gif">', 
		'<img src="../img/forum/smileys/wink.gif">', 
		'<img src="../img/forum/smileys/tongue.gif">', 
		'<img src="../img/forum/smileys/sad.gif">', 
		'<img src="../img/forum/smileys/nosmile.gif">', 
		'<img src="../img/forum/smileys/o.O.gif">', 
		'<img src="../img/forum/smileys/bigsmile.gif">', 
		'<img src="../img/forum/smileys/^^.gif">',
		'<img src="../img/forum/smileys/shocked.gif">', 
		'<img src="../img/forum/smileys/angry.gif">');
			
		return $content = stripslashes(str_replace($text, $smileys, $content));
    }
    
    public function getActionBar($rank, $id, $forum)
    {
        //check if the post is already reported
        $this->database->processQuery("SELECT * FROM `reports` WHERE `reported` = ?", array($id.':t'), true);
        $report = ($this->database->getRowCount() >= 1) ? '<span style="color:#C0C0C0">Report</span>' : '<a href="report.php?forum='. $forum .'&id='. $id. '&type=2">Report</a>';
        
        //set the base url
        $base_url = '?forum='. $forum .'&id='. $id;
        
        //display their action bar
        switch($rank)
        {
            case 1:
            case 2:
                $bar = $report;
                break;
            case 3:
                $bar = '<a href="edit.php'. $base_url .'&type=2">Edit</a>';
                break;
            case 4:
                $bar = '<a href="reply.php'. $base_url .'&quote='. $id .'&qt=2">Quote</a> | <a href="edit.php'. $base_url .'&type=2">Edit</a>';
                break;
        }
        
        return $bar;
    }
    
    public function bumpThread($id, $username)
    {
        $thread = $this->database->processQuery("SELECT `username` FROM `threads` WHERE `id` = ? LIMIT 1", array($id), true);
        
        if($thread[0]['username'] == $username) $this->database->processQuery("UPDATE `threads` SET `lastbump` = ? WHERE `id` = ?", array(time(), $id), false);
    }
}

?>
