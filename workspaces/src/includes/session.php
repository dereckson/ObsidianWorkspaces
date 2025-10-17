<?php

/**
 *    _, __,  _, _ __, _  _, _, _
 *   / \ |_) (_  | | \ | /_\ |\ |
 *   \ / |_) , ) | |_/ | | | | \|
 *    ~  ~    ~  ~ ~   ~ ~ ~ ~  ~
 *
 * Session
 *
 * This class uses a singleton pattern, as we only need one single instance.
 * Cf. http://www.php.net/manual/en/language.oop5.patterns.php
 *
 * @package     ObsidianWorkspaces
 * @subpackage  Keruald
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @filesource
 *
 */

/**
 * Session class
 */
class Session {
    /**
     * @var string session ID
     */
    public $id;

    /**
     * @var string remote client IP
     */
    public $ip;

    /*
     * @var Session current session instance
     */
    private static $instance;

    /*
     * Gets or initializes current session instance
     *
     * @return Session current session instance
     */
    public static function load () {
        if (!isset(self::$instance)) {
            //Creates new session instance
            $c = __CLASS__;
            self::$instance = new $c;
        }

        return self::$instance;
    }

    /**
     * Initializes a new instance of Session object
     */
    private function __construct () {
        //Starts PHP session, and gets id
        session_start();
        $_SESSION['ID'] = session_id();
        $this->id = $_SESSION['ID'];

        //Gets remote client IP
        $this->ip = self::get_ip();

        //Updates or creates the session in database
        $this->update();
    }

    /**
     * Gets remote client IP address
     * @return string IP
     */
    public static function get_ip () {
        //mod_proxy + mod_rewrite (old pluton url scheme) will define 127.0.0.1
        //in REMOTE_ADDR, and will store ip in HTTP_X_FORWARDED_FOR variable.
        //Some ISP/orgz proxies also use this setting.
        if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }

