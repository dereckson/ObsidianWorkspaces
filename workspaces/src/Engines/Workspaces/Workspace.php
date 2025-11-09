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
 * @subpackage  Workspaces
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @filesource
 */

namespace Waystone\Workspaces\Engines\Workspaces;

use Waystone\Workspaces\Engines\Errors\ErrorHandling;
use Waystone\Workspaces\Engines\Framework\Context;
use Waystone\Workspaces\Engines\Users\User;

use Cache;
use Language;

use Exception;
use LogicException;

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
     * @var WorkspaceConfiguration The workspace configuration
     */
    public $configuration;

    /**
     * Initializes a new instance
     *
     * @param int $id the primary key
     */
    function __construct ($id = null) {
        if ($id) {
            $this->id = $id;
            $this->load_from_database();
        }
    }

    /**
     * Loads the object Workspace (ie fill the properties) from the $_POST array
     */
    function load_from_form () {
        if (array_key_exists('code', $_POST)) {
            $this->code = $_POST['code'];
        }
        if (array_key_exists('name', $_POST)) {
            $this->name = $_POST['name'];
        }
        if (array_key_exists('created', $_POST)) {
            $this->created = $_POST['created'];
        }
        if (array_key_exists('description', $_POST)) {
            $this->description = $_POST['description'];
        }
    }

    /**
     * Loads the object zone (ie fill the properties) from the $row array
     */
    function load_from_row ($row) {
        $this->id = $row['workspace_id'];
        $this->code = $row['workspace_code'];
        $this->name = $row['workspace_name'];
        $this->created = $row['workspace_created'];
        $this->description = $row['workspace_description'];
    }

    /**
     * Loads the specified workspace from code
     *
     * @param string $code The workspace code
     *
     * @return Workspace The specified workspace instance
     */
    public static function fromCode ($code) {
        global $db;
        $code = $db->escape($code);
        $sql = "SELECT * FROM " . TABLE_WORKSPACES . " WHERE workspace_code = '"
               . $code . "'";
        if (!$result = $db->query($sql)) {
            ErrorHandling::messageAndDie(SQL_ERROR,
                "Unable to query workspaces", '', __LINE__, __FILE__, $sql);
        }
        if (!$row = $db->fetchRow($result)) {
            throw new Exception("Workspace unknown: " . $code);
        }

        $workspace = new Workspace();
        $workspace->load_from_row($row);

        return $workspace;
    }

    /**
     * Loads the object Workspace (ie fill the properties) from the database
     */
    function load_from_database () {
        global $db;
        $id = $db->escape($this->id);
        $sql = "SELECT * FROM " . TABLE_WORKSPACES . " WHERE workspace_id = '"
               . $id . "'";
        if (!$result = $db->query($sql)) {
            ErrorHandling::messageAndDie(SQL_ERROR,
                "Unable to query workspaces", '', __LINE__, __FILE__, $sql);
        }
        if (!$row = $db->fetchRow($result)) {
            $this->lastError = "Workspace unknown: " . $this->id;

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

        $id = $this->id ? "'" . $db->escape($this->id) . "'" : 'NULL';
        $code = $db->escape($this->code);
        $name = $db->escape($this->name);
        $created = $db->escape($this->created);
        $description = $db->escape($this->description);

        //Updates or inserts
        $sql = "REPLACE INTO " . TABLE_WORKSPACES
               . " (`workspace_id`, `workspace_code`, `workspace_name`, `workspace_created`, `workspace_description`) VALUES ('$id', '$code', '$name', '$created', '$description')";
        if (!$db->query($sql)) {
            ErrorHandling::messageAndDie(SQL_ERROR, "Unable to save", '',
                __LINE__, __FILE__, $sql);
        }

        if (!$this->id) {
            //Gets new record id value
            $this->id = $db->nextId();
        }
    }

    /**
     * Determines if the specified user has access to the current workspace
     *
     * @param User $user The user to check
     *
     * @return boolean true if the user has access to the current workspace ;
     *                 otherwise, false.
     */
    public function userCanAccess (User $user) {
        if ($this->id === false || $this->id === null || $this->id === '') {
            throw new LogicException("The workspace must has a valid id before to call userCanAccess.");
        }
        foreach ($user->get_workspaces() as $workspace) {
            if ($workspace->id == $this->id) {
                return true;
            }
        }

        return false;
    }

    /**
     * Loads configuration
     *
     * @param Context $context The site context
     */
    public function loadConfiguration (Context $context) {
        global $Config;

        $dir = $Config['Content']['Workspaces'] . '/' . $this->code;

        // Load JSON configuration file
        $file = $dir . '/workspace.conf';
        if (file_exists($file)) {
            $this->configuration =
                WorkspaceConfiguration::loadFromFile($file, $context);
            return;
        }

        // Load YAML configuration file
        $file = $dir . '/workspace.yml';
        if (file_exists($file)) {
            $this->configuration =
                WorkspaceConfiguration::loadFromYamlFile($file, $context);
            return;
        }

        $exceptionMessage =
            sprintf(Language::get('NotConfiguredWorkspace'), $file);
        throw new Exception($exceptionMessage);
    }

    /**
     * Gets workspaces specified user has access to.
     *
     * @param int $user_id The user to get his workspaces
     *
     * @return Workspace[] A list of workspaces
     */
    public static function get_user_workspaces ($user_id) {
        global $db;

        //Gets the workspaces list from cache, as this complex request could take 100ms
        //and is called on every page.
        $cache = Cache::load();

        if (!$workspaces = unserialize($cache->get("workspaces-$user_id"))) {
            $clause = User::get_permissions_clause_from_user_id($user_id, $db);
            $sql = "SELECT DISTINCT w.*
                     FROM " . TABLE_PERMISSIONS . " p, " . TABLE_WORKSPACES . " w
                     WHERE p.target_resource_type = 'W' AND
                           p.target_resource_id = w.workspace_id AND
                           p.permission_name = 'accessLevel' AND
                           p.permission_flag > 0 AND
                           ($clause)";
            if (!$result = $db->query($sql)) {
                ErrorHandling::messageAndDie(SQL_ERROR,
                    "Can't get user workspaces", '', __LINE__, __FILE__, $sql);
            }

            $workspaces = [];

            while ($row = $db->fetchRow($result)) {
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
     *
     * @return boolean If the specified code matches an existing workspace,
     *                 true; otherwise, false.
     */
    public static function is_workspace ($code) {
        global $db;

        $code = $db->escape($code);
        $sql = "SELECT count(*) FROM " . TABLE_WORKSPACES
               . " WHERE workspace_code = '$code'";
        if (!$result = $db->query($sql)) {
            ErrorHandling::messageAndDie(SQL_ERROR,
                "Can't check workspace code", '', __LINE__, __FILE__, $sql);
        }
        $row = $db->fetchRow($result);

        return ($row[0] == 1);
    }
}
