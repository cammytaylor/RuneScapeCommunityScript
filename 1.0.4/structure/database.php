<?php

/*
 * @DATABASE
 * ~~~~~~~~~~~~
 * @FILE DESCRIPTION: Handles all database related processes
 * @LAST MODIFIED: May 25, 2012
 */

class database
{
    private $dbc;
    private $row_count;
    private $insertId;
    
    function __construct($db_host, $db_name, $db_user, $db_password)
    {
        try
        {
            $this->dbc = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_password);
        }
        catch(PDOException $e)
        {
            echo '<b>An error occured while trying to create a database connection: </b>'. $e->getMessage();
        }
        
        //run the maintenance check
        $this->isMaintenance();
    }
    
    /*
     * @METHOD  setPDOAttribute
     * @DESC    Sets a PDO attribute for our dbc object
     */
    
    public function setAttribute($first_param, $second_param)
    {
        try
        {
            $this->dbc->setAttribute($first_param, $second_param);
        }
        catch(PDOException $e)
        {
            echo 'PDOException Error: '. $e->getMessage();
        }
    }
    
    /*
     * @METHOD  processInsertQuery
     * @DESC    prepares a query for use, then runs it
     */
    
    public function processQuery($query, array $binds, $fetch)
    {
        $query_handle = $this->dbc->prepare($query);
        if(!$query_handle->execute($binds))
        {
            $error = $query_handle->errorInfo();
            echo $error[2];
	}
        
        //update insertId var
        $this->insertId = $this->dbc->lastInsertId();
        
        //incase we ever want to get the number of rows affected
        //we set our row_count variable to the number of rows
        //affected
        $this->row_count = $query_handle->rowCount();
        
        if($fetch == true)
        {
            return $query_handle->fetchAll();
        }
    }
    
    /*
     * @METHOD  getRowCount
     * @PARAM   returns the effected rows of the last executed query
     */
    
    public function getRowCount()
    {
        return $this->row_count;
    }
    
    /*
     * @METHOD  getInsertId
     * @DESC    returns the ID of the last inserted row
     */
    
    public function getInsertId()
    {
        return $this->insertId;
    }
    
    /*
     * @METHOD  close_connection
     * @DESC    Closes DB access
     */
    
    public function close_connection()
    {
        if($this->dbc != null)
        {
            $this->dbc = null;
        }
    }
    
    /*
     * @METHOD  getDBStatus
     * @DESC    Get the status of the DB
     */
    
    public function getDBStatus()
    {
        return ($this->dbc == null) ? false : true;
    }
    
    /*
     * @METHOD  isMaintenance
     * @DESC    checks if the site is under maintenace, redirects non-admins to maintenace page is set
     */
    
    private function isMaintenance($return = false)
    {
        if(basename($_SERVER['PHP_SELF']) != 'login.php')
        {
           $maintenance = $this->processQuery("SELECT `maintenance` FROM `config` LIMIT 1", array(), true);
        
           if($return == false)
           {
                if($maintenance[0]['maintenance'] == 1)
                {
                    //extract their rank/status
                    if(!isset($_COOKIE['user']))
                    {
                        //die('test: '. $_SERVER['HTTP_HOST'].'/maintenance.php');
                        header('location: http://'. $_SERVER['HTTP_HOST'] .'/maintenance.php');
                    }
                    else
                    {
                        $user = $this->processQuery("SELECT `acc_status` FROM `users` WHERE `cookie` = ?", array($_COOKIE['user']), true);

                        if($this->row_count == 0 || $user[0]['acc_status'] < 4)
                        {
                            //die('test: '. $_SERVER['HTTP_HOST'].'/maintenance.php');
                            header('location: http://'. $_SERVER['HTTP_HOST'] .'/maintenance.php');
                        }
                    }
                } 
           }
           else
           {
               return ($maintenance[0]['maintenance'] == 1) ? true : false;
           }
        }
    }
}

?>