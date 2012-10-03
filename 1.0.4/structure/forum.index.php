<?php
/*
 * @FORUM:INDEX
 * ~~~~~~~~~~~~
 * @FILE DESCRIPTION: the index of the forums
 * @LAST MODIFIED: June 7, 2012
 */

class forum_index extends forum
{
    public function retrieveCategories($rank)
    {
        return ($rank > 2) ? $this->database->processQuery("SELECT `title`,`id`,`type` FROM `cats` ORDER BY `pos`", array(), true) : $this->database->processQuery("SELECT `title`,`id`,`type` FROM `cats` WHERE `type` <> 1 ORDER BY `pos`", array(), true);
    }
    
    /*
     * @METHOD  retrieveSubForums
     * @DESC    retrieves all the forums of a specific category and returns them 
     * @DESC    as an array (pretty much same as retrieveCategories)
     */
    
    public function retrieveSubForums($parent)
    {
        return (isset($_COOKIE['user']))? $this->database->processQuery("SELECT `id`,`title`,`description`,`icon`,`type` FROM `forums` WHERE `parent` = ? ORDER BY `pos`", array($parent), true) : $this->database->processQuery("SELECT `id`,`title`,`description`,`icon`,`type` FROM `forums` WHERE `parent` = ? AND `type` <> 6 ORDER BY `pos`", array($parent), true);
    }
    
    /*
     * @METHOD  retrieveFStatistics
     * @DESC    retrieves the statistis for the specified forum
     * @DESC    which is currently post count and thread count
     */
    
    public function retrieveFStatistics($id)
    {
        $statistics = array();
        $statistics['thread_count'] = 0;
        $statistics['post_count'] = 0;
        
        //get all the threads under the specified forum
        $threads = $this->database->processQuery("SELECT `id` FROM `threads` WHERE `parent` = ? ORDER BY `id`", array($id), true);
        
        //update thread count
        $statistics['thread_count'] = $this->database->getRowCount();
        
        //get the forum's last post
        $last_post = $this->database->processQuery("SELECT `lastpost` FROM `threads` WHERE `parent` = ? ORDER BY `id` DESC LIMIT 1", array($id), true);
        
        $statistics['last_post'] = $last_post[0]['lastpost'];
        
        //get post count
        foreach($threads as $thread)
        {
            //get all the posts in this thread, and then add it to the forum's postcount
            $this->database->processQuery("SELECT * FROM `posts` WHERE `thread` = ?", array($thread['id']), true);
            
            $statistics['post_count'] += $this->database->getRowCount();
        }
        
        return $statistics;
    }
}

?>
