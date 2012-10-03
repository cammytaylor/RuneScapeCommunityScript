<?php

/*
 * @POLL
 * ~~~~~~~~~~~~
 * @FILE DESCRIPTION: poll related processes
 * @LAST MODIFIED: June 26th, 2012
 */

class poll
{
    protected $database;
    
    public function __construct(database $database)
    {
        //ACCESS TO DATABASE CLASS
        $this->database = $database;
        
        if($database->getDBStatus() == false) echo 'POLL <b>There is no database connection</b>';
    }
    
    /*
     * @METHOD  isClosed
     * @DESC    checks if the specifided poll is closed for voting
     */
    
    public function isClosed($id)
    {
        $status = $this->database->processQuery("SELECT `closed` FROM `polls` WHERE `id` = ? LIMIT 1", array($id), true);
        return ($status[0]['closed'] == 1) ? true : false;
    }
    
    /*
     * @METHOD  toggleStatus
     * @DESC    toggles the status
     */
    
    public function toggleStatus($id, $rank)
    {
        if($rank > 3)
        {
            //get the current status then set the opposite
            $status = $this->database->processQuery("SELECT `closed` FROM `polls` WHERE `id` = ? LIMIT 1", array($id), true);
            $this->database->processQuery("UPDATE `polls` SET `closed` = ? WHERE `id` = ? LIMIT 1", array(($status[0]['closed'] == 1) ? 0 : 1, $id), false);
        }
    }
    
    /*
     * @METHOD  pollExists
     * @DESC    checks if the poll exists
     */
    
    public function pollExists($id)
    {
        $this->database->processQuery("SELECT * FROM `polls` WHERE `id` = ? LIMIT 1", array($id), false);
        return ($this->database->getRowCount() == 0) ? false : true;
    }
    
    /*
     * @METHOD  canVote
     * @DESC    checks if the specifided user can vote in a poll
     */
    
    public function canVote($id, $username)
    {
        if($this->isClosed($id) || !isset($_COOKIE['user']))
        {
            return false;
        }
        else
        {
            //extract if they've already voted in the poll
            $this->database->processQuery("SELECT * FROM `votes` WHERE `poll` = ? AND `username` = ? LIMIT 1", array($id, $username), false);
            
            if($this->database->getRowCount() == 0)
                return true;
            else
                return false;
        }
    }
    
    /*
     * @METHOD  optionExists
     * @DESC    checks if the specifided option actually exists for a certain poll
     * @PARAM   $id     the id of the poll
     * @PARAM   $option the id of the option where checking the existence of
     */
    
    public function optionExists($id, $option)
    {
        $this->database->processQuery("SELECT * FROM `poll_options` WHERE `belongs` = ? AND `id` = ?", array($id, $option), false);
        return ($this->database->getRowCount() == 0) ? false : true;
    }
    
    /*
     * @METHOD  getNumOfVotes
     * @DESC    get the number of votes a poll has
     */
    
    public function getNumOfVotes($id)
    {
        $this->database->processQuery("SELECT * FROM `votes` WHERE `poll` = ?", array($id), false);
        return $this->database->getRowCount();
    }
}

?>