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

namespace Waystone\Workspaces\Engines\Collection;

use Waystone\Workspaces\Engines\Serialization\ObjectDeserializable;

use JsonSerializable;

/**
 * Collection document class
 *
 * This class represents a document, inside a collection.
 */
class CollectionDocument implements JsonSerializable, ObjectDeserializable {

    /**
     * @var string The document identifier
     */
    public $id = '';

    /**
     * Specifies data which should be serialized to JSON
     */
    public function jsonSerialize () : static {
        return $this;
    }

    /**
     * Loads a CollectionDocument instance from a generic object.
     *
     * @param object $data The object to deserialize
     *
     * @return self The deserialized instance
     */
    public static function loadFromObject (object $data) : static {
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
