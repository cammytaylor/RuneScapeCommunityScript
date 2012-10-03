<?php
/*
 * @FORUM
 * ~~~~~~~~~~~~
 * @FILE DESCRIPTION: Forum
 * @LAST MODIFIED: June 5, 2012
 */

class forum
{
    protected $database;
    
    function __construct($database)
    {
        //ACCESS TO DATABASE CLASS
        $this->database = $database;
        
        if($database == null || $database->getDBStatus() == false) echo '<b>There is no database connection</b>';
    }
    
    /*
     * @METHOD  threadCount
     * @DESC    returns the total thread count of the site
     */
    
    public function threadCount()
    {
        $this->database->processQuery("SELECT * FROM `threads`", array(), false);
        return $this->database->getRowCount();
    }
    
    /*
     * @METHOD  postCount
     * @DESC    returns the total post count of the site
     */
    
    public function postCount()
    {
        $this->database->processQuery("SELECT * FROM `posts`", array(), false);
        return $this->database->getRowCount();
    }
    
    /*
     * @METHOD  catExists
     * @DESC    checks if the specified category exits
     */
    
    public function catExists($id)
    {
        $this->database->processQuery("SELECT * FROM `cats` WHERE `id` = ?", array($id), false);
        return ($this->database->getRowCount() == 0) ? false : true;
    }
    
    /*
     * @METHOD  forumExists
     * @DESC    checks if the specified forum exits
     */
    
    public function forumExists($id)
    {
        $this->database->processQuery("SELECT * FROM `forums` WHERE `id` = ?", array($id), false);
        return ($this->database->getRowCount() == 0) ? false : true;
    }
    
    /*
     * @METHOD  getNavBar
     * @DESC    get the navigation bar for the user
     */
    
    public function getNavBar($username, $rank)
    {
        ?>
        <div class="frame e">
        <span style="float: right;">
        <?php
            if(isset($_COOKIE['user']))
            {
                    if($rank > 3)
                    {
                        echo '<a href="../index.php">Main Page</a> | <a href="../logout.php">Logout</a>';
                    }
                    else
                    {
                        echo '<a href="../index.php">Main Page</a> | <a href="../logout.php">Logout</a>';
                    }
            }
            else
            {
                    echo '<a href="../index.php">Main Page</a> | <a href="../login.php">Login</a>';
            }
            ?>
                </span>
                <div>
       <?php
            if(isset($_COOKIE['user'])) 
            { 
                    echo 'You are logged in as <span style="color: rgb(255, 187, 34);">'. $username .'</span>'; 
            } 
            else 
            { 
                    echo 'You are not logged in.'; 
            } 
            ?>
                </div>
                </div>
        <?php
    }
    
    /*
     * @METHOD  filter
     * @DESC    applies a censor to the given text
     */
    
    public function filter($content)
    {
        $filter = explode("\n", trim(file_get_contents('../includes/censor.txt')));
        
        for($i = 0; $i < count($filter); $i++)
        {
            $string = '';
            
            //add a * for every letter in the blocked word
            for($x = 0; $x < strlen($filter[$i]); $x++)
            {
                $string .= '*';
            }
            
            $content = str_replace($filter[$i], $string, $content);
        }
        
        return $content;
    }
	
    /*
     * @METHOD  getOnlineUsers
     * @DESC    Gets the online users
     */
    
    public function getOnlineUsers()
    {
        $this->database->processQuery("SELECT * FROM `online_users`", array(), false);
        return number_format($this->database->getRowCount());
    }
    
    /*
     * @METHOD  canView
     * @DESC    checks if the user has permissions to see the specified forum
     */
    
    public function canView($id, $rank)
    {
        //extract forum details
        $forum = $this->database->processQuery("SELECT `type` FROM `forums` WHERE `id` = ? LIMIT 1", array($id), true);
        
        return ((($forum[0]['type'] == 4 && $rank < 3) || ($forum[0]['type'] == 5 && $rank < 4)) || $this->database->getRowCount() == 0) ? $cansee = false : $cansee = true;
    }
    
    /*
     * @METHOD  canCreate
     * @DESC    checks if the specified user can create threads in the specified forum
     */
    
