<?php

/**
 *    _, __,  _, _ __, _  _, _, _
 *   / \ |_) (_  | | \ | /_\ |\ |
 *   \ / |_) , ) | |_/ | | | | \|
 *    ~  ~    ~  ~ ~   ~ ~ ~ ~  ~
 *
 * Database result base class
 *
 * @package     ObsidianWorkspaces
 * @subpackage  Keruald
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @filesource
 *
 */

/**
 * Represents a database result
 */
abstract class DatabaseResult implements IteratorAggregate {
    ///
    /// The methods to implement
    ///

    /**
     * Gets number of rows in result
     *
     * @return int The number of rows in the specified result
     */
    public abstract function numRows ();

    /**
     * Fetches a row of the result
     *
     * @param DatabaseResult $result The query result
     * @return array An associative array with the database result
     */
    public abstract function fetchRow ();
}
