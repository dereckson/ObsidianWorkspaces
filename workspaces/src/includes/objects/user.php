<?php

/**
 *    _, __,  _, _ __, _  _, _, _
 *   / \ |_) (_  | | \ | /_\ |\ |
 *   \ / |_) , ) | |_/ | | | | \|
 *    ~  ~    ~  ~ ~   ~ ~ ~ ~  ~
 *
 * User class
 *
 * @package     ObsidianWorkspaces
 * @subpackage  Model
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @filesource
 *
 */

/**
 * User class
 */
class User {

    public $id;
    public $name;
    public $password;
    public $active = 0;
    public $email;
    public $regdate;

    /**
     * @var Array An array of users already loaded, the username as user id
     */
    public static $hashtableById = [];

    /**
     * @var array|null An array of the workspaces the user has access to, each element an instance of the Workspace object. As long as the field hasn't been initialized by get_workspaces, null.
     */
    private $workspaces = null;

    /*
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
     * Initializes a new User instance if needed or get already available one.
     *
     * @param iint $id the user ID
     * @return User the user instance
     */
    static function get ($id = NULL) {
        if ($id && array_key_exists($id, User::$hashtableById)) {
            return self::$hashtableById[$id];
        }

        $user = new self($id);
        return $user;
    }

    /**
     * Loads the object User (ie fill the properties) from the $_POST array
     */
    function load_from_form () {
        if (array_key_exists('name', $_POST)) $this->name = $_POST['name'];
        if (array_key_exists('password', $_POST)) $this->password = $_POST['password'];
        if (array_key_exists('active', $_POST)) $this->active = $_POST['active'];
        if (array_key_exists('actkey', $_POST)) $this->actkey = $_POST['actkey'];
        if (array_key_exists('email', $_POST)) $this->email = $_POST['email'];
        if (array_key_exists('regdate', $_POST)) $this->regdate = $_POST['regdate'];
    }

    /**
     * Loads the object User (ie fill the properties) from the database
     */
    function load_from_database () {
        global $db;
        $sql = "SELECT * FROM " . TABLE_USERS . " WHERE user_id = '" . $this->id . "'";
        if ( !($result = $db->sql_query($sql)) ) message_die(SQL_ERROR, "Unable to query users", '', __LINE__, __FILE__, $sql);
        if (!$row = $db->sql_fetchrow($result)) {
            $this->lastError = "User unknown: " . $this->id;
            return false;
        }

        $this->load_from_row($row);

        return true;
    }

    /**
     * Loads the object User (ie fill the properties) from the database row
     */
    function load_from_row ($row) {
        $this->id       = $row['user_id'];
        $this->name     = $row['username'];
        $this->password = $row['user_password'];
        $this->active   = $row['user_active'] ? true : false;
        $this->email    = $row['user_email'];
        $this->regdate  = $row['user_regdate'];

        //Puts object in hashtable, so it's accessible in future call of
        //this run through User::get($id).
        self::$hashtableById[$this->id] = $this;
    }

    /**
     * Saves to database
     */
    function save_to_database () {
        global $db;

        $id = $this->id ? "'" . $db->sql_escape($this->id) . "'" : 'NULL';
        $name = $db->sql_escape($this->name);
        $password = $db->sql_escape($this->password);
        $active = $this->active ? 1 : 0;
        $email = $db->sql_escape($this->email);
        $regdate = $this->regdate ? "'" . $db->sql_escape($this->regdate) . "'" : 'NULL';

        //Updates or inserts
        $sql = "REPLACE INTO " . TABLE_USERS . " (`user_id`, `username`, `user_password`, `user_active`, `user_email`, `user_regdate`) VALUES ($id, '$name', '$password', $active, '$email', $regdate)";
        if (!$db->sql_query($sql)) {
            message_die(SQL_ERROR, "Unable to save user", '', __LINE__, __FILE__, $sql);
        }

        if (!$this->id) {
            //Gets new record id value
            $this->id = $db->sql_nextid();
        }
    }

    /**
     * Updates the specified field in the database record
     */
    function save_field ($field) {
        global $db;
        if (!$this->id) {
            message_die(GENERAL_ERROR, "You're trying to update a record not yet saved in the database");
        }
        $id = $db->sql_escape($this->id);
        $value = $db->sql_escape($this->$field);
        $sql = "UPDATE " . TABLE_USERS . " SET `$field` = '$value' WHERE user_id = '$id'";
        if (!$db->sql_query($sql)) {
            message_die(SQL_ERROR, "Unable to save $field field", '', __LINE__, __FILE__, $sql);
        }
    }

