<?php

/**
 *    _, __,  _, _ __, _  _, _, _
 *   / \ |_) (_  | | \ | /_\ |\ |
 *   \ / |_) , ) | |_/ | | | | \|
 *    ~  ~    ~  ~ ~   ~ ~ ~ ~  ~
 *
 * Collection document class
 *
 * @package     ObsidianWorkspaces
 * @subpackage  Collection
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @filesource
 */

 /**
 * Collection document class
 *
 * This class repreesnts a document, inside a collection.
 */
class CollectionDocument implements JsonSerializable, ObjectDeserializable {
    /**
     * @var string The document identifier
     */
    public $id = '';

    /**
     * Specifies data which should be serialized to JSON
     *
     * @return moxed data which can be serialized by json_encode()
     */
    public function jsonSerialize () {
        return $this;
    }

    /**
     * Loads a CollectionDocument instance from a generic object.
     *
     * @param object $data The object to deserialize
     * @return object The deserialized instance
     */
    public static function loadFromObject ($data) {
        $instance = new static;
        foreach ($data as $key => $value) {
            if ($key == '_id') {
                //Kludge to let MongoCollection directly use this method
                //without having to create an intermediary object or to
                //browse each property twice, one to substitute _id, then here.
                $instance->id = $value;
            }
            $instance->$key = $value;
        }
        return $instance;
    }
}
