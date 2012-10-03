<?php
 /*
 * @USER:REGISTER
 * ~~~~~~~~~~~~
 * @FILE DESCRIPTION: for registraiton of new users
 * @LAST MODIFIED: June 24, 2012
 */

class user_register extends user 
{
    /*
     * @METHOD  generateCookie
     * @DESC    Generates a unique cookie upon registration
     */

    public function generateCookie()
    {
        //generate unique hash 35 characters long
        $cookie = substr(hash(sha256, time()), 35);

        //make sure the cookie doesn't already exist - just incase
        $this->database->processQuery("SELECT `cookie` FROM `users` WHERE `cookie` = ? LIMIT 1", array($cookie), false);

        if($this->database->getRowCount() > 0)
        {
            generateCookie();
        }
        else
        {
            return $cookie;
        }
    }
    
    public function clear()
    {
        session_start();
        session_unset();
        session_destroy();
    }
    
    /*
     * @METHOD  validateUsername
     * @DESC    Checks to make sure that the requested username
     * @DESC    is not a duplicate and is fit for creation
     */
    
    public function validateUsername($username)
    {
        $this->database->processQuery("SELECT * FROM `users` WHERE `username` = ? LIMIT 1", array($username), false);
        
        if($this->database->getRowCount() > 0)
        {
            return false;
        }
        elseif(strlen($username) > 12 || strlen($username) == '')
        {
            return false;
        }
        else
        {
            $err = 0;
            
            //make sure word doesn't contain a illegal character
            if(preg_match('/[^a-zA-Z0-9_ ]/', $username)) $err = 1;
            
            //make sure their username doesn't start with these restricted words
            $restricted = array('mod', 'admin');
            
            foreach($restricted as $word)
            {
                if(substr_count(strtolower($username), $word, 0) && strlen($username) >= strlen($word)) $err = 1;
            }
            
            return ($err == 0) ? true : false;
        }
    }
    
}

?>
