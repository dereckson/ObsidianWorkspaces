<?php

/*
 *    _, __,  _, _ __, _  _, _, _
 *   / \ |_) (_  | | \ | /_\ |\ |
 *   \ / |_) , ) | |_/ | | | | \|
 *    ~  ~    ~  ~ ~   ~ ~ ~ ~  ~
 *
 * MySQL layer and helper class
 * 
 * @package     ObsidianWorkspaces
 * @subpackage  Keruald
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @filesource
 * 
 */

if (!defined('SQL_LAYER')) {
    define('SQL_LAYER', 'MySQL');

    /**
     * SQL layer and helper class: MySQL
     */
    class sql_db {
        /**
         * @var int the connection identifier
         */
        private $id;

        /**
         * Initializes a new instance of the database abstraction class, for MySQL engine
         */
        function __construct($host = 'localhost', $username = '', $password = '', $database = '') {
            //Connects to MySQL server
            $this->id = @mysql_connect($host, $username, $password) or $this->sql_die();
            
            //Selects database
            if ($database != '') {
                mysql_select_db($database, $this->id);
            }
        }
        
        /**
         * Outputs a can't connect to the SQL server message and exits.
         * It's called on connect failure
         */
        private function sql_die () {
            //You can custom here code when you can't connect to SQL server
            //e.g. in a demo or appliance context, include('start.html'); exit;
            die ("Can't connect to SQL server.");
        }
        
        /**
         * Sends a unique query to the database
         * 
         * @return mixed if the query is successful, a result identifier ; otherwise, false
         */
        function sql_query ($query) {
            return mysql_query($query, $this->id);
        }

        /*
         * Fetches a row of result into an associative array
         * @return array an associative array with columns names as keys and row values as values
         */
        function sql_fetchrow ($result) {
            return mysql_fetch_array($result);
        }
        
        /**
         * Gets last SQL error information
         * 
         * @return array an array with two keys, code and message, containing error information
         */
        function sql_error () {
            $error['code'] = mysql_errno($this->id);
            $error['message'] = mysql_error($this->id);
            return $error;
        }
        
        /**
         * Gets the number of rows affected or returned by a query
         * 
         * @return int the number of rows affected (delete/insert/update) or the number of rows in query result
         */
        function sql_numrows ($result) {
            return mysql_num_rows($result);
        }
        
        /**
         * Gets the primary key value of the last query (works only in INSERT context)
         * 
         * @return int  the primary key value
         */
        function sql_nextid () {
            return mysql_insert_id($this->id);
        }
        
        /**
         * Express query method, returns an immediate and unique result
         *
         * @param string $query the query to execute
         * @param string $error_message the error message
         * @param boolean $return_as_string return result as string, and not as an array
         * @return mixed the row or the scalar result
         */
        function sql_query_express ($query = '', $error_message = "Impossible d'exécuter cette requête.", $return_as_string = true) {
            if ($query === '' || $query === false || $query === null) {
                //No query, no value
                return '';
            } elseif (!$result = $this->sql_query($query)) {
                //An error have occured
                message_die(SQL_ERROR, $error_message, '', '', '', $query);
            } else {
                //Fetches row
                $row = $this->sql_fetchrow($result);
                
                //If $return_as_string is true, returns first query item (scalar mode) ; otherwise, returns row
                return $return_as_string ? $row[0] : $row;
            }
        }
        
        /**
         * Escapes a SQL expression
         * 
         * @param string expression The expression to escape
         * @return string The escaped expression
         */
        function sql_escape ($expression) {
            return mysql_real_escape_string($expression);
        }
        
        /**
         * Sets charset
         */
        function set_charset ($encoding) {
            mysql_set_charset($encoding, $this->id);
        }
    }
    
    //Creates an instance of this database class with configuration values
    $db = new sql_db(
        $Config['sql']['host'],
        $Config['sql']['username'],
        $Config['sql']['password'],
        $Config['sql']['database']
    );
    
    //To improve security, we unset sql parameters
    unset($Config['sql']);
    
    //Sets SQL connexion in UTF8. PHP 5.2.3+
    $db->set_charset('utf8');
}
