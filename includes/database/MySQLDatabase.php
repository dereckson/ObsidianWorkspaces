<?php

/**
 *    _, __,  _, _ __, _  _, _, _
 *   / \ |_) (_  | | \ | /_\ |\ |
 *   \ / |_) , ) | |_/ | | | | \|
 *    ~  ~    ~  ~ ~   ~ ~ ~ ~  ~
 *
 * Database implementation for legacy MySQL extension
 *
 * @package     ObsidianWorkspaces
 * @subpackage  Keruald
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @filesource
 *
 */

class MySQLDatabase extends Database {
    /**
     * @var resource the connection identifier
     */
    private $id;

    /**
     * Initializes a new instance of the database abstraction class, for MySQL engine
     *
     * @param string $host The host of the MySQL server [optional, default: localhost]
     * @param string $username The username used to connect [optional, default: root]
     * @param string $password The password used to connect [optional, default: empty]
     * @param string $database The database to select [optional]
     */
    public function __construct($host = 'localhost', $username = 'root', $password = '', $database = '') {
        if (!$this->id = @mysql_connect($host, $username, $password)) {
            $this->onCantConnectToHost();
        }

        //Selects database
        if ($database !== '') {
            mysql_select_db($database, $this->id);
        }
    }

    /**
     * Loads a new instance of the MySQLDatabase object
     *
     * @param Context $context The application context
     * @return MySQLDatabase The MySQLDatabase instance
     */
    public static function load (Context $context) {
        if (!array_key_exists('sql', $context->config)) {
            throw new InvalidArgumentException("To load a MySQL database, you need to add in your configuration a parameter block like:

    'sql' => [
        'engine' => 'MySQL',
        'host' => 'localhost',
        'username' => 'obsidian',
        'password' => 'somePassword',
        'database' => 'obsidian'
    ]"
            );
        }

        $config = $context->config['sql'];
        return new MySQLDatabase(
            $context->config['sql']['host'],
            $context->config['sql']['username'],
            $context->config['sql']['password'],
            $context->config['sql']['database']
        );
    }

    /**
     * Executes a query
     *
     * @param string $query The query to execute
     * @param int $resultType The result type (MYSQL_ASSOC, MYSQL_NUM, MYSQL_BOTH)
     * @return DatabaseResult The query result
     */
    public function query ($query, $resultType = MYSQL_BOTH) {
        $result = mysql_query($query, $this->id);
        if ($result) {
            if (is_resource($result)) {
                return new MySQLDatabaseResult($result, $resultType);
            }
            return new EmptyDatabaseResult();
        }
        $this->onQueryError($query);
    }

    /**
     * Fetches a row of the result
     *
     * @param DatabaseResult $result The query result
     * @return array An associative array with the databae result
     */
    public function fetchRow (DatabaseResult $result) {
        return $result->fetchRow();
    }

    /**
     * Retrieves the id generated by the last statement
     *
     * @return int The last id generated
     */
    public function nextId () {
        return mysql_insert_id($this->id);
    }

    /**
     * Escapes the expression
     *
     * @param $expression The expression to escape
     * @return string The escaped expression
     */
    public function escape ($expression) {
        return mysql_real_escape_string($expression, $this->id);
    }

    ///
    /// Events
    ///

    /**
     * Called on connect failure
     */
    protected function onCantConnectToHost () {
        $ex = new RuntimeException("Can't connect to SQL server.");
        Events::callOrThrow($this->cantConnectToHostEvents, $this, $ex);
    }

    /**
     * Called on query error
     *
     * @param string $query The query executed when the error occured
     */
    protected function onQueryError ($query) {
        $ex = new DatabaseException(
            $query,
            mysql_error($this->id),
            mysql_errno($this->id)
        );
        Events::callOrThrow($this->queryErrorEvents, [$this, $query, $ex], $ex);
    }
}