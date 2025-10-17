<?php

/**
 *    _, __,  _, _ __, _  _, _, _
 *   / \ |_) (_  | | \ | /_\ |\ |
 *   \ / |_) , ) | |_/ | | | | \|
 *    ~  ~    ~  ~ ~   ~ ~ ~ ~  ~
 *
 * MongoDB collection iterator class
 *
 * @package     ObsidianWorkspaces
 * @subpackage  Collection
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @filesource
 */

/**
 * Iterator for MongoDBCollection::getAll()
 */
class MongoDBCollectionIterator implements Iterator {
    /**
     * @var MongoCursor The MongoDB cursor
     */
    private $cursor;

    /**
     * @var MongoDBCollection The collection attached to the current iterator instance
     */
    private $collection;

    /**
     * Initializes a new instance of the MongoDBCollectionIterator object
     *
     * @param MongoDBCollection $collection The collection to iterate
     * @param MongoCursor $cursor The cursor to the results [optional]
     */
    public function __construct (MongoDBCollection $collection, MongoCursor $cursor = null) {
        $this->collection = $collection;
        if ($cursor === null) {
            $this->cursor = $collection->mongoCollection->find();
        } else {
            $this->cursor = $cursor;
        }
    }

    /**
     * Returns a collection document from the current result
     *
     * @return CollectionDocument the current result's document
     */
    public function current () {
        return $this->collection->getDocumentFromArray(
            $this->cursor->current()
        );
    }

    /**
     * Returns the key of the current element
     *
     * @return string the current result's _id
     */
    public function key () {
        return $this->cursor->key();
    }

    /**
     *  Moves forward to next element
     */
    public function next ()  {
        $this->cursor->next();
    }

    /**
     * Rewinds the iterator to the first element
     */
    public function rewind () {
        $this->cursor->rewind();
    }

    /**
     * Checks if current position is valid
     *
     * @return boolean true if the current position is valid ; otherwise, false
     */
    public function valid () {
        return $this->cursor->valid();
    }
}
