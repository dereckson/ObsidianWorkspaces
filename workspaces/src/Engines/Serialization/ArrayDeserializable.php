<?php

namespace Waystone\Workspaces\Engines\Serialization;

interface ArrayDeserializable {

    /**
     * Loads a specified class instance from an associative array.
     *
     * @param array $data The object to deserialize
     * @return self The deserialized instance
     */
    public static function loadFromArray (array $data) : self;

}