    //
    // USER MANAGEMENT FUNCTIONS
    //

    /**
     * Generates a unique user id
     */
    function generate_id () {
        global $db;

        do {
            $this->id = mt_rand(2001, 9999);
            $sql = "SELECT COUNT(*) FROM " . TABLE_USERS . " WHERE user_id = $this->id";
            if (!$result = $db->sql_query($sql)) {
                message_die(SQL_ERROR, "Can't check if a user id is free", '', __LINE__, __FILE__, $sql);
            }
            $row = $db->sql_fetchrow($result);
        } while ($row[0]);
    }

    /**
     * Fills password field with encrypted version
     * of the specified clear password
     */
    public function set_password ($newpassword) {
        $this->password = md5($newpassword);
    }

    /**
     * Checks if a login is available
     *
     * @param string $login the login to check
     * @return boolean true if the login is available; otherwise, false.
     */
    public static function is_available_login ($login) {
        global $db;
        $sql = "SELECT COUNT(*) FROM " . TABLE_USERS . " WHERE username = '$login'";
        if (!$result = $db->sql_query($sql)) {
            message_die(SQL_ERROR, "Can't check if the specified login is available", '', __LINE__, __FILE__, $sql);
        }
        $row = $db->sql_fetchrow($result);
        return ($row[0] == 0);
    }

    /**
     * Initializes a new User instance ready to have its property filled
     *
     * @return User the new user instance
     */
    public static function create () {
        $user = new User();
        $user->generate_id();
        $user->active = true;
        $user->regdate = time();
        return $user;
    }

    /**
     * Gets user from specified e-mail
     *
     * @return User the user matching the specified e-mail; null, if the mail were not found.
     */
    public static function get_user_from_email ($mail) {
        global $db;
        $sql = "SELECT * FROM " . TABLE_USERS . " WHERE user_email = '$mail'";
        if (!$result = $db->sql_query($sql)) {
            message_die(SQL_ERROR, "Can't get user", '', __LINE__, __FILE__, $sql);
        }

        if ($row = $db->sql_fetchrow($result)) {
            //E-mail found.
            $user = new User();
            $user->load_from_row($row);
            return $user;
        }

        //E-mail not found.
        return null;
    }

    //
    // REMOTE IDENTITY PROVIDERS
    //

    /**
     * Gets user from remote identity provider identifiant
     *
     * @param $authType The authentication method type
     * @param $remoteUserId The remote user identifier
     * @return User the user matching the specified identity provider and identifiant; null if no user were found.
     */
    public static function getUserFromRemoteIdentity ($authType, $remoteUserId) {
        global $db;

        $authType = $db->sql_escape($authType);
        $remoteUserId = $db->sql_escape($remoteUserId);
        $sql = "SELECT user_id FROM " . TABLE_USERS_AUTH . "    WHERE "
             . "auth_type = '$authType' AND auth_identity = '$remoteUserId'";
        if (!$result = $db->sql_query($sql)) {
            message_die(SQL_ERROR, "Can't get user", '', __LINE__, __FILE__, $sql);
        }

        if ($row = $db->sql_fetchrow($result)) {
            return User::get($row['user_id']);
        }

        return null;
    }

    /**
     * Sets user's remote identity provider identifiant
     *
     * @param $authType The authentication method type
     * @param $remoteUserId The remote user identifier
     * */
    public function setRemoteIdentity ($authType, $remoteUserId, $properties = null) {
        global $db;
        $authType = $db->sql_escape($authType);
        $remoteUserId = $db->sql_escape($remoteUserId);
        $properties = ($properties === NULL) ? 'NULL' : "'" . $db->sql_escape($properties) . "'";
        $sql = "INSERT INTO " . TABLE_USERS_AUTH . " (auth_type, auth_identity, auth_properties, user_id) "
             . "VALUES ('$authType', '$remoteUserId', $properties, $this->id)";
        if (!$db->sql_query($sql)) {
             message_die(SQL_ERROR, "Can't set user remote identity provider information", '', __LINE__, __FILE__, $sql);
         }
    }

    //
    // INTERACTION WITH OTHER OBJECTS
    //

