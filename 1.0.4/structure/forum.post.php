<?php

class post extends forum
{
    /*
     * @METHOD  getParent
     * @DESC    gets the id of the thread's parent
     * @PARAM   $id : the id of the post we're using
     */
    
    public function getParent($id)
    {
        //get the post's parent (a thread)
        $parent = $this->database->processQuery("SELECT `thread` FROM `posts` WHERE `id` = ?", array($id), true);
        return $parent[0]['thread'];
    }
    
    /*
     * @METHOD  getActionBar
     * @DESC    the bar such as "Quote | Toggle Hide | Edit" that users/mods/admins see
     */
    
    public function getActionBar($rank, $id, $thread, $forum)
    {
        //check if the post is already reported
        $this->database->processQuery("SELECT * FROM `reports` WHERE `reported` = ?", array($id.':p'), true);
        $report = ($this->database->getRowCount() >= 1) ? '<span style="color:#C0C0C0">Report</span>' : '<a href="report.php?forum='. $forum .'&id='. $thread.'&pid='. $id .'&type=1">Report</a>';
        
        //set the base url
        $base_url = '?forum='. $forum .'&id='. $thread;
        
        //display their action bar
        switch($rank)
        {
            case 1:
            case 2:
                $bar = $report;
                break;
            case 3:
                $bar = '<a href="actions/hidepost.php'. $base_url .'&pid='. $id .'">Toggle Hide</a> | <a href="edit.php'. $base_url .'&type=1&pid='. $id .'">Edit</a>';
                break;
            case 4:
                $bar = '<a href="reply.php'. $base_url .'&quote='. $id .'&qt=1">Quote</a> | <a href="actions/hidepost.php'. $base_url .'&pid='. $id .'">Toggle Hide</a> | <a href="edit.php'. $base_url .'&type=1&pid='. $id .'">Edit</a> | <a href="actions/deletepost.php'. $base_url .'&pid='. $id .'">Delete</a>';
                break;
        }
        
        return $bar;
    }
    
    /*
     * @METHOD  hidePost
     * @DESC    hides the post
     */
    
    public function hidePost($id, $rank)
    {
        //extract current status of post
        $status = $this->database->processQuery("SELECT `status` FROM `posts` WHERE `id` = ? LIMIT 1", array($id), true);
        
        if($rank > 2) $this->database->processQuery("UPDATE `posts` SET `status` = ? WHERE `id` = ? LIMIT 1", array(($status[0]['status'] == 0) ? 1 : 0, $id), false);
    }
    
    /*
     * @METHOD  deletePost
     * @DESC    deletes a post
     */
    
    public function deletePost($id, $rank)
    {
		//delete the post if they are an admin
        if($rank > 3) $this->database->processQuery("DELETE FROM `posts` WHERE `id` = ? LIMIT 1", array($id), false);
    }  
}

?>
