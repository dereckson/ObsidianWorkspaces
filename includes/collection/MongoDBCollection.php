<?php

/**
 *    _, __,  _, _ __, _  _, _, _
 *   / \ |_) (_  | | \ | /_\ |\ |
 *   \ / |_) , ) | |_/ | | | | \|
 *    ~  ~    ~  ~ ~   ~ ~ ~ ~  ~
 *
 * MongoDB Collection class
 *
 * @package     ObsidianWorkspaces
 * @subpackage  Collection
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @filesource
 */

define('MONGO_DEFAULT_HOST',     'localhost');
define('MONGO_DEFAULT_PORT',     27017);
define('MONGO_DEFAULT_DATABASE', 'obsidian');

/**
 * MongoDB Collection class
 *
 * This class represents a collection of documents, stored on MongoDB.
 */
class MongoDBCollection extends Collection {
    /**
     * @var MongoCollection the Mongo collection
     */
    public $mongoCollection;

    ///
    /// Singleton pattern to get or initialize the MongoClient instance
    ///

    /**
     * @var MongoClient The mongo client to the database the collection is hosted
     */
    public static $mongoClient = null;


    /**
     * Gets the existing MongoClient instance, or if not available, initializes one.
     *
     * @return MongoClient The MongoClient instance
     */
    public static function getMongoClient () {
        if (self::$mongoClient === null) {
            self::$mongoClient = self::initializeMongoClient();
        }
        return self::$mongoClient;
    }

    ///
    /// Mongo objects initialization and helper methods
    ///

    /**
     * Gets the MongoDB connection string
     *
     * @return string The connection string
     */
    private static function getConnectionString () {
        global $Config;

        //Protocol
        $connectionString  = 'mongodb://';

        //Host
        if (isset($Config['DocumentStorage']) && array_key_exists('Host', $Config['DocumentStorage'])) {
            $connectionString .= $Config['DocumentStorage']['Host'];
        } else {
            $connectionString .= MONGO_DEFAULT_HOST;
        }

        //Port
        $connectionString .= ':';
        if (isset($Config['DocumentStorage']) && array_key_exists('Port', $Config['DocumentStorage'])) {
            $connectionString .= $Config['DocumentStorage']['Port'];
        } else {
            $connectionString .= MONGO_DEFAULT_PORT;
        }

        return $connectionString;
    }

    /**
     * Initializes a new MongoClient instance
     *
     * @return MongoClient the client
     */
    public static function initializeMongoClient () {
        global $Config;

        $connectionString  = self::getConnectionString();
        if (isset($Config['DocumentStorage']) && array_key_exists('SSL', $Config['DocumentStorage']) && $Config['DocumentStorage']['SSL'] !== null) {
            $context = stream_context_create(
                [ 'ssl' => $Config['DocumentStorage']['SSL'] ]
            );

            $m = new MongoClient(
                $connectionString,
                [ 'ssl' => true ],
                [ 'context' => $context ]
            );
        } else {
            $m = new MongoClient($connectionString);
        }

        return $m;
    }

    /**
     * Gets the database to use for the current collection
     */
    public static function getDatabase () {
        global $Config;

        if (isset($Config['DocumentStorage']) && array_key_exists('Database', $Config['DocumentStorage'])) {
            return $Config['DocumentStorage']['Database'];
        }

        return MONGO_DEFAULT_DATABASE;
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
        $this->id = $id;
        $m = self::getMongoClient();
        $this->mongoCollection = $m->selectCollection(self::getDatabase(), $id);
    }

    ///
    /// Mongo BSON
    ///

    /**
     * Gets an associative array with document collection properties as key and values.
     *
     * @param CollectionDocument $document The document
     * @return array The array representation of the document
     */
    public function getArrayFromDocument (CollectionDocument $document) {
        foreach ($document as $key => $value) {
            if ($key == 'id') {
                if ($value !== '') {
                    $array['_id'] = $value;
                }
            } else {
                $array[$key] = $value;
            }
        }
        return $array;
    }

    ///
    /// CRUD features
    ///

    /**
     * Adds a document to the collection
     *
     * @param CollectionDocument $document The document to add
     * @return boolean true if the operation succeeded; otherwise, false.
     */
    public function add (CollectionDocument &$document) {
        $object = static::getArrayFromDocument($document);
        $this->mongoCollection->save($object);
        $document->id = $object['_id'];
    }

    /**
     * Deletes a document from the collection
     *
     * @param string $documentId The identifiant of the document to delete
     * @return boolean true if the operation succeeded; otherwise, false.
     */
    public function delete ($documentId) {
        $this->mongoCollection->remove(
            [ '_id' => $documentId ]
        );
    }

    /**
     * Determines if a document exists
     *
     * @param CollectionDocument $document The document to check
     * @return boolean true if the document exists; otherwise, false.
     */
    public function exists (CollectionDocument $document) {
        //According https://blog.serverdensity.com/checking-if-a-document-exists-mongodb-slow-findone-vs-find/
        //this is more efficient to use find than findOne() to determine existence.
        $cursor = $this->mongoCollection->find(
            [ '_id' => $document->id ]
        )->limit(1);
        return $cursor->hasNext();
    }

    /**
     * Updates a document in the collection
     *
     * @param CollectionDocument $document The document to update
     * @return boolean true if the operation succeeded; otherwise, false.
     */
    public function update (CollectionDocument &$document) {
        $object = static::getArrayFromDocument($document);;
        $this->mongoCollection->update(
            [ '_id' => $document->id ],
            $object
        );
    }

    /**
     * Gets a document from the collection
     *
     * @param string $documentId The identifiant of the document to get
     * @param CollectionDocument $document The document
     */
    public function get ($documentId) {
        $data = $this->mongoCollection->findOne(
            [ '_id' => $documentId ]
        );
        return $this->getDocumentFromArray($data);
    }

    /**
     * Gets a document of the relevant collection documents type from an array.
     */
    public function getDocumentFromArray($documentArray) {
        $type = $this->documentType;
        if (!class_exists($type)) {
            throw new Exception("Can't create an instance of $type. If the class exists, did you register a SPL autoloader or updated includes/autoload.php?");
        }
        return $type::loadFromObject($documentArray);
    }

    /**
     * Gets a count of the documents in the collection
     *
     * @return int The number of documents
     */
    public function count () {
        return $this->mongoCollection->count();
    }

    /**
     * Gets all the documents from the collection
     *
     * @return Iterator An iterator to the documents, each item an instance of CollectionDocument
     */
    public function getAll () {
        return new MongoDBCollectionIterator($this);
    }
}
