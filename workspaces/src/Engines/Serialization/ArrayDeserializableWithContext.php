<?php

namespace Waystone\Workspaces\Engines\Serialization;

interface ArrayDeserializableWithContext {

    /**
     * Loads a specified class instance from an associative array.
     *
     * @param array $data The object to deserialize
     * @param mixed $context The site or application context
     * @return self The deserialized instance
     */
    public static function loadFromArray (array $data, mixed $context) : self;

}
