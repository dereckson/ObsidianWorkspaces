<?php

/**
 *    _, __,  _, _ __, _  _, _, _
 *   / \ |_) (_  | | \ | /_\ |\ |
 *   \ / |_) , ) | |_/ | | | | \|
 *    ~  ~    ~  ~ ~   ~ ~ ~ ~  ~
 *
 * Collection class
 *
 * @package     ObsidianWorkspaces
 * @subpackage  Collection
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @filesource
 */

/**
 * Collection class
 *
 * This abstract class repreesnts a collection of documents
 */
abstract class Collection {
    /**
     * @var string The collection identifiant
     */
    public $id;

    /**
     * @var string the name of the document collection class to use
     */
    public $documentType = 'CollectionDocument';

    ///
    /// CRUD features
    ///

    /**
     * Adds a document to the collection
     *
     * @param CollectionDocument $document The document to add
     * @return boolean true if the operation succeeded; otherwise, false.
     */
    public abstract function add (CollectionDocument &$document);

    /**
     * Deletes a document from the collection
     *
     * @param string $documentId The identifiant of the document to delete
     * @return boolean true if the operation succeeded; otherwise, false.
     */
    public abstract function delete ($documentId);

    /**
     * Determines if a document exists
     *
     * @param CollectionDocument $document The document to check
     * @return boolean true if the document exists; otherwise, false.
     */
    public abstract function exists (CollectionDocument $document);

    /**
     * Updates a document in the collection
     *
     * @param CollectionDocument $document The document to update
     * @return boolean true if the operation succeeded; otherwise, false.
     */
    public abstract function update (CollectionDocument &$document);

    /**
     * Gets a document from the collection
     *
     * @param string $documentId The identifiant of the document to get
     * @param CollectionDocument $document The document
     */
    public abstract function get ($documentId);

    /**
     * Adds or updates a document in the collection
     *
     * @param CollectionDocument $document The document to set
     * @return boolean true if the operation succeeded; otherwise, false.
     */
    public function set (CollectionDocument &$document) {
        if ($document->id !== null && $this->exists($document)) {
            return $this->update($document);
        } else {
            return $this->add($document);
        }
    }
}
