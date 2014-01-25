<?php

/**
 *    _, __,  _, _ __, _  _, _, _
 *   / \ |_) (_  | | \ | /_\ |\ |
 *   \ / |_) , ) | |_/ | | | | \|
 *    ~  ~    ~  ~ ~   ~ ~ ~ ~  ~
 *
 * UserGroup class
 *
 * @package     ObsidianWorkspaces
 * @subpackage  Model
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @filesource
 */

/**
 * UserGroup class
 *
 * This class maps the users_groups table.
 */
class UserGroup {

    public $id;
    public $code;
    public $title;
    public $description;

    /**
     * Initializes a new instance
     *
     * @param int $id the primary key
     */
    function __construct ($id = NULL) {
        if ($id) {
            $this->id = $id;
            $this->load_from_database();
        }
    }

    /**
     * Loads the object UserGroup (ie fill the properties) from the $_POST array
     */
    function load_from_form () {
        if (array_key_exists('code', $_POST)) $this->code = $_POST['code'];
        if (array_key_exists('title', $_POST)) $this->title = $_POST['title'];
        if (array_key_exists('description', $_POST)) $this->description = $_POST['description'];
    }

    /**
     * Loads the object UserGroup (ie fill the properties) from the database
     */
    function load_from_database () {
        global $db;
        $id = $db->sql_escape($this->id);
        $sql = "SELECT * FROM users_groups WHERE group_id = '" . $id . "'";
        if (!$result = $db->sql_query($sql)) message_die(SQL_ERROR, "Unable to query users_groups", '', __LINE__, __FILE__, $sql);
        if (!$row = $db->sql_fetchrow($result)) {
            $this->lastError = "UserGroup unkwown: " . $this->id;
            return false;
        }
        $this->code = $row['group_code'];
        $this->title = $row['group_title'];
        $this->description = $row['group_description'];
        return true;
    }

    /**
     * Saves to database
     */
    function save_to_database () {
        global $db;

        $id = $this->id ? "'" . $db->sql_escape($this->id) . "'" : 'NULL';
        $code = $db->sql_escape($this->code);
        $title = $db->sql_escape($this->title);
        $description = $db->sql_escape($this->description);

        //Updates or inserts
        $sql = "REPLACE INTO users_groups (`group_id`, `group_code`, `group_title`, `group_description`) VALUES ('$id', '$code', '$title', '$description')";
        if (!$db->sql_query($sql)) {
            message_die(SQL_ERROR, "Unable to save", '', __LINE__, __FILE__, $sql);
        }

        if (!$this->id) {
            //Gets new record id value
            $this->id = $db->sql_nextid();
        }
    }
}
