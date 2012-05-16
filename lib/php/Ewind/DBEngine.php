<?php
/**
 * Engine creating data arrays for controllers or templates
 *
 * @author e-wind
 * @version 0.81
 */

/**
 * Defined database connection constants
 */
require_once 'connectvars.php';

/**
 *
 * Class Engine
 *
 */
class Ewind_DBEngine {

    /**
     * Database obj.
     * @var $mysqli mysqli
     */
    private $mysqli;

    /**
     * @var string
     */
    private $curCategoryPath = 'main';

    /**
     * @var int
     */
    private $curCategoryID;

    /**
     * @var string
     */
    private $curEntryPath = '';

    /**
     * @var string
     */
    private $curTag = '';

    /**
     * constructor.
     * open DB connection.
     */
    function __construct() {
        $this->mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        if (mysqli_connect_errno()) {
            printf("Can not connect to database: %s\n", mysqli_connect_error());
            exit();
        }
        $this->mysqli->set_charset("cp1251");
    }

    /**
     * destructor.
     * closed DB connection.
     */
    function __destruct() {
        $this->mysqli->close();
        $this->mysqli = NULL;
        //echo 'database closed';
    }


    /**
     * Check if one row for given $query, and set $value for $type property
     * @param string $query
     * @return boolean
     */
    private function checkSetValue($query, $value, $type) {
        $r = $this->executeQuery($query);
            /* @var $r mysqli_result */
        if ($r->num_rows == 1) {
            switch ($type) {
                case 'category':
                    $this->curCategoryPath = $value;
                    $row = $r->fetch_row();
                    $this->curCategoryID = $row[0];
                    break;
                case 'entry':
                    $this->curEntryPath = $value;
                    break;
                case 'settag':
                    $this->curTag = $value;
                    break;
                case 'checktag';
                    $row = $r->fetch_row();
                    return $row[0];
                default:
                    break;
            }
            $r->close();
        }
        else { //value not exist
            $this->curEntryPath = '404';
            $this->curCategoryPath = 'main';
            $this->curCategoryID = 1;
            return FALSE;
        }
    }

    /**
     * check tag names from list(or one tag name if param string).
     * if tag not exist add tag name to DB
     * return list or one of tag IDs
     * @param mixed $tagName
     * @return mixed
     */
    private function checkSetTag($tagName) {
        if (is_array($tagName)) {
            $tagIdList = array();
            foreach($tagName as $tag) {
                $tagIdList[] = $this->checkSetTag($tag);
            }
            return $tagIdList;
        }
        $tagID = 0;
        $query = "SELECT `tag_id` FROM `tags` WHERE tag_name ='" . $tagName . "'";
        $tagID = $this->checkSetValue($query, $tagName, 'checktag');
        if ($tagID === FALSE){
            $query2 = "INSERT INTO `tags` (`tag_name`) VALUES ('" . $tagName . "')";
            $this->executeQuery($query2);
            $tagID = $this->mysqli->insert_id;
        }
        return $tagID;
    }

    /**
     * fetch multirow mysqli_result to list of assoc
     * @param string $query
     * @return array
     */
    private function queryToAssocList($query) {
        $assoc = array();
        $r = $this->executeQuery($query);
        /* @var $r mysqli_result */
        while ($row = $r->fetch_assoc()) {
            $assoc[] = $row;
        }
        $r->close();

        return $assoc;
    }

    /**
     * execute query
     * @param string $query
     */
    private function executeQuery($query) {
        $r = $this->mysqli->query($query);
            /* @var $r mysqli_result */
        if ($this->mysqli->errno) {
            throw new Exception('Database error #'.$this->mysqli->errno.': '.$this->mysqli->error);
        }
        else {
            return $r;
        }
    }

    /**
     * Get list of tags for entry
     * @param int $entry_id
     * @return array
     */
    private function getEntryTags($entry_id) {
        $tagList = array();

        $query = "SELECT `tag_name` FROM `entry_tags`".
                    "INNER JOIN `tags` USING (tag_id)".
                    "WHERE entry_id ='".$entry_id."' AND tag_id != 1";
        $tags = $this->queryToAssocList($query);
        //simplifier taglist array
        foreach ($tags as $tag) {
            $tagList[] = $tag['tag_name'];
        }
        return $tagList;
    }

    /**
     *
     * @param int $entry_id
     * @param array $tagList
     */
    private function setEntryTags($entry_id, $tagList) {
        $query = "DELETE FROM `entry_tags` WHERE entry_id = '".$entry_id."'";
        $this->executeQuery($query);
        if (isset($tagList[0])) {
            $tagIdList = $this->checkSetTag($tagList);
        }
        $tagIdList [] = 1; //add default tag
        $i = 0;
        foreach ($tagIdList as $tag_id) {
            $query2 = "INSERT INTO `entry_tags` (`entry_id`, `tag_id`, entry_tag_main)".
                        " VALUES ('".$entry_id."', '".$tag_id."', ";
            if ($i == 0) { //first tag - main, default - main if empty $tagList
                $query2 .= "1)";
            }
            else {
                $query2 .= "0)";
            }
            $this->executeQuery($query2);
            $i++;
        }

    }

