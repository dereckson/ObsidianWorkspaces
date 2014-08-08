<?php

/**
 *    _, __,  _, _ __, _  _, _, _
 *   / \ |_) (_  | | \ | /_\ |\ |
 *   \ / |_) , ) | |_/ | | | | \|
 *    ~  ~    ~  ~ ~   ~ ~ ~ ~  ~
 *
 * Database result for MySQL legacy extension class
 *
 * @package     ObsidianWorkspaces
 * @subpackage  Keruald
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @filesource
 *
 */

class MySQLDatabaseResult extends DatabaseResult {
    ///
    /// Private members
    ///

    /**
     * @var resource The resource to the MySQL result
     */
    private $result;

    /**
     * @var int The type of result to return
     */
    private $resultType;


    ///
    /// Constructor
    ///
    /**
     * Initializes a new instance of the MySQLDatabaseResult class
     *
     * @param resource $result the resource to the MySQL result
     * @param int $resultType The result type (MYSQL_ASSOC, MYSQL_NUM, MYSQL_BOTH)
     */
    public function MySQLDatabaseResult ($result, $resultType = MYSQL_BOTH) {
        $this->result = $result;
        $this->resultType = $resultType;
    }

    ///
    /// DatabaseResult implementation
    ///

    /**
     * Gets number of rows in result
     *
     * @return int The number of rows in the specified result
     */
    public function numRows () {
        return mysql_num_rows($this->result);
    }

    /**
     * Fetches a row of the result
     *
     * @param DatabaseResult $result The query result
     * @return array An associative array with the databae result
     */
    public function fetchRow () {
        return mysql_fetch_array($this->result, $this->resultType);
    }

    ///
    /// IteratorAggregate implementation
    ///

    /**
     * Gets an iterator
     *
     * @return Generator an iterator on the query result
     */
    public function getIterator () {
        while ($row = $this->fetchRow()) {
            yield $row;
        }
    }
}