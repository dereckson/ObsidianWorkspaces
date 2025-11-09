<?php

namespace Waystone\Workspaces\Engines\Framework;

use Keruald\Database\DatabaseEngine;
use Keruald\OmniTools\Collections\HashMap;
use Keruald\OmniTools\DataTypes\Option\Option;

abstract class Repository {

    ///
    /// Properties
    ///

    protected DatabaseEngine $db;

    /**
     * @var HashMap A map of objects already loaded from the database
     */
    protected HashMap $table;

    ///
    /// Constructor
    ///

    public function __construct (DatabaseEngine $db) {
        $this->db = $db;
        $this->table = new HashMap();
    }

    ///
    /// Table
    ///

    protected function lookupInTable (string $property, string $value) : Option {
        return $this->table
            ->filter(fn($item) => $item->$property == $value)
            ->first();
    }

}
