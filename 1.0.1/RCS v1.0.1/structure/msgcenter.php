<?php

/*
 * @MSGCENTER
 * ~~~~~~~~~~~~
 * @FILE DESCRIPTION: Message center related proccesses
 * @LAST MODIFIED: June 26th, 2012
 */

class msgcenter
{
    protected $database;
    
    public function __construct(database $database)
    {
        //ACCESS TO DATABASE CLASS
        $this->database = $database;
        
        if($database->getDBStatus() == false) echo 'MESSAGE <b>There is no database connection</b>';
    }
    
    /*
     * @METHOD  isSolved
     * @DESC    checks if the message is solved (status == 1)
     */
    
    public function isSolved($id)
    {
        $status = $this->database->processQuery("SELECT `status` FROM `messages` WHERE `id`= ? ", array($id), true);
        return ($status[0]['status'] == 1) ? true : false;
    }
    
    /*
     * @METHOD  toggleSolve
     * @DESC    toggles the solved/status state
     */
    
    public function toggleSolve($id)
    {
        $status = $this->database->processQuery("SELECT `status` FROM `messages` WHERE `id`= ? ", array($id), true);
        $this->database->processQuery("UPDATE `messages` SET `status` = ? WHERE `id` = ?", array(($status[0]['status'] == 1) ? 0 : 1, $id), false);
    }
    
    /*
     * @METHOD  getReplies
     * @DESC    gets the number of replies to a conversation
     */
    
    public function getReplies($id)
    {
        $this->database->processQuery("SELECT * FROM `replies` WHERE `conversation` = ?", array($id), false);
        return $this->database->getRowCount();
    }
    
    /*
     * @METHOD  canReply
     * @DESC    checks if they can reply
     */
    
    public function canReply($id, $username, $rank)
    {
        $conv = $this->database->processQuery("SELECT `status`,`creator`,`receiver`,`lastreply` FROM `messages` WHERE `id` = ?", array($id), true);
        return ((($conv[0]['creator'] != $username && $conv[0]['receiver'] != $username) || $conv[0]['status'] == 1 || $conv[0]['lastreply'] == $username) && $rank < 4) ? false : true;
    }
    
    /*
     * @METHOD  canCreate
     * @DESC    checks if they can creat a conversation
     */
    
    public function canCreate($username, $rank)
    {
        $last_conversation = $this->database->processQuery("SELECT `timestamp` FROM `messages` WHERE `creator` = ? ORDER BY `timestamp` DESC LIMIT 1", array($username), true);
        return ((time()-$last_conversation[0]['timestamp']) > 3600 || $rank >= 4) ? true : false;
    }
	
	/*
	 * @METHOD	canView
	 * @DESC	checks if the user has permissions to view the specified message
	 */
    
    public function canView($id, $username, $rank)
    {
        $conversation = $this->database->processQuery("SELECT `creator`,`receiver` FROM `messages` WHERE `id` = ? LIMIT 1", array($id), true);
    
        if($this->database->getRowCount() == 0)
            return false;
        elseif(($conversation[0]['creator'] != $username && $conversation[0]['receiver'] != $username) && $rank < 4)
            return false;
        else
            return true;
    }
}

?>