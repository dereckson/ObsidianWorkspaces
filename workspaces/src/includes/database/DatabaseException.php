<?php

/**
 *    _, __,  _, _ __, _  _, _, _
 *   / \ |_) (_  | | \ | /_\ |\ |
 *   \ / |_) , ) | |_/ | | | | \|
 *    ~  ~    ~  ~ ~   ~ ~ ~ ~  ~
 *
 * Database exception class
 *
 * @package     ObsidianWorkspaces
 * @subpackage  Keruald
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @filesource
 *
 */

/**
 * DatabaseException class.
 *
 * Used to throw exception running queries.
 */
class DatabaseException extends RuntimeException {
    /**
     * @var string the SQL query
     */
    protected $query = null;

    /**
     * Initializes a new instance of the DatabaseException class
     *
     * @param string|null $query The query executed. Null if the exception occured outside a query.
     * @param string $message The message to throw.
     * @param int $code The code.
     * @param ?Exception $previous The previous exception used for the exception chaining.
     */
    public function __construct ($query = null, $message = '', $code = 0, ?Exception $previous = null) {
        $this->query = $query;
        parent::__construct($message, $code, $previous);
    }

    /**
     * Gets the the SQL query
     *
     * @return string|null The SQL query or null if the exception were thrown outside a query context.
     */
    public final function getQuery () {
        return $this->query;
    }
}
