<?php
 /*
 * @USER:LOGIN
 * ~~~~~~~~~~~~
 * @FILE DESCRIPTION: for accessing existing accounts/login page
 * @LAST MODIFIED: July 13th, 2012
 */

class user_login extends user 
{
    /*
     * @METHOD  resetAttempts
     * @DESC    delete all expired failed attempts
     */
    
    public function resetAttempts()
    {
        $this->database->processQuery("DELETE FROM `login_attempts` WHERE ". time() ." - `time` > 10", array(), false);
    }
    
    /*
     * @METHOD  canAttempt
     * @DESC    checks if the guest can attempt to login
     */
    
    public function canAttempt()
    {
        //let's make sure the user hasn't had more than 3 failed attempts in the past 15 minutes
        $validate = $this->database->processQuery("SELECT `failed` FROM `login_attempts` WHERE `ip` = ?", array($_SERVER['REMOTE_ADDR']), true);
        return ($validate[0]['failed'] >= 3) ? false : true;
    }
    
    /*
     * @METHOD  failed
     * @DESC    the user failed a login attempt
     */
    
    public function failed()
    {
        //check if they already have failed attempts
        $this->database->processQuery("SELECT `failed` FROM `login_attempts` WHERE `ip` = ?", array($_SERVER['REMOTE_ADDR']), true);
        
        if($this->database->getRowCount() == 0)
            $this->database->processQuery("INSERT INTO `login_attempts` VALUES (?,?,?)", array(1,$_SERVER['REMOTE_ADDR'],time()), false);
        else
            $this->database->processQuery("UPDATE `login_attempts` SET `failed` = `failed` + 1 WHERE `ip` = ?", array($_SERVER['REMOTE_ADDR']), false);
    }
}

?>
