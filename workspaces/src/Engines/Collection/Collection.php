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

namespace Waystone\Workspaces\Engines\Collection;

use Exception;
use Traversable;

/**
 * Collection class
 *
 * This abstract class represents a collection of documents
 */
abstract class Collection {

    ///
    /// Common properties
    ///

    /**
     * @var string The collection identifiant
     */
    public $id;

    /**
     * @var string the name of the document collection class to use
     */
    public $documentType = 'CollectionDocument';

    ///
    /// Factory
    ///

    /**
     * Loads a new instance of the collection
     *
     * @param string $id The collection identifiant
     * @param string $documentType The type, inheriting from CollectionDocumt
     *     to use for ollection's documents
     *
     * @return Collection The collection, of the type specified in the storage
     *     configuration
     */
    public static function load ($id, $documentType = null) {
        global $Config;

        if (!array_key_exists(('DocumentStorage'), $Config)) {
            throw new Exception("Configuration required parameter missing: DocumentStorage");
        }
        if (!array_key_exists(('Type'), $Config['DocumentStorage'])) {
            throw new Exception("Configuration required parameter missing in DocumentStorage array: Type");
        }
        $collectionClass = $Config['DocumentStorage']['Type'] . 'Collection';
        if (!class_exists($collectionClass)) {
            throw new Exception("Storage class not found: $collectionClass");
        }

        $instance = new $collectionClass($id);
        if ($documentType !== null) {
            $instance->documentType = $documentType;
        }

        return $instance;
    }


    ///
    /// CRUD features
    ///

    /**
     * Adds a document to the collection
     *
     * @param CollectionDocument $document The document to add
     *
     * @return boolean true if the operation succeeded; otherwise, false.
     */
    public abstract function add (CollectionDocument &$document);

    /**
     * Deletes a document from the collection
     *
     * @param string $documentId The identifiant of the document to delete
     *
     * @return boolean true if the operation succeeded; otherwise, false.
     */
    public abstract function delete ($documentId);

    /**
     * Determines if a document exists
     *
     * @param CollectionDocument $document The document to check
     *
     * @return boolean true if the document exists; otherwise, false.
     */
    public abstract function exists (CollectionDocument $document);

    /**
     * Updates a document in the collection
     *
     * @param CollectionDocument $document The document to update
     *
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
     * Gets a count of the documents in the collection
     *
     * @return int The number of documents
     */
    public abstract function count ();

    /**
     * Gets all the documents from the collection
     *
     * @return Traversable An iterator to the documents, each item an instance
     *     of CollectionDocument
     */
    public abstract function getAll ();

    /**
     * Adds or updates a document in the collection
     *
     * @param CollectionDocument $document The document to set
     *
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
