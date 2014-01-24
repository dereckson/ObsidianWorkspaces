<?php

/**
 *    _, __,  _, _ __, _  _, _, _
 *   / \ |_) (_  | | \ | /_\ |\ |
 *   \ / |_) , ) | |_/ | | | | \|
 *    ~  ~    ~  ~ ~   ~ ~ ~ ~  ~
 *
 * Workspace class
 *
 * @package     ObsidianWorkspaces
 * @subpackage  Model
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @filesource
 */

/**
 * Workspace class
 *
 * This class maps the workspaces table.
 */
class Workspace {

    public $id;
    public $code;
    public $name;
    public $created;
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
     * Loads the object Workspace (ie fill the properties) from the $_POST array
     */
    function load_from_form () {
        if (array_key_exists('code', $_POST)) $this->code = $_POST['code'];
        if (array_key_exists('name', $_POST)) $this->name = $_POST['name'];
        if (array_key_exists('created', $_POST)) $this->created = $_POST['created'];
        if (array_key_exists('description', $_POST)) $this->description = $_POST['description'];
    }

    /**
     * Loads the object zone (ie fill the properties) from the $row array
     */
    function load_from_row ($row) {
        $this->code = $row['workspace_code'];
        $this->name = $row['workspace_name'];
        $this->created = $row['workspace_created'];
        $this->description = $row['workspace_description'];
    }

    /**
     * Loads the object Workspace (ie fill the properties) from the database
     */
    function load_from_database () {
        global $db;
        $id = $db->sql_escape($this->id);
        $sql = "SELECT * FROM " . TABLE_WORKSPACES . " WHERE workspace_id = '" . $id . "'";
        if (!$result = $db->sql_query($sql)) message_die(SQL_ERROR, "Unable to query workspaces", '', __LINE__, __FILE__, $sql);
        if (!$row = $db->sql_fetchrow($result)) {
            $this->lastError = "Workspace unkwown: " . $this->id;
            return false;
        }
        $this->load_from_row($row);
        return true;
    }

    /**
     * Saves to database
     */
    function save_to_database () {
        global $db;

        $id = $this->id ? "'" . $db->sql_escape($this->id) . "'" : 'NULL';
        $code = $db->sql_escape($this->code);
        $name = $db->sql_escape($this->name);
        $created = $db->sql_escape($this->created);
        $description = $db->sql_escape($this->description);

        //Updates or inserts
        $sql = "REPLACE INTO " . TABLE_WORKSPACES . " (`workspace_id`, `workspace_code`, `workspace_name`, `workspace_created`, `workspace_description`) VALUES ('$id', '$code', '$name', '$created', '$description')";
        if (!$db->sql_query($sql)) {
            message_die(SQL_ERROR, "Unable to save", '', __LINE__, __FILE__, $sql);
        }

        if (!$this->id) {
            //Gets new record id value
            $this->id = $db->sql_nextid();
        }
    }

    /**
     * Gets workspaces specified user has access to.
     * 
     * @param int $user_id The user to get his workspaces
     * @return Array A list of workspaces
     */
    public static function get_user_workspaces ($user_id) {
        global $db;

        //Gets the workspaces list from cache, as this complex request could take 100ms
        //and is called on every page.
        $cache = Cache::load();

        if (!$workspaces = unserialize($cache->get("workspaces-$user_id"))) {
            $clause = User::get_permissions_clause_from_user_id($user_id);
            $sql =  "SELECT DISTINCT w.*
                     FROM " . TABLE_PERMISSIONS . " p, " . TABLE_WORKSPACES . " w
                     WHERE p.target_resource_type = 'W' AND
                           p.target_resource_id = w.workspace_id AND
                           p.permission_name = 'accessLevel' AND
                           ($clause)";
            if (!$result = $db->sql_query($sql)) {
                message_die(SQL_ERROR, "Can't get user workspaces", '', __LINE__, __FILE__, $sql);
            }

            $workspaces = array();

            while ($row = $db->sql_fetchrow($result)) {
                $workspace = new Workspace();
                $workspace->id = $row['workspace_id'];
                $workspace->load_from_row($row);
                $workspaces[] = $workspace;
            }

            $cache->set("workspaces-$user_id", serialize($workspaces));
        }

        return $workspaces;        
    }

    /**
     * Determines if a string matches an existing workspace code.
     *
     * @param string $code The workspace code to check
     * @return boolean If the specified code matches an existing workspace, true; otherwise, false.
     */
    public static function is_workspace ($code) {
        global $db;

        $code = $db->sql_escape($code);
        $sql = "SELECT count(*) FROM " . TABLE_WORKSPACES . " WHERE workspace_code = '$code'";
        if (!$result = $db->sql_query($sql)) {
            message_die(SQL_ERROR, "Can't check workspace code", '', __LINE__, __FILE__, $sql);
        }
        $row = $db->sql_fetchrow($result);
        return ($row[0] == 1);
    }
}