    public function canCreate($id, $rank)
    {
        $forum = $this->database->processQuery("SELECT `type` FROM `forums` WHERE `id` = ? LIMIT 1", array($id), true);
        
        return ($forum[0]['type'] == 2 && $rank < 3) ? false : true;
    }
       
    /*
     * @METHOD  getPostTitle
     * @DESC    returns the title of the poster
     */
    
    
    public function getPostTitle($username, $rank = null)
    {
        if(is_null($rank))
        {
            $rank = $this->database->processQuery("SELECT `acc_status` FROM `users` WHERE `username` = ? LIMIT 1", array($username), true);
            $rank = $rank[0]['acc_status'];
            
        }
        
        switch($rank)
        {
            case 2:
                $title = 'Game Moderator';
                break;
            case 3:
                $title = 'Forum Moderator';
                break;
            case 4:
                $title = 'Administrator';
                break;
        }
        
        return $title;
    }
    
    /*
     * @METHOD  getIcon
     * @DESC    returns the icon for a forum
     */
    
    public function getIcon($icon_number)
    {
        switch($icon_number)
        {
                case 1:
		$img =  '<img src="../img/forum/icons/bug.gif">';
		break;
		
		case 2:
		$img =  '<img src="../img/forum/icons/clan.gif">';
		break;
		
		case 3:
		$img =  '<img src="../img/forum/icons/clan_recruitment.gif">';
		break;
		
		case 4:
		$img =  '<img src="../img/forum/icons/compliments.gif">';
		break;
		
		case 5:
		$img =  '<img src="../img/forum/icons/events.gif">';
		break;
		
		case 6:
		$img =  '<img src="../img/forum/icons/forum_feedback.gif">';
		break;
		
		case 7:
		$img =  '<img src="../img/forum/icons/forum_games.gif">';
		break;
		
		case 8:
		$img =  '<img src="../img/forum/icons/future_updates.gif">';
		break;
		
		case 9:
		$img =  '<img src="../img/forum/icons/general.gif">';
		break;
		
		case 10:
		$img =  '<img src="../img/forum/icons/goalsandachievements.gif">';
		break;
		
		case 11:
		$img =  '<img src="../img/forum/icons/Guides.gif">';
		break;
		
		case 12:
		$img =  '<img src="../img/forum/icons/item_discussion.gif">';
		break;
		
		case 13:
		$img =  '<img src="../img/forum/icons/monsters.gif">';
		break;
		
		case 14:
		$img =  '<img src="../img/forum/icons/news_announcements.gif">';
		break;
		
		case 15:
		$img =  '<img src="../img/forum/icons/off_topic.gif">';
		break;
		
		case 16:
		$img =  '<img src="../img/forum/icons/fighting.gif">';
		break;
		
		case 17:
		$img =  '<img src="../img/forum/icons/quest.gif">';
		break;
		
		case 18:
		$img = '<img src="../img/forum/icons/questions.gif">';
		break;
		
		case 19:
		$img = '<img src="../img/forum/icons/rants.gif">';
		break;
		
		case 20:
		$img = '<img src="../img/forum/icons/recent_updates.gif">';
		break;
		
		case 21:
		$img = '<img src="../img/forum/icons/skills.gif">';
		break;
		
		case 22:
		$img = '<img src="../img/forum/icons/stories.gif">';
		break;
		
		case 23:
		$img = '<img src="../img/forum/icons/suggestions.gif">';
		break;
		
		case 24:
		$img = '<img src="../img/forum/icons/tech_support.gif">';
		break;
		
		case 25:
		$img = '<img src="../img/forum/icons/web_feedback.gif">';
		break;
		
		case 26:
		$img = '<img src="../img/forum/icons/armour_2.gif">';
		break;
		
		case 27:
		$img = '<img src="../img/forum/icons/crafting_2.gif">';
		break;
		
		case 28:
		$img = '<img src="../img/forum/icons/fletching_2.gif">';
		break;
		
		case 29:
		$img = '<img src="../img/forum/icons/ores_bars_2.gif">';
		break;
		
		case 30:
		$img = '<img src="../img/forum/icons/runes_2.gif">';
		break;
		
		case 31:
		$img = '<img src="../img/forum/icons/weapons_forum_2.gif">';
		break;
        }
        
        return $img;
    }
}

?>