        //Standard cases
        return $_SERVER['REMOTE_ADDR'];
    }

    /**
     * Cleans up session
     * i.  deletes expired session
     * ii. sets offline relevant sessions
     */
    public static function clean_old_sessions () {
        global $db, $Config;

        //Gets session and online status lifetime (in seconds)
        //If not specified in config, sets default 5 and 120 minutes values
        $onlineDuration  = array_key_exists('OnlineDuration', $Config)  ? $Config['OnlineDuration']  :  300;
        $sessionDuration = array_key_exists('SessionDuration', $Config) ? $Config['SessionDuration'] : 7200;

        $resource = array_key_exists('ResourceID', $Config) ? '\'' . $db->sql_escape($Config['ResourceID']) . '\'' : 'default';

        //Deletes expired sessions
        $sql = "DELETE FROM " . TABLE_SESSIONS . " WHERE session_resource = $resource AND TIMESTAMPDIFF(SECOND, session_updated, NOW()) > $sessionDuration";
        if (!$db->sql_query($sql)) message_die(SQL_ERROR, "Can't delete expired sessions", '', __LINE__, __FILE__, $sql);

        //Online -> offline
        $sql = "UPDATE " . TABLE_SESSIONS . " SET session_resource = $resource AND session_online = 0 WHERE TIMESTAMPDIFF(SECOND, session_updated, NOW()) > $onlineDuration";
        if (!$db->sql_query($sql)) message_die(SQL_ERROR, 'Can\'t update sessions online statuses', '', __LINE__, __FILE__, $sql);
    }


    /**
     * Updates or creates a session in the database
     */
    public function update () {
        global $db, $Config;

        //Cleans up session
        //To boost SQL performances, try a random trigger
        //     e.g. if (rand(1, 100) < 3) self::clean_old_sessions();
        //or comment this line and execute a cron script you launch each minute.
        self::clean_old_sessions();

        //Saves session in database.
        //If the session already exists, it updates the field online and updated.
        $id = $db->sql_escape($this->id);
        $resource = array_key_exists('ResourceID', $Config) ? '\'' . $db->sql_escape($Config['ResourceID']) . '\'' : 'default';
        $user_id = $db->sql_escape(ANONYMOUS_USER);
        $sql = "INSERT INTO " . TABLE_SESSIONS . " (session_id, session_ip, session_resource, user_id) VALUES ('$id', '$this->ip', $resource, '$user_id') ON DUPLICATE KEY UPDATE session_online = 1";
        if (!$db->sql_query($sql)) message_die(SQL_ERROR, 'Can\'t save current session', '', __LINE__, __FILE__, $sql);
    }

    /**
     * Gets the number of online users
     *
     * @return int the online users count
     */
    public function count_online () {
        //Keeps result for later method call
        static $count = -1;

        if ($count == -1) {
            //Queries sessions table
            global $db, $Config;

            $resource = array_key_exists('ResourceID', $Config) ? '\'' . $db->sql_escape($Config['ResourceID']) . '\'' : 'default';
            $sql = "SELECT count(*) FROM " . TABLE_SESSIONS . " WHERE session_resource = $resource AND session_online = 1";
            $count = (int)$db->sql_query_express($sql, "Can't count online users");
        }

        //Returns number of users online
        return $count;
    }

    /**
     * Gets the value of a custom session table field
     *
     * @param string $info the field to get
     * @return string the session specified field's value
     */
    public function get_info ($info) {
        global $db;

        $id = $db->sql_escape($this->id);
        $sql = "SELECT `$info` FROM " . TABLE_SESSIONS . " WHERE session_id = '$id'";
        return $db->sql_query_express($sql, "Can't get session $info info");
    }

    /**
     * Sets the value of a custom session table field to the specified value
     *
     * @param string $info the field to update
     * @param string $value the value to set
     */
    public function set_info ($info, $value) {
        global $db;

        $value = ($value === null) ? 'NULL' : "'" . $db->sql_escape($value) . "'";
        $id = $db->sql_escape($this->id);
    	$sql = "UPDATE " . TABLE_SESSIONS . " SET `$info` = $value WHERE session_id = '$id'";
        if (!$db->sql_query($sql))
            message_die(SQL_ERROR, "Can't set session $info info", '', __LINE__, __FILE__, $sql);
    }

    /**
     * Gets logged user information
     *
     * @return User the logged user information
     */
    public function get_logged_user () {
        global $db;

        //Gets session information
        $id = $db->sql_escape($this->id);
        $sql = "SELECT * FROM " . TABLE_SESSIONS . " WHERE session_id = '$id'";
        if (!$result = $db->sql_query($sql))
            message_die(SQL_ERROR, "Can't query session information", '', __LINE__, __FILE__, $sql);
        $row = $db->sql_fetchrow($result);

        //Gets user instance
        require_once('includes/objects/user.php');
        $user = new User($row['user_id']);

        //Adds session property to this user instance
        $user->session = $row;

        //Returns user instance
        return $user;
    }

    /**
     * Cleans session
     *
     * This method is to be called when an event implies a session destroy
     */
    public function clean () {
        //Destroies $_SESSION array values, help ID
        foreach ($_SESSION as $key => $value) {
            if ($key != 'ID') unset($_SESSION[$key]);
        }
    }

    /**
     * Updates the session in an user login context
     *
     * @param string $user_id the user ID
     */
    public function user_login ($user_id) {
        global $db;

        //Sets specified user ID in sessions table
        $user_id = $db->sql_escape($user_id);
        $id  = $db->sql_escape($this->id);
        $sql = "UPDATE " . TABLE_SESSIONS . " SET user_id = '$user_id' WHERE session_id = '$id'";
        if (!$db->sql_query($sql))
            message_die(SQL_ERROR, "Can't set logged in status", '', __LINE__, __FILE__, $sql);
    }

    /**
     * Updates the session in an user logout context
     */
    public function user_logout () {
        global $db;

        //Sets anonymous user in sessions table
        $user_id = $db->sql_escape(ANONYMOUS_USER);
        $id  = $db->sql_escape($this->id);
        $sql = "UPDATE " . TABLE_SESSIONS . " SET user_id = '$user_id' WHERE session_id = '$id'";
        if (!$db->sql_query($sql))
            message_die(SQL_ERROR, "Can't set logged out status", '', __LINE__, __FILE__, $sql);

        //Cleans session
        $this->clean();
    }
}

//The user_id matching anonymous user (overridable in config file)
if (!defined('ANONYMOUS_USER')) {
    define('ANONYMOUS_USER', -1);
}
