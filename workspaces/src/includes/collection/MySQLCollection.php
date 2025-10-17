<?php

/**
 *    _, __,  _, _ __, _  _, _, _
 *   / \ |_) (_  | | \ | /_\ |\ |
 *   \ / |_) , ) | |_/ | | | | \|
 *    ~  ~    ~  ~ ~   ~ ~ ~ ~  ~
 *
 * MySQL Collection class
 *
 * @package     ObsidianWorkspaces
 * @subpackage  Collection
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @filesource
 */

/**
 * MySQL Collection class
 *
 * This class represents a collection of documents, stored on MySQL.
 */
class MySQLCollection extends SQLCollection {
    ///
    /// Singleton pattern to get the MySQLDatabase instance
    ///

    /**
     * @var MySQLDatabase The mongo client to the database the collection is hosted
     */
    public static $client = null;


    /**
     * Gets the existing MySQLDatabase instance, or if not available, initializes one.
     *
     * @param Context $context
     * @return MySQLDatabase The MySQLDatabase instance
     */
    public static function getCurrentSiteDatabaseClient () {
        if (self::$client === null) {
            $client = Database::load();
            if ($candidateClient instanceof MySQLDatabase) {
                self::$client = $client;
            } else {
                throw new InvalidArgumentException("The MySQLDatabase driver is intended to be used when your main database product is MySQL. We recommend whether you pick the same engine for collections and other db use, whether you use a key/store storage solution for collections, like MongoDB.");
            }
        }
        return self::$client;
    }

    ///
    /// Constructor
    ///

    /**
     * Initializes a new instance of MongoCollection
     *
     * @param string $id the collection identifiant
     */
    public function __construct ($id, MySQLDatabase $client = null, $table = '') {
        global $Config;

        if ($client === null) {
            self::getCurrentSiteDatabaseClient();
        } else {
            self::$client = $client;
        }

        if ($table == '') {
            if (!array_key_exists('DocumentStorage', $Config) || !array_key_exists('Table', $Config['DocumentStorage'])) {
                throw new Exception("Configuration parameter missing: \$Config['DocumentStorage']['Table']. Expected value for this parameter is the table to store the collections documents.");
            }
            $this->table = $Config['DocumentStorage']['Table'];
        } else {
            $this->table = $table;
        }

        $this->id = $id;
        $this->initializeCollectionsTable();
    }

    ///
    /// Helper to create schema if required
    ///

    /**
     * Initialiazes collections table
     */
    protected function initializeCollectionsTable () {
        if (defined('COLLECTIONS_MYSQL_DATABASE_READY') && COLLECTIONS_MYSQL_DATABASE_READY) {
            return;
        }

        self::$client->query("
            CREATE TABLE if not exists $this->table (
                collection_id VARCHAR(255),
                document_id VARCHAR(255),
                document_value BLOB,
                PRIMARY KEY (collection_id, document_id)
            );"
        );

        define('COLLECTIONS_MYSQL_DATABASE_READY', true);
    }

    ///
    /// SqlCollection implementation
    ///

    /**
     * Executes a SQL query
     *
     * @param string $sql The SQL query
     * @return mixed If the query doesn't return any result, null. If the query return a row with one field, the scalar value. Otheriwse, an aossciative array, the fields as keys, the row as values.
     */
    public function query ($sql) {
        if ($sql == "") {
            return null;
        }

        $db = self::$client;
        if (!$result = $db->query($sql, MYSQL_ASSOC)) {
            throw new Exception("Can't execute collection query.");
        }

        if (!$row = $db->fetchRow($result)) {
            return null;
        }

        if (count($row) == 1) {
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
        return self::$client->escape($value);
    }

    /**
     * Gets all the documents from the collection
     *
     * @return Iterator An iterator to the documents, each item an instance of CollectionDocument
     */
    public function getAll () {
        $db = self::$client;

        $collectionId = $this->escape($this->id);
        $sql = "SELECT * FROM $this->table WHERE collection_id = '$collectionId'";
        if (!$result = $db->query($sql, MYSQL_ASSOC)) {
            throw new Exception("Can't get each collection documents.");
        }
        $type = $this->documentType;
        while ($row = $db->fetchRow($result)) {
            $data = json_decode($row['document_value']);
            yield $type::loadFromObject($data);
        }
    }
}
