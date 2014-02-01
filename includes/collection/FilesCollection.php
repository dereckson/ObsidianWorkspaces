<?php

/**
 *    _, __,  _, _ __, _  _, _, _
 *   / \ |_) (_  | | \ | /_\ |\ |
 *   \ / |_) , ) | |_/ | | | | \|
 *    ~  ~    ~  ~ ~   ~ ~ ~ ~  ~
 *
 * Files Collection class
 *
 * @package     ObsidianWorkspaces
 * @subpackage  Collection
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @filesource
 */

/**
 * Files Collection class
 *
 * This class repreesnts a collection of documents, stored on the filesystem.
 */
class FilesCollection extends Collection {
    ///
    /// Helper methods
    ///

    /**
     * Gets the path to the folder where the specified collection id is stored.
     *
     * @param string $collectionId The collection identifiant
     * @return string The path to the specified collection folder
     */
    public static function getCollectionPath ($collectionId) {
        global $Config;
        if (!array_key_exists('DocumentStorage', $Config) || !array_key_exists('Path', $Config['DocumentStorage'])) {
            throw new Exception("Configuration parameter missing: \$Config['DocumentStorage']['Path']. Expected value for this parameter is the path to the collections folders.");
        }
        $path = $Config['DocumentStorage']['Path'] . DIRECTORY_SEPARATOR . $collectionId;

        //Ensure directory exists. If not, creates it or throws an exception
        if (!file_exists($path) && !mkdir($path, 0700, true)) {
            throw new Exception("Directory doesn't exist and couldn't be created: $path");
        }

        return $path;
    }

    /**
     * Gets the path to the folder where the specified collection id is stored.
     *
     * @param string $documentId The document identifiant
     * @return string The path to the specified document file
     */
    public function getDocumentPath ($documentId) {
        return static::getCollectionPath($this->id) . DIRECTORY_SEPARATOR . $documentId . '.json';
    }
    
    ///
    /// Constructor
    ///

    /**
     * Initializes a new instance of MongoCollection
     *
     * @param string the database identifiant
     */
    public function __construct ($id) {
        $this->id = $id;
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
        if ($document->id === '') {
            $document->id = uniqid();
        }

        $filename = $this->getDocumentPath($document->id);
        if (file_exists($filename)) {
            throw new Exception("A document with the same identifiant is already in the collection, stored at $filename");
        }

        $data = json_encode($document);

        file_put_contents($filename, $data);
    }

    /**
     * Deletes a document from the collection
     *
     * @param string $documentId The identifiant of the document to delete
     * @return boolean true if the operation succeeded; otherwise, false.
     */
    public function delete ($documentId) {
        $filename = $this->getDocumentPath($documentId);
        unlink($filename);
    }

    /**
     * Determines if a document exists
     *
     * @param CollectionDocument $document The document to check
     * @return boolean true if the document exists; otherwise, false.
     */
    public function exists (CollectionDocument $document) {
        $filename = $this->getDocumentPath($document->id);
        return file_exists($filename);
    }

    /**
     * Updates a document in the collection
     *
     * @param CollectionDocument $document The document to update
     * @return boolean true if the operation succeeded; otherwise, false.
     */
    public function update (CollectionDocument &$document) {
        if ($document->id === '') {
            $document->id = uniqid();
            user_error("An ID were expected for an update operation, but not provided. We'll use $document->id.", E_USER_WARNING);
        }

        $filename = $this->getDocumentPath($document->id);
        if (!file_exists($filename)) {
            user_error("File $filename doesn't exist. The update operation has became an insert one.", E_USER_WARNING);
        }

        $data = json_encode($document);

        file_put_contents($filename, $data);
    }

    /**
     * Gets a document from the collection
     *
     * @param string $documentId The identifiant of the document to get
     * @param CollectionDocument $document The document
     */
    public function get ($documentId) {
        $type = $this->documentType;
        if (!class_exists($type)) {
            throw new Exception("Can't create an instance of $type. If the class exists, did you registered a SPL autoloader or updated includes/autoload.php?");
        }

        $filename = $this->getDocumentPath($documentId);
        $data = json_decode(file_get_contents($filename));
        return $type::loadFromObject($data);
    }

    /**
     * Adds or updates a document in the collection
     *
     * @param CollectionDocument $document The document to set
     * @return boolean true if the operation succeeded; otherwise, false.
     */
    public function set (CollectionDocument &$document) {
        if ($document->id === '') {
            $document->id = uniqid();
        }
        $filename = $this->getDocumentPath($document->id);
        $data = json_encode($document);
        file_put_contents($filename, $data);
    }
}