    /**
     * Gets the groups where the current user has access to.
     *
     * @return array an array containing group_id, matching groups the current user has access to.
     */
    public function get_groups () {
        return self::get_groups_from_user_id($this->id);
    }

    /**
     * Determines if the user is a member of the specified group
     *
     * @param UserGroup $group The group to check
     */
    public function isMemberOfGroup (UserGroup $group) {
        global $db;
        $sql = "SELECT count(*) FROM users_groups_members WHERE group_id = $group->id AND user_id = $this->id";
        if (!$result = $db->sql_query($sql)) {
            message_die(SQL_ERROR, "Can't determine if the user belongs to the group", '', __LINE__, __FILE__, $sql);
        }
        $row = $db->sql_fetchrow($result);

        return $row[0] == 1;
    }

    /**
     * Adds user to the specified group
     *
     * @param UserGroup $group The group where to add the user
     * @parap boolean $isAdmin if true, set the user admin; otherwise, set it regular user.
     */
    public function addToGroup (UserGroup $group, $isAdmin = false) {
        global $db;
        $isAdmin = $isAdmin ? 1 : 0;
        $sql = "REPLACE INTO users_groups_members VALUES ($group->id, $this->id, $isAdmin)";
        if (!$db->sql_query($sql)) {
            message_die(SQL_ERROR, "Can't add user to group", '', __LINE__, __FILE__, $sql);
        }
    }

    /**
     * Gets the SQL permission clause to select resources where the user is the subject.
     *
     * @return string The SQL WHERE clause
     */
    public function get_permissions_clause () {
        return self::get_permissions_clause_from_user_id($this->id);
    }

    /**
     * Gets workspaces this user has access to.
     *
     * @return Array A list of workspaces
     */
    public function get_workspaces () {
        if ($this->workspaces === null) {
            $this->workspaces = Workspace::get_user_workspaces($this->id);
        }
        return $this->workspaces;
    }

    /**
     * Sets user permission
     *
     * @param string $resourceType The target resource type
     * @param int $resourceId The target resource ID
     * @param string $permissionName The permission name
     * @param int $permissionFlag The permission flag (facultative; by default, 1)
     */
    public function setPermission ($resourceType, $resourceId, $permissionName, $permissionFlag = 1) {
        global $db;
        $resourceType = $db->sql_escape($resourceType);
        if (!is_numeric($resourceId)) {
            throw new Exception("Resource ID must be a positive or null integer, and not $resourceId.");
        }
        $permissionName = $db->sql_escape($permissionName);
        if (!is_numeric($permissionFlag)) {
            throw new Exception("Permission flag must be a positive or null integer, and not $permissionFlag.");
        }

        $sql = "REPLACE INTO permissions
                (subject_resource_type, subject_resource_id,
                 target_resource_type,  target_resource_id,
                 permission_name,       permission_flag)
                VALUES
                ('U',               $this->id,
                 '$resourceType',   $resourceId,
                 '$permissionName', $permissionFlag)";
        if (!$db->sql_query($sql)) {
            message_die(SQL_ERROR, "Can't set user permission", '', __LINE__, __FILE__, $sql);
        }
    }

    /**
     * Gets the groups where a user has access to.
     *
     * @param int $user_id the user to get the groups list
     * @return array an array containing group_id, matching groups the specified user has access to.
     */
    public static function get_groups_from_user_id ($user_id) {
        global $db;
        $sql = "SELECT group_id FROM " . TABLE_UGROUPS_MEMBERS . " WHERE user_id = " . $user_id;
        if (!$result = $db->sql_query($sql)) {
            message_die(SQL_ERROR, "Can't get user groups", '', __LINE__, __FILE__, $sql);
        }
        $gids = array();
        while ($row = $db->sql_fetchrow($result)) {
            $gids[] = $row['group_id'];
        }
        return $gids;
    }

    /**
     * Gets the SQL permission clause to select resources where the specified user is the subject.
     *
     * @param $user_id The user ID
     * @return string The SQL WHERE clause
     */
    public static function get_permissions_clause_from_user_id ($user_id) {
        $clause = "subject_resource_type = 'U' AND subject_resource_id = $user_id";
        if ($groups = self::get_groups_from_user_id ($user_id)) {
            $clause  = "($clause) OR (subject_resource_type = 'G' AND subject_resource_id = ";
            $clause .= join(") OR (subject_resource_type = 'G' AND subject_resource_id = ", $groups);
            $clause .= ')';
        }
        return $clause;
    }
}
