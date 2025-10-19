<?php

/**
 *    _, __,  _, _ __, _  _, _, _
 *   / \ |_) (_  | | \ | /_\ |\ |
 *   \ / |_) , ) | |_/ | | | | \|
 *    ~  ~    ~  ~ ~   ~ ~ ~ ~  ~
 *
 * SQL Collection abstract class
 *
 * @package     ObsidianWorkspaces
 * @subpackage  Collection
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @filesource
 */

/**
 * Abstract class with SQL standard implementation of CRUD features for collections using a SQL database.
 */
abstract class SQLCollection extends Collection {
    /**
     * @var string The SQL collections table
     */
    public $table;

    /**
     * Executes a SQL query
     *
     * @param string $sql The SQL query
     * @return mixed If the query doesn't return any null, nothing. If the query return a row with one field, the scalar value. Otherwise, an associative array, the fields as keys, the row as values.
     */
    public abstract function query ($sql);

    /**
     * Escapes the SQL string
     *
     * @param string $value The value to escape
     * @return string The escaped value
     */
    public abstract function escape ($value);

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

        $collectionId = $this->escape($this->id);
        $documentId = $this->escape($document->id);
        $value = $this->escape(json_encode($document));

        $sql = "INSERT INTO $this->table "
             . "(collection_id, document_id, document_value) VALUES "
             . "('$collectionId', '$documentId', '$value')";
        $this->query($sql);
    }

    /**
     * Deletes a document from the collection
     *
     * @param string $documentId The identifiant of the document to delete
     * @return boolean true if the operation succeeded; otherwise, false.
     */
    public function delete ($documentId) {
        $collectionId = $this->escape($this->id);
        $documentId = $this->escape($documentId);

        $sql = "DELETE FROM $this->table WHERE collection_id = '$collectionId' AND document_id = '$documentId'";
        $this->query($sql);
    }

    /**
     * Determines if a document exists
     *
     * @param CollectionDocument $document The document to check
     * @return boolean true if the document exists; otherwise, false.
     */
    public function exists (CollectionDocument $document) {
        $collectionId = $this->escape($this->id);
        $documentId = $this->escape($document->id);

        $sql = "SELECT count(*) FROM $this->table WHERE collection_id = '$collectionId' AND document_id = '$documentId'";
        return $this->query($sql) == 1;
    }

    /**
     * Updates a document in the collection
     *
     * @param CollectionDocument $document The document to update
     * @return boolean true if the operation succeeded; otherwise, false.
     */
    public function update (CollectionDocument &$document) {
        $collectionId = $this->escape($this->id);
        $documentId = $this->escape($document->id);
        $value = $this->escape(json_encode($document));

        $sql = "UPDATE $this->table SET document_value = '$value' WHERE collection_id = '$collectionId' AND document_id = '$documentId'";
        $this->query($sql);
    }

    /**
     * Gets a document from the collection
     *
     * @param string $documentId The identifiant of the document to get
     * @param CollectionDocument $document The document
     */
    public function get ($documentId){
        $type = $this->documentType;
        if (!class_exists($type)) {
            throw new Exception("Can't create an instance of $type. If the class exists, did you registered a SPL autoloader or updated includes/autoload.php?");
        }

        $collectionId = $this->escape($this->id);
        $documentId = $this->escape($documentId);

        $sql = "SELECT document_value FROM $this->table WHERE collection_id = '$collectionId' AND document_id = '$documentId'";
        $data = $this->query($sql);
        $data = json_decode($data);

        return $type::loadFromObject($data);
    }

    /**
     * Gets a count of the documents in the collection
     *
     * @return int The number of documents
     */
    public function count () {
        $collectionId = $this->escape($this->id);
        $sql = "SELECT count(*) FROM $this->table WHERE collection_id = '$collectionId'";
        return $this->query($sql);
    }
}
