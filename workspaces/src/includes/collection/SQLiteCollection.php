<?php

/**
 *    _, __,  _, _ __, _  _, _, _
 *   / \ |_) (_  | | \ | /_\ |\ |
 *   \ / |_) , ) | |_/ | | | | \|
 *    ~  ~    ~  ~ ~   ~ ~ ~ ~  ~
 *
 * SQLite Collection class
 *
 * @package     ObsidianWorkspaces
 * @subpackage  Collection
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @filesource
 */

/**
 * SQLite Collection class
 *
 * This class represents a collection of documents, stored on MySQL.
 */
class SQLiteCollection extends SQLCollection {

    ///
    /// Singleton pattern to get or initialize the SQLite instance
    ///

    /**
     * @var SQLite3 The SQLite client to the database the collection is hosted
     */
    public static $client = null;


    /**
     * Gets the existing SQLite3 instance, or if not available, initializes one.
     *
     * @return SQLite3 The SQLite3 instance
     */
    public static function getClient () {
        if (self::$client === null) {
            self::$client = self::initializeClient();
        }
        return self::$client;
    }

    /**
     * Initializes a new SQLite3 instance
     *
     * @return SQLite3 the client
     */
    public static function initializeClient () {
        global $Config;
        if (!array_key_exists('DocumentStorage', $Config) || !array_key_exists('File', $Config['DocumentStorage'])) {
            throw new Exception("Configuration parameter missing: \$Config['DocumentStorage']['File']. Expected value for this parameter is the path to the SQLite database file.");
        }

        return new SQLite3($Config['DocumentStorage']['File']);
    }

    ///
    /// Constructor
    ///

    /**
     * Initializes a new instance of MongoCollection
     *
     * @param string $id the collection identifiant
     */
    public function __construct ($id) {
        $this->table = 'collections';
        $this->id = $id;
        $this->initializeCollectionsTable();
    }

    ///
    /// Helper to create SQLite3 schema if required
    ///

    /**
     * Initializes collections table
     */
    protected function initializeCollectionsTable () {
        if (defined('COLLECTIONS_SQLITE_DATABASE_READY') && COLLECTIONS_SQLITE_DATABASE_READY) {
            return;
        }

        $client = static::getClient();
        $client->exec("
            CREATE TABLE if not exists $this->table (
                collection_id TEXT,
                document_id TEXT,
                document_value BLOB,
                PRIMARY KEY (collection_id, document_id)
            );"
        );

        define('COLLECTIONS_SQLITE_DATABASE_READY', true);
    }

    ///
    /// SqlCollection implementation
    ///

    /**
     * Determines if the SQL query is a statement
     *
     * @return boolean true is a SELECT one; otherwise, false.
     * @todo To use this outside the Collection scope, adds the other cases where a query is indicated.
     */
    public static function isStatement ($sql) {
        $instruction = strtoupper(strstr($sql, ' ', true));
        return $instruction != "SELECT" && $instruction != "PRAGMA";
    }

    /**
     * Executes a SQL query
     *
     * @param string $sql The SQL query
     * @return mixed If the query doesn't return any result, null. If the query return a row with one field, the scalar value. Otherwise, an associative array, the fields as keys, the row as values.
     */
    public function query ($sql) {
        $client = static::getClient();

        if (static::isStatement($sql)) {
            if (!$client->exec($sql)) {
                throw new Exception(
                    "Can't execute collection query. "
                    . $client->lastErrorMsg()
                );
            }
            return null;
        }

        $result = $client->query($sql);
        if ($result === true) {
            return null;
        }
        if ($result === false) {
            throw new Exception(
                "Can't execute collection query. "
                . $client->lastErrorMsg()
            );
        }

        $row = $result->fetchArray(SQLITE3_ASSOC);
        $scalar = ($result->numColumns() == 1);
        $result->finalize();

        if ($scalar) {
            return array_shift($row);
        } else {
            return $row;
        }
    }

    /**
     * Escapes the SQL string
     *
     * @param string $value The value to escape
     * @return string The escaped value
     */
    public function escape ($value) {
        return SQLite3::escapeString($value);
    }

    /**
     * Gets all the documents from the collection
     *
     * @return Iterator An iterator to the documents, each item an instance of CollectionDocument
     */
    public function getAll () {
        $collectionId = $this->escape($this->id);
        $sql = "SELECT document_value FROM $this->table WHERE collection_id = '$collectionId'";
        $client = static::getClient();
        $type = $this->documentType;

        $result = $client->query($sql);
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $data = json_decode($row['document_value']);
            yield $type::loadFromObject($data);
        }
    }
}
