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
            $this->lastError = "User unkwown: " . $this->id;
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
     * @return boolean true if the login is avaiable; otherwise, false.
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
        return $user;
    }

    /**
     * Gets user from specified e-mail
     *
     * @return User the user matching the specified e-mail ; null, if the mail were not found.
     */
    public static function get_user_from_email ($mail) {
        global $db;
        $sql = "SELECT username FROM " . TABLE_USERS . " WHERE user_email = '$mail'";
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
     * Gets the SQL permission clause to select resources where the user is the subject.
     *
     * @return string The SQL WHERE clause
     */
    public function get_permissions_clause () {
        return self::get_permissions_clause_from_user_id($this->id);
    }

    /**
     * Gets workspaces this user has accces to.
     *
     * @return Array A list of workspaces
     */
    public function get_workspaces () {
        return Workspace::get_user_workspaces($this->id);
    }

    /**
     * Gets the groups where an user has access to.
     *
     * @param int $user_id the user to get the groups list
     * @return array an array containing group_id, matching groups the specified user has access to.
     */
    public static function get_groups_from_user_id ($user_id) {
        global $db;
        $sql = "SELECT group_id FROM " . TABLE_UGROUPS_MEMBERS . " WHERE user_id = " . $user_id;
        if (!$result = $db->sql_query($sql)) {
            message_die(SQL_ERROR, "Can't get user", '', __LINE__, __FILE__, $sql);
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
