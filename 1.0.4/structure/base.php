<?php

 /*
 * @BASE
 * ~~~~~~~~~~~~
 * @FILE DESCRIPTION: includes basic functions that some pages use
 * @LAST MODIFIED: June 5, 2012
 */

class base
{
    private $database;
    
    /*
     * @METHOD  construct
     * @DESC    runs important functions/methods upon initiating
     */
    
    function __construct(database $database = null)
    {
        if(!is_null($database)) $this->database = $database;
    }
    
    /*
     * @METHOD  userCount
     * @DESC    # of users registered
     */
    
    public function userCount()
    {
        if($this->database == null)
        {
            die('Can\'t use userCount() without access to database class');
        }
        else
        {
            $this->database->processQuery("SELECT * FROM `users`", array(), false);
            return $this->database->getRowCount();
        }
    }
    
    /*
     * @METHOD  loadConig
     * @DESC    loads the config settings from the database table "config"
     */
    
    public function loadConfig()
    {
        if($this->database == null){
            die('BASE : loadConfig - Cannot load config without DB connection');
        }else{
            $returned = $this->database->processQuery("SELECT * FROM config", array(), true);
            
            return array('postcount' => $returned[0]['postcount'], 'floodlimit' => $returned[0]['floodlimit'], 'maintenance' => $returned[0]['maintenance'], 'reportforum' => $returned[0]['reportforum'], 'bbcode_members' => $returned[0]['bbcode_members']);
        }
    }
    
    /*
     * @METHOD  redirect
     * @DESC    instead of writing the header function so many times,
     * @DESC    we'll just use this redirect function
     */
    
    public function redirect($url)
    {
        header('Location: '. $url);
        exit();
    }
    
    
    /*
     * @METHOD  getPageName
     * @DESC    returns the name of the page the viewer is on
     */
	
    public function getPageName()
    {
        $page = preg_replace('#\/(.+)\/#', '', $_SERVER['PHP_SELF']);
        $page = str_replace('/', null, $page);
        return $page;
    }
    
    /*
     * @METHOD  br2nl
     * @DESC    converts break tags to \n (new lines)
     */
    
    public function br2nl($string)
    {
        return str_replace('&lt;br /&gt;', '<br />', $string);
    }
    
    /*
     * @METHOD  remBr
	 * @DESC	get rid of <br /> (such as when you edit a post)
     */
    
    public function remBr($content)
    {
        return str_replace('<br />', null, $content);
    }
    
    /*
     * @METHOD  seconds_to_time
     * @DESC    converts an int (seconds) to a string, E.G:
     * @DESC    4 Days 3 Hours 26 Seconds
     */
    
    public function seconds_to_time($seconds)
    {
            if($seconds == 0) return 'Never.';
            
            //time units
            $units = array('day' => 86400, 'hour' => 3600, 'minute' => 60, 'second' => 1);

            foreach($units as $name => $key)
            {
                    if($k = intval($seconds / $key))
                    {
                            ($k > 1) ? $s .= $k.' '.$name.'s ' : $s .= $k.' '.$name.' ';

                            //update seconds
                            $seconds -= $k*$key;
                    }
            }

            return $s;
    }
	
	/*
		@METHOD	writeToFile
		@DESC	Does what it says - writes to a file
	*/
    
    public function appendToFile($file, array $string)
    {
            $file_handle = fopen($file, 'a');
            
            foreach($string as $string_to_write)
            {
                fwrite($file_handle, '['. date('M-d-Y h:m:s') .'] '.$string_to_write."\n");
            }

            fclose($file_handle);
    }
    
    /*
     * @METHOD  shorten
     * @DESC    shortens the string to the length, then returns it
     * @PARAM   $cutoff  1 = return without words being cut-off
     */
    
    public function shorten($string, $length, $cutoff = false)
    {
        if(!$cutoff)
        {
            return substr($string, 0, (int) $length);
        }
        else
        {
            $string = substr($string, 0, (int) $length);
            return $string = substr($string, 0, strrpos($string, ' '));
        }
            
    }
    
    public function randomString($length)
    {
        $array = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '0', '1', '2', '3', '4', '5', '6', '7',
                       '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '!', '&', '^', '#', '@', 'a', 'b', 'c', 'd');
        
        $selected = '';
        
        for($x = 0; $x < (int) $length; $x++)
        {
            $selected .= time();
            $selected .= $array[rand(0,56)];
        }
        
        return $this->shorten(hash(sha256, $selected), $length);
    }
    
    /*
     * @METHOD  addDecors
     * @DESC    for stories. changes [a] to a
     * @PARAM   base    the base url for images
     */
    
    public function addSpecials($content, $base)
    {
        //add decors
        $original = array('[a]', '[f]', '[i]', '[k]', '[l]', '[s]', '[t]', '[w]', '[y]');

        $decors = array('<img src="'.$base .'decor_a.gif" style="float:left;">', 
                            '<img src="'.$base .'decor_f.gif" style="float:left;">',
                            '<img src="'.$base .'decor_i.gif" style="float:left;">',
                            '<img src="'.$base .'decor_k.gif" style="float:left;">',
                            '<img src="'.$base .'decor_l.gif" style="float:left;">',
                            '<img src="'.$base .'decor_s.gif" style="float:left;">',
                            '<img src="'.$base .'decor_t.gif" style="float:left;">',
                            '<img src="'.$base .'decor_w.gif" style="float:left;">',
                            '<img src="'.$base .'decor_y.gif" style="float:left;">');
        
        $content = str_replace($original, $decors, $content);
        
        return $content;
    }
}
?>