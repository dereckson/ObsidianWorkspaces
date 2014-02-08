<?php

/**
 *    _, __,  _, _ __, _  _, _, _
 *   / \ |_) (_  | | \ | /_\ |\ |
 *   \ / |_) , ) | |_/ | | | | \|
 *    ~  ~    ~  ~ ~   ~ ~ ~ ~  ~
 *
 * MongoDB colletction iterator class
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
     * @param MongoDBCollection $collection The collection to itrate
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
     * Returns the current element
     */
    public function current () {
        return $this->collection->getDocumentFromArray($this->cursor->current());
    }

    /**
     * Returns the key of the current element
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
     */
    public function valid () {
        return $this->cursor->valid();
    }
}
