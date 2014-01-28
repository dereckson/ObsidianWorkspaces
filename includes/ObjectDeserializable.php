<?php

/**
 *    _, __,  _, _ __, _  _, _, _
 *   / \ |_) (_  | | \ | /_\ |\ |
 *   \ / |_) , ) | |_/ | | | | \|
 *    ~  ~    ~  ~ ~   ~ ~ ~ ~  ~
 *
 * ObjectDeserializable interface
 *
 * @package     ObsidianWorkspaces
 * @subpackage  Keruald
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @filesource
 *
 */

/**
 * ObjectDeserializable interface
 */
interface ObjectDeserializable {
    /**
     * Loads a specified class instance from a generic object. Typically used to deserialize a JSON document.
     *
     * @param object $data The object to deserialize
     * @return object The deserialized instance
     */
    public static function loadFromObject ($data);
}


/**
 * ObjectDeserializableWithContext interface
 */
interface ObjectDeserializableWithContext {
    /**
     * Loads a specified class instance from a generic object. Typically used to deserialize a JSON document.
     *
     * @param object $data The object to deserialize
     * @param object $context The site or application context
     * @return object The deserialized instance
     */
    public static function loadFromObject ($data, $context);
}