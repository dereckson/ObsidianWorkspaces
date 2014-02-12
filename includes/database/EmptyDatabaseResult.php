<?php

/**
 *    _, __,  _, _ __, _  _, _, _
 *   / \ |_) (_  | | \ | /_\ |\ |
 *   \ / |_) , ) | |_/ | | | | \|
 *    ~  ~    ~  ~ ~   ~ ~ ~ ~  ~
 *
 * Empty database result class
 *
 * @package     ObsidianWorkspaces
 * @subpackage  Keruald
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @filesource
 *
 */

/**
 * Represents an empty database result
 */
class EmptyDatabaseResult extends DatabaseResult {
    ///
    /// The methods to implement
    ///

    /**
     * Gets number of rows in result
     *
     * @return int 0
     */
    public function numRows () { return 0; }

    /**
     * Fetches a row of the result
     *
     * @param DatabaseResult $result The query result
     * @return array An associative array with the databae result
     */
    public function fetchRow () { return null; }

    ///
    /// IteratorAggregate implementation
    ///

    /**
     * Gets an iterator
     *
     * @return Generator an iterator on the query result
     */
    public function getIterator () {
        return;
        yield; //This unreachable code indicates to the PHP parser this method is a generator
    }

}