    /**
     * Check and set paths, if wrong then set main page.
     * @param string $path
     * @param string $tag optional
     * @return array $page contain recognized or default pathes and tag.
     */
    public function setPaths($path, $tag = '') {
        $matches = array();
        $page = array();
        $query = '';
        if ($path == '/') $path = '/main'.PAGE_EXT;
        //check and parse url
        $pattern = '{
                        ^\/                     #start with "/"
                        ([\w_]+?)               #category path
                        \/??                    #can be directory separator
                        ((?<=\/)[\w_]+?)??      #can be entry path
                        '.PAGE_EXT.'$           #end with extension
                    }xi';
        if (preg_match($pattern, $path, $matches)) {
            //check and set parsed category path if exist and not hidden
            $query = "SELECT `cat_id` FROM `category` WHERE cat_path ='" . $matches[1] . "'" .
                     "AND cat_state = 1";
            $this->checkSetValue($query, $matches[1], 'category');
            //check and set parsed entry path if exist and not hidden
            if (isset($matches[2])) {
                $query = "SELECT `entry_id`" .
                     "FROM `entry`" .
                     "INNER JOIN `category`" .
                     "USING (cat_id)" .
                     "WHERE entry_path ='" . $matches[2] . "'" .
                     "AND cat_path ='" . $this->curCategoryPath . "'" .
                     "AND entry_type != 'h'";
                $this->checkSetValue($query, $matches[2], 'entry');
            }
            //check and set tag if exist
            elseif (preg_match('/^[0-9a-z_]+?$/i', $tag)) {
                $query = "SELECT `tag_id` FROM `tags` WHERE tag_name ='" . $tag . "'";
                $this->checkSetValue($query, $tag, 'settag');
            }
        }
        else { //path checking filed - not matched to pattern
            $this->curEntryPath = '404';
        }
        $page['category'] = $this->curCategoryPath;
        $page['entry'] = $this->curEntryPath;
        $page['tag'] = $this->curTag;
        return $page;
    }

    /**
     * return assoc list of tag IDs and names
     * @return array
     */
    public function getTags() {
        $tags = array();
        $query = "SELECT * FROM `tags` ORDER by tag_id";
        $tags = $this->queryToAssocList($query);
        return $tags;
    }

    /**
     * get array of tag IDs and delete all, set main tag to default if deleted
     * @param array $tagIDs
     * @throws Exception
     */
    public function deleteTags($tagIDs) {
        $tagID = 0;
        $defaultTag = 1;
        $stmt = $this->mysqli->stmt_init();
        foreach ($tagIDs as $tagID) {
            if ($tagID != $defaultTag) {
                $query = "DELETE FROM `tags` WHERE tag_id = ? ";
                $stmt->prepare($query);
                $stmt->bind_param('i', $tagID);
                $stmt->execute();
                if ($stmt->errno) {
                    throw new Exception('Database error #'.$stmt->errno.': '.$stmt->error);
                }
                $query = "DELETE FROM `entry_tags` WHERE tag_id = ? ";
                $stmt->prepare($query);
                $stmt->bind_param('i', $tagID);
                $stmt->execute();
                if ($stmt->errno) {
                    throw new Exception('Database error #'.$stmt->errno.': '.$stmt->error);
                }
                $query = "SELECT `entry_id` FROM `entry`";
                $entryIDs = $this->queryToAssocList($query);
                foreach ($entryIDs as $entry) {
                    $this->setEntryTags($entry['entry_id'], $this->getEntryTags($entry['entry_id']));
                }
            }
        }
    }

    /**
     * get assoc list of tag IDs and names and save all names
     * @param arrray $tags
     * @throws Exception
     */
    public function updateTags($tags) {
        $tagID = 0;
        $tagName = '';
        $stmt = $this->mysqli->stmt_init();
        $query = "UPDATE `tags` SET tag_name = ? WHERE tag_id = ? ";
        $stmt->prepare($query);
        $stmt->bind_param('si',$tagName, $tagID);
        foreach ($tags as $tag) {
            $tagName = $tag['tag_name'];
            $tagID = $tag['tag_id'];
            $stmt->execute();
            if ($stmt->errno) {
                throw new Exception('Database error #'.$stmt->errno.': '.$stmt->error);
            }
        }
    }

    /**
     * Return content of current page, depending on its category or entry path
     * if not entry path then it's category page
     * @return array
     */
    public function getContent($categ_ID = 0, $entry_ID = 0) {
        $content = array();

        if (!$this->curEntryPath && !$entry_ID) {
            $query = "SELECT * FROM `category` WHERE ".
            ($categ_ID ? "cat_id='".$categ_ID."'" : "cat_path='".$this->curCategoryPath."'");
        }
        else {
            $query =    "SELECT *, UNIX_TIMESTAMP(entry_time) AS entry_time " .
                        "FROM `entry` INNER JOIN `category` USING (cat_id)" .
                        "WHERE ".
                        ($categ_ID ? "cat_id='".$categ_ID."'" : "cat_path='".$this->curCategoryPath."'") .
                        "AND ".
                        ($entry_ID ? "entry_id='".$entry_ID."'" : "entry_path='".$this->curEntryPath."'");
        }
        $list = $this->queryToAssocList($query);
        if (isset($list[0])) {
            $content = $list[0];
            if ($this->curEntryPath || $entry_ID) {
                $content['tag_list'] = $this->getEntryTags($content['entry_id']);
            }
        }
        else {
            $content = NULL;
        }
        return $content;
    }

    /**
     * Return list of all categories title content.
     * if $is_admin then also return hidden categories
     * @param bool $is_admin
     * @return array
     */
    public function getCatList($is_admin = FALSE) {
        $catList = array();
        $query = "SELECT `cat_id`, `cat_path`, `cat_name`, `cat_title`, `cat_serial_num` FROM `category` ";
        if (!$is_admin) {
            $query .= " WHERE cat_state = 1 ";
        }
        $query .="ORDER by cat_serial_num";
        $catList = $this->queryToAssocList($query);
        return $catList;
    }

    /**
     * Return list of all entries title content.
     * if $is_admin then also return hidden end error type entries
     * @param bool $is_admin
     * @param int $cat_ID
     * @return array
     */
    public function getCatMap($is_admin = FALSE, $cat_ID = 0) {
        $catMap = array();
        if (!$cat_ID) $cat_ID = $this->curCategoryID;
        $query = "SELECT `entry_id`, `entry_path`, `entry_name`, `entry_title`,".
                " `tag_name`, UNIX_TIMESTAMP(entry_time) AS entry_time, `entry_state`, `entry_type` ".
                 "FROM `entry` ".
                 "INNER JOIN `entry_tags` USING (entry_id) ".
                 "INNER JOIN `tags` USING (tag_id)".
                 "WHERE cat_id ='" . $cat_ID . "'AND entry_tag_main = 1 ";
        if (!$is_admin) {
            $query .= "AND entry_state = 1 AND entry_type != 'e'";
        }
        $query .= "ORDER by tag_name, entry_name, entry_time";
        $catMap = $this->queryToAssocList($query);
        return $catMap;
    }

    /**
     * Return list of entries descriptions sorted by edited time and selected with tag
     * also return total number of pages
     * @param int $amount amount entries in page
     * @param int $pageNumber number of current page
     * @param string $cat_path
     * @return array
     */
    public function getLastEntries($amount, $pageNumber, $cat_path = '') {
        $lastEntryes = array();
        if (!$cat_path) $cat_path = $this->curCategoryPath;
        $startLimit = $amount * ($pageNumber - 1);
        $endLimit = ($amount * $pageNumber);
        $query = "SELECT cat_id, entry_id, `entry_path`, `entry_name`, `entry_title`, ".
                 "`entry_descr`, UNIX_TIMESTAMP(entry_time) AS entry_time ".
                 "FROM `entry` ".
                 "INNER JOIN `category` USING (cat_id) ";
        if ($this->curTag) {
            $query .= "INNER JOIN `entry_tags` USING (entry_id) ".
                      "INNER JOIN `tags` USING (tag_id) ";
        }
        $query .= "WHERE cat_path ='" . $cat_path . "' ".
                  "AND entry_state = 1 AND entry_type != 'e'";
        if ($this->curTag) {
            $query .= " AND tag_name ='" . $this->curTag . "'";
        }
        $query .= "ORDER by entry_time DESC ".
                "LIMIT ".$startLimit . ", " .$endLimit;
        $lastEntryes = $this->queryToAssocList($query);
        foreach ($lastEntryes as $key => $entry) {
            $lastEntryes[$key]['tag_list'] = $this->getEntryTags($entry['entry_id']);
        }
        //count number of entries ang calculating number of pages
        $query = "SELECT COUNT(*) AS count FROM `entry` WHERE cat_id ='". @$lastEntryes[0]['cat_id'] ."'";
        $numEntries = $this->queryToAssocList($query);
        $lastEntryes['num_of_pages'] = ceil($numEntries[0]['count'] / $amount);
        return $lastEntryes;
    }

    /**
     * Add $data in DB depending on it is category or entry, also udate tag list
     * @param array $data
     * @param int $categ_id
     * @return string
     */
    public function addPage(&$data, $categ_id = 0) {
        $error = '';
        $stmt = $this->mysqli->stmt_init();
        if (!$categ_id ) {
            $query = "INSERT INTO `category` (`cat_path`, `cat_name`, `cat_title`, `cat_descr`, ".
                     "`cat_state`, `cat_serial_num`) ".
                     "VALUES (?, ?, ?, ?, ?, ?)";
            $stmt->prepare($query);
            $stmt->bind_param('ssssii', $data['path'], $data['name'], $data['title'], $data['descr'],
                                        $data['state'], $data['serialnum']);
            $stmt->execute();
            if ($stmt->errno) {
                throw new Exception('Database error #'.$stmt->errno.': '.$stmt->error);
            }
        }
        else {
            $tagList = '';
            if ($data['tags']) {
                $tagList = explode(',', $data['tags']);
            }
            $time = $data['time'] ?  implode('-',  array_reverse(explode('.',$data['time']))) : NULL;
            $query = "INSERT INTO `entry` (`cat_id`, ".
                     "`entry_path`, `entry_time`, `entry_name`, ".
                     "`entry_title`, `entry_descr`, `entry_content`, `entry_type`, ".
                     "`entry_state`) ".
                     "VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt->prepare($query);
            $stmt->bind_param('isssssssi', $categ_id,
                    $data['path'], $time, $data['name'],
                    $data['title'], $data['descr'], $data['content'], $data['type'],
                    $data['state']);
            $stmt->execute();
            if ($stmt->errno) {
                throw new Exception('Database error #'.$stmt->errno.': '.$stmt->error);
            }
            $entry_id = $stmt->insert_id;
            $this->setEntryTags($entry_id, $tagList);

        }

        return $error;
    }

        /**
     * Update $data in DB depending on it is category or entry, also udate tag list
     * @param array $data
     * @param int $categ_id
     * @return string
     */
    public function updatePage(&$data, $categ_id, $entry_id = 0) {
        $error = '';
        $stmt = $this->mysqli->stmt_init();
        if ($categ_id && !$entry_id) {
            $query = "UPDATE `category` SET cat_path = ?, cat_name = ?, cat_title = ?, cat_descr = ?, ".
                     "cat_state = ?, cat_serial_num = ? ".
                     "WHERE cat_id = ?";
            $stmt->prepare($query);
            $stmt->bind_param('ssssiii', $data['path'], $data['name'], $data['title'], $data['descr'],
                                        $data['state'], $data['serialnum'], $categ_id);
            $stmt->execute();
            if ($stmt->errno) {
                throw new Exception('Database error #'.$stmt->errno.': '.$stmt->error);
            }
        }
        if ($categ_id && $entry_id) {
            $tagList = '';
            if ($data['tags']) {
                $tagList = explode(',', $data['tags']);
            }
            $time = $data['time'] ?  implode('-',  array_reverse(explode('.',$data['time']))) : NULL;
            $query = "UPDATE `entry`  SET cat_id = ?, ".
                     "entry_path = ?, entry_time = ?, entry_name = ?, ".
                     "entry_title = ?, entry_descr = ?, entry_content = ?, entry_type = ?, ".
                     "entry_state = ? ".
                     "WHERE entry_id = ?";
            $stmt->prepare($query);
            $stmt->bind_param('isssssssii', $data['cat_id'],
                    $data['path'], $time, $data['name'],
                    $data['title'], $data['descr'], $data['content'], $data['type'],
                    $data['state'], $entry_id);
            $stmt->execute();
            if ($stmt->errno) {
                throw new Exception('Database error #'.$stmt->errno.': '.$stmt->error);
            }
            $this->setEntryTags($entry_id, $tagList);
        }

        return $error;
    }

    /**
     *
     * @param type $categ_id
     * @param type $entry_id
     */
    public function deletePage($categ_id, $entry_id = 0) {
        if ($entry_id) {
            $query ="DELETE FROM `entry` WHERE entry_id = '".$entry_id."' LIMIT 1";
            $this->executeQuery($query);
            $query = "DELETE FROM `entry_tags` WHERE entry_id = '".$entry_id."'";
            $this->executeQuery($query);
        }
        else {
            $query ="DELETE FROM `category` WHERE cat_id = '".$categ_id."' LIMIT 1";
            $this->executeQuery($query);
            $query = "SELECT `entry_id` FROM `entry` WHERE cat_id = '".$categ_id."'";
            $entryIDs = $this->queryToAssocList($query);
            foreach ($entryIDs as $entry) {
                $query = "DELETE FROM `entry_tags` WHERE entry_id = '".$entry['entry_id']."'";
                $this->executeQuery($query);
            }
            $query = "DELETE FROM `entry` WHERE cat_id = '".$categ_id."'";
            $this->executeQuery($query);
        }
    }

}
?>
