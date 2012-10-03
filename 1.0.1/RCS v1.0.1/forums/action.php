<?php
require('../includes/config.php');
require('../structure/base.php');
require('../structure/database.php');
require('../structure/user.php');
require('../structure/forum.php');
require('../structure/forum.index.php');
require('../structure/forum.thread.php');

$base = new base;
$database = new database($db_host, $db_name, $db_user, $db_password);
$user = new user($database);
$forum_index = new forum_index($database);
$forum_thread = new thread($database);

$user->updateLastActive();

//get the user's rank
$username = $user->getUsername($_COOKIE['user'], 2);
$rank = $user->getRank($username);

if($rank > 2)
{
    //get selected threads and our action
    $threads = explode('-', $_GET['threads']);
    $action = $_GET['action'];

    //ACTION LIST
    //1 = HIDE
    //2 = LOCK
    //3 = MOVE
    //4 = AUTO-HIDE
    //5 = STICKY
    //6 = DELETE
    
    if($action == 3)
    {
        if(!isset($_GET['moveto']))
        {
            ?>
            <form action="action.php" method="GET">
                <select name="moveto">
                    <?php
                        $categories = $forum_index->retrieveCategories();

                        foreach($categories as $category)
                        {
                            $listing = $forum_index->retrieveSubForums($category['id']);

                            echo '<option disabled="disabled">'. $category['title'] .'</option>';

                            foreach($listing as $list_f)
                            {
                                echo '<option value="'. $list_f['id'] .'">'. $list_f['title'] .'</option>';
                            }
                        }
                    ?>
                </select>
                <input type="submit" value="Move">
                <input type="hidden" name="action" value="<?php echo $_GET['action']; ?>">
                <input type="hidden" name="threads" value="<?php echo $_GET['threads']; ?>">
            </form>

            <?php
        }
        else
        {
            foreach($threads as $thread)
            {
                moveThread($thread, $_GET['moveto'], $database);
                $base->appendToFile('logs.txt', array($username.' moved the thread '. $thread .' to '. $_GET['moveto']));
            }
            
            $base->redirect('viewforum.php?forum='. $_GET['moveto']);
        }
    }
    else
    {
        foreach($threads as $thread)
        {
            switch($action)
            {
                case 1:
                    $forum_thread->hideThread($thread, $rank); $base->appendToFile('logs.txt', array($username.' hid/un-hid the thread '. $thread));
                    break;
                case 2:
                    $forum_thread->lock($thread, $rank); $base->appendToFile('logs.txt', array($username.' locked the thread '. $thread));
                    break;
                case 4:
                    setAutoHide($thread, $database, $rank); $base->appendToFile('logs.txt', array($username.' toggled auto-hide hide on the thread '. $thread));
                    break;
                case 5:
                    setSticky($thread, $database, $rank); $base->appendToFile('logs.txt', array($username.' stickied the thread '. $thread));
                    break;
                case 6:
                    delete($thread, $database, $rank); $base->appendToFile('logs.txt', array($username.' deleted the thread '. $thread));
                    break;
            }
        }
        
        //we're done here
        $base->redirect('viewforum.php?forum='. $_GET['forum']);
    }
}
else
{
    $base->redirect('index.php');
}

function moveThread($id, $moveTo, database $database)
{
    //make sure it exists
    $thread = $database->processQuery("SELECT * FROM `threads` WHERE `id` = ? LIMIT 1", array($id), true);

    if($database->getRowCount() == 1)
    {
        //get the name of the forum we're switching to
        $forum = $database->processQuery("SELECT `title` FROM `forums` WHERE `id` = ?", array($moveTo), true);
        
        //array on a different line so it's not so messy
        $values = array($thread[0]['parent'], $thread[0]['title'], 'This thread has been moved to '. $forum[0]['title'] .'. <br/><br/> <a href="viewthread.php?forum='. $moveTo .'&id='. $id .'">View it here.</a>', $thread[0]['username'], $thread[0]['date'], $thread[0]['qfc'], $thread[0]['lastposter'], $thread[0]['lastbump'], $id, $thread[0]['timestamp'], $thread[0]['autohiding']);

        //thread exists, so let's continue and create a new thread where it was orgionally to indicate it has been moved
        $database->processQuery("INSERT INTO `threads` VALUES (null, ?, ?, ?, ?, ?, ?, NOW(), ?, '', ?, '', '0', '1', '0', ?, '0', ?, ?)", $values, false);

        //now let's make the orgional thread be moved to the selected destination
        $database->processQuery("UPDATE `threads` SET `parent` = ? WHERE `id` = ? LIMIT 1", array($moveTo, $id), false);
    }
}

function setAutoHide($id, database $database, $rank)
{
    //this is an administrator only feature
    if($rank > 3)
    {
        //make sure it exists
        $thread = $database->processQuery("SELECT `autohiding` FROM `threads` WHERE `id` = ? LIMIT 1", array($id), true);

        if($database->getRowCount() == 1)
        {
            //do the opposite of what it's at now
            ($thread[0]['autohiding'] == 1) ? $do = 0 : $do = 1;

            //take action
            $database->processQuery("UPDATE `threads` SET `autohiding` = ? WHERE `id` = ? LIMIT 1", array($do,$id), false);
        }
    }
}

function setSticky($id, database $database, $rank)
{
    //this is an administrator only feature
    if($rank > 3)
    {
        //make sure it exists
        $thread = $database->processQuery("SELECT `sticky` FROM `threads` WHERE `id` = ? LIMIT 1", array($id), true);

        if($database->getRowCount() == 1)
        {
            //do the opposite of what it's at now
            ($thread[0]['sticky'] == 1) ? $do = 0 : $do = 1;

            //take action
            $database->processQuery("UPDATE `threads` SET `sticky` = ? WHERE `id` = ? LIMIT 1", array($do,$id), false);
        }
    }
}

function delete($id, database $database, $rank)
{
    //this is an administrator only feature
    if($rank > 3)
    {
        //make sure it exists
        $database->processQuery("SELECT * FROM `threads` WHERE `id` = ? LIMIT 1", array($id), false);
        
        if($database->getRowCount() == 1)
        {
            //delete thread
            $database->processQuery("DELETE FROM `threads` WHERE `id` = ? LIMIT 1", array($id), false);

            //delete all posts the thread had
            $database->processQuery("DELETE FROM `posts` WHERE `thread` = ?", array($id), false);
        }
    }
}

?